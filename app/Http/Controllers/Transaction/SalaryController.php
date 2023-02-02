<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Attendance\AttendanceImportConfig;
use App\Models\Attendance\AttendanceSummary;
use App\Models\Attendance\Overtime;
use App\Models\MasterData\Setting;
use App\Models\Transaction\Salary;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalaryController extends Controller
{
    public function getByAttendanceId(AttendanceSummary $attendance)
    {
        $employee = $attendance->employee;
        $importConfig = $attendance->importConfig;
        $allowance = [];
        $salaryCuts = [];
        $totalAllowance = 0;
        $totalSalaryCut = 0;
        $total = 0;
        $status = config('common.salaryStatus')['1'];
        $basicSalary = $employee->basic_salary;

        /** get allowance and salary cut*/
        if(!$attendance->is_final) {
            $_allowanceFromEmployee = $employee->allowance;
            foreach ($_allowanceFromEmployee as $val) {
                $allowance[] = [
                    'name' => $val->masterAllowance->allowance_name,
                    'amount' => $val->masterAllowance->allowance_amount,
                ];
                $totalAllowance = $totalAllowance + $val->masterAllowance->allowance_amount;
            }

            /** add job position salary */
            $allowance[] = [
                'name' => "Tunjangan Jabatan",
                'amount' => $employee->position->position_salary,
            ];
            $totalAllowance = $totalAllowance + $employee->position->position_salary;

            /** Calculate Overtime */
            $feeOvertime = Setting::where('id', 2)->first();
            $feeOvertime = $feeOvertime->value;
            $overtime = Overtime::whereDate('overtime_date', '>=', $importConfig->start_period)
            ->whereDate('overtime_date', '<=', $importConfig->end_period)
            ->where('status', 2)
            ->sum('total');    

            $overtime = $overtime * $feeOvertime;
            $allowance[] = [
                'name' => "Tunjangan Lembur",
                'amount' => $overtime,
            ];
            $totalAllowance = $totalAllowance + $overtime;

            $_salaryCutFromEmployee = $employee->salaryCut;
            foreach ($_salaryCutFromEmployee as $val) {
                $amount = $val->masterSalaryCut->salary_cuts_amount;
                if($val->masterSalaryCut->salary_cuts_type == 'percentage') $amount = ($employee->basic_salary * $amount) / 100;

                $salaryCuts[] = [
                    'name' => $val->masterSalaryCut->salary_cuts_name,
                    'amount' => $amount,
                ];
                $totalSalaryCut = $totalSalaryCut + $amount;
            }

            /** Calculate late penalty */
            $latePenaltyFee = Setting::where('id', 1)->first();
            $latePenaltyFee = $latePenaltyFee->value;
            $late = $attendance->late * $latePenaltyFee;
            $salaryCuts[] = [
                'name' => "Denda Kesiangan",
                'amount' => $late,
            ];
            $totalSalaryCut = $totalSalaryCut + $late;
        }else{
            $_allowanceFromSalary = $attendance->salary->allowances;
            foreach($_allowanceFromSalary as $val) {
                $allowance[] = [
                    'name' => $val->name,
                    'amount' => $val->amount,
                ];
            }
            $totalAllowance = $attendance->salary->total_allowances;

            $_salaryCutFormSalary = $attendance->salary->salaryCut;
            foreach($_salaryCutFormSalary as $val) {
                $salaryCuts[] = [
                    'name' => $val->name,
                    'amount' => $val->amount,
                ];
            }
            $totalSalaryCut = $attendance->salary->total_salary_cuts;
            $basicSalary = $attendance->salary->basic_salary;
            $status = config('common.salaryStatus')[$attendance->salary->status];
        }

        $total = $basicSalary + $totalAllowance;
        $salary = $total - $totalSalaryCut;
        $this->data = [
            'id' => $attendance->id,
            'period' => $importConfig->getPeriod(),
            'employeeName' => $employee->fullname,
            'noInduk' => $employee->no_induk,
            'division' => $employee->division,
            'employeePosition' => $employee->position->position_name,
            'assignmentDate' => $employee->assignment_date,
            'bankAccNo' => $employee->bank_acc_no,
            'dayOfWork' => $importConfig->day_of_work,
            'sick' => $attendance->sick,
            'permitte' => $attendance->permitte,
            'leave' => $attendance->leave,
            'late' => $attendance->late,
            'basicSalary' => $employee->basic_salary,
            'allowances' => $allowance,
            'salaryCuts' => $salaryCuts,
            'total' => $total,
            'totalSalaryCut' => $totalSalaryCut,
            'salary' => $salary,
            'status' => $status,
        ];

        return $this->sendResponse();
    }

    public function markAsTransferred(AttendanceSummary $attendance)
    {
        try {
            DB::beginTransaction();
            if($attendance->is_final) throw new Exception('Gaji karywan sudah di transfer.');

            $employee = $attendance->employee;
            $allowance = $employee->allowance;
            $salaryCut = $employee->salaryCut;
            $importConfig = $attendance->importConfig;

            $salary = Salary::create([
                'attendance_summary_id' => $attendance->id,
                'employee_id' => $employee->id,
                'basic_salary' => $employee->basic_salary,
                'status' => config('common.salaryStatusCode')["Sudah ditransfer"]
            ]);
            
            $totalAllowance = 0;
            foreach ($allowance as $val) {
                $salary->allowances()->create([
                    'name' => $val->masterAllowance->allowance_name,
                    'amount' => $val->masterAllowance->allowance_amount,
                ]);
                $totalAllowance = $totalAllowance + $val->masterAllowance->allowance_amount;
            }

            /** add job position salary */
            $salary->allowances()->create([
                'name' => 'Tunjangan Jabatan',
                'amount' => $employee->position->position_salary,
            ]);
            $totalAllowance = $totalAllowance + $employee->position->position_salary;
            
            /** Calculate Overtime */
            $feeOvertime = Setting::where('id', 2)->first();
            $feeOvertime = $feeOvertime->value;
            $overtime = Overtime::whereDate('overtime_date', '>=', $importConfig->start_period)
            ->whereDate('overtime_date', '<=', $importConfig->end_period)
            ->where('status', config('common.leaveStatus')['2'])
            ->sum('total');

            $overtime = $overtime * $feeOvertime;
            $salary->allowances()->create([
                'name' => 'Tunjangan Lembur',
                'amount' => $overtime,
            ]);
            $totalAllowance = $totalAllowance + $overtime;

            $totalSalaryCut = 0;
            foreach ($salaryCut as $val) {
                $amount = $val->masterSalaryCut->salary_cuts_amount;
                if($val->masterSalaryCut->salary_cuts_type == 'percentage') $amount = ($employee->basic_salary * $amount) / 100;

                $salary->salaryCut()->create([
                    'name' => $val->masterSalaryCut->salary_cuts_name,
                    'amount' => $amount,
                ]);
                $totalSalaryCut = $totalSalaryCut + $amount;
            }

            /** Calculate late penalty */
            $latePenaltyFee = Setting::where('id', 1)->first();
            $latePenaltyFee = $latePenaltyFee->value;
            $late = $attendance->late * $latePenaltyFee;
            $salary->salaryCut()->create([
                'name' => "Denda Kesiangan",
                'amount' => $late,
            ]);

            $salary->total_allowances = $totalAllowance;
            $salary->total_salary_cuts = $totalSalaryCut;
            $salary->save();

            $attendance->is_final = true;
            $attendance->save();

            DB::commit();
            $this->message = 'Data Berhasil diperbahrui.';
            return $this->sendResponse();
        } catch (\Throwable $e) {
            DB::rollback();
            return $this->sendErrorResponse($e->getMessage());
        }
    }

    public function getSlip()
    {
        $this->data = AttendanceImportConfig::whereHas("attendanceSummaryByEmployeeId", function($q) {
            $q->where("is_final", 1);
        })
        ->with("attendanceSummaryByEmployeeId")
        ->get()
        ->transform(function($data) {
            return [
                "id" => $data->attendanceSummaryByEmployeeId->id,
                "month" => $this->_getMonthName($data->month),
                "year" => $data->year,
                "dayOfWork" => $data->day_of_work,
                "startPeriod" => $data->start_period,
                "endPeriod" => $data->end_period,
            ];
        });
        return $this->sendResponse();
    }
}
