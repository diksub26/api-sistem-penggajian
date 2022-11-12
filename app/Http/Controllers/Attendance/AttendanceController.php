<?php

namespace App\Http\Controllers\Attendance;

use App\Exports\AttendaceTemplate;
use App\Http\Controllers\Controller;
use App\Imports\GeneralImport;
use App\Models\Attendance\AttendanceImportConfig;
use App\Models\Attendance\AttendanceSummary;
use App\Models\Attendance\Leave;
use App\Models\Employee;
use App\Models\MasterData\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function downloadImportTemplate(Request $request)
    {
        return \Excel::download(new AttendaceTemplate, 'templateImportAbsensi.xlsx');
    }

    public function importFormExcel(Request $request)
    {        
        $payload = $this->validatingRequest($request, [
            'fileImport' => 'required|mimetypes:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel',
            'dayOfWork' => 'required|numeric|max:30|min:10',
        ]);

        if($payload->fails()) return $this->sendResponse();

        $payload = $payload->validated();
        $cutOfDate = Setting::find(3);
        $cutOfDate = $cutOfDate ? $cutOfDate->value : 20;
        $endDate = date("Y-m") . "-" . $cutOfDate;
        $startDate = date("Y-m-d", strtotime("-1 month", strtotime($endDate)));

        $attendanceConfig = AttendanceImportConfig::firstOrNew([
            'month' => date('m'),
            'year'=> date('Y')
        ]);
        $attendanceConfig->day_of_work = $payload['dayOfWork'];
        $attendanceConfig->start_period = $startDate;
        $attendanceConfig->end_period = $endDate;
        $attendanceConfig->save();

        $lastBar = 1;
        $collectionFromExcel = \Excel::toCollection(new GeneralImport, $request->file('fileImport'));
        $error = [];        
        foreach ($collectionFromExcel[0] as $val) {
            try {
                DB::beginTransaction();

                $employe = Employee::where('no_induk', $val['no_induk'])->first();
                if(!$employe) throw new \Exception('Data Karyawan dengan No. Induk : ' . $val['no_induk'] . ' tidak ditemukan.');

                $leaveThisMonth = Leave::where('employee_id', $employe->id)
                ->whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $endDate)
                ->whereYear('created_at', date('Y'))
                ->where('status', 2)
                ->sum('amount');

                $attendanceSummary = AttendanceSummary::firstOrNew([
                    'employee_id' => $employe->id,
                    'attendance_import_config_id' => $attendanceConfig->id
                ]);

                if($attendanceSummary->is_final) throw new \Exception('Gaji Karyawan dengan No. Induk : ' . $val['no_induk'] . ' sudah dibayarkan.');

                $attendanceSummary->attend = $val['hadir'] - $leaveThisMonth;
                $attendanceSummary->leave = $leaveThisMonth;
                $attendanceSummary->permitte = $val['izin'];
                $attendanceSummary->sick = $val['sakit'];
                $attendanceSummary->late = $val['terlambat'];
                $attendanceSummary->save();
                DB::commit();
                $lastBar++;
            } catch (\Throwable $e) {
                DB::rollback();
                $error[] = [
                    'row' =>  $lastBar,
                    'message' => $e->getMessage(),
                ];
            }
        }
        
        $this->message = $lastBar . ' dari '.sizeof($collectionFromExcel[0]) +1 . ' Data berhasil disimpan.';
        $this->error = $error;
        return $this->sendResponse();
    }

    public function get(Request $request)
    {
        $payload = $this->validatingRequest($request, [
            'month' => 'required|date_format:m',
            'year' => 'required|date_format:Y'
        ]);

        if($payload->fails()) return $this->sendResponse();

        $payload = $payload->validated();

        $attendances = AttendanceImportConfig::where('month', $payload['month'])
        ->where('year', $payload['year'])
        ->first();

        $this->data = [];
        if($attendances && !is_null($attendances->attendanceSummary)) {
            foreach ($attendances->attendanceSummary as $val) {
                $this->data[] = [
                    'id' => $val->id,
                    'noInduk' => $val->employee->no_induk,
                    'employeeName' => $val->employee->fullname,
                    'basicSalary' => $val->employee->basic_salary,
                    'attend' => $val->attend,
                    'leave' => $val->leave,
                    'permitte' => $val->permitte,
                    'sick' => $val->sick,
                    'late' => $val->late,
                    'salaryStatus' => config('common.salaryStatus')[1]
                ];
            }
        }

        return $this->sendResponse();
    }

    public function getSavedDayOfWork(Request $request)
    {
        $attendanceConfig = AttendanceImportConfig::select('day_of_work', 'start_period', 'end_period')
        ->where('month', date('m'))
        ->where('year', date('Y'))
        ->first();

        $this->data['day_of_work'] = 0;
        if($attendanceConfig) {
            $this->data['dayOfWork'] = $attendanceConfig->day_of_work;
            $this->data['startPeriod'] = $attendanceConfig->start_period;
            $this->data['endPeriod'] = $attendanceConfig->end_period;
        }

        return $this->sendResponse();
    }
}
