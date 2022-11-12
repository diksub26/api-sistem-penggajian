<?php

namespace App\Http\Controllers\Attendance;

use App\Exports\AttendaceTemplate;
use App\Http\Controllers\Controller;
use App\Models\Attendance\AttendanceSummary;
use App\Models\Attendance\Leave;
use App\Models\Employee;
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
            'month' => 'required|date_format:m',
            'year' => 'required|date_format:Y'
        ]);

        if($payload->fails()) return $this->sendResponse();

        $payload = $payload->validated();

        $lastBar = 1;
        $collectionFromExcel = Excel::toCollection(new GeneralImport, $request->file('fileImport'));
        $error = [];

        foreach ($collectionFromExcel[0] as $val) {
            try {
                DB::beginTransaction();
                    $employe = Employee::where('no_induk', $val['no_induk'])->first();
                    $leaveThisMonth = Leave::where('employee_id', $employe->id)
                    ->whereMonth('created_at', $payload['month'])
                    ->whereYear('created_at', $payload['year'])
                    ->where('status', 2)
                    ->sum('amount');

                    AttendanceSummary::create([
                        'employee_id' => $employe->id,
                        'month' => $payload['month'],
                        'year' => $payload['year'],
                        'attend' => $val['hadir'] - $leaveThisMonth->amount,
                        'leave' => $leaveThisMonth->amount,
                        'permitte' => $val['izin'],
                        'sick' => $val['sakit'],
                        'late' => $val['terlambat'],
                    ]);
                DB::commit();
            } catch (\Throwable $e) {
                DB::rollback();
                $error[] = [
                    'row' =>  $lastBar,
                    'message' => $e->getMessage()
                ];
            }
            $lastBar++;
        }

        $this->message = $lastBar . ' dari'.sizeof($collectionFromExcel[0]) +1 . ' Data berhasil disimpan.';
        $this->error = $error;
        $this->sendResponse();
    }

    public function get(Request $request)
    {
        $payload = $this->validatingRequest($request, [
            'month' => 'required|date_format:m',
            'year' => 'required|date_format:Y'
        ]);

        if($payload->fails()) return $this->sendResponse();

        $payload = $payload->validated();

        $attendances = AttendanceSummary::where('month', $payload['month'])
        ->where('year', $payload['year'])
        ->get();

        $this->data = [];
        foreach ($attendances as $val) {
            $this->data[] = $this->_mapResponse($val);
        }

        return $this->sendResponse();
    }

    private function _mapResponse($data)
    {
        return [
            'id' => $data->id,
            'hadir' => $data->attend,
            'sakit' => $data->sick,
            'izin' => $data->permitte,
            'cuti' => $data->leave,
            'terlambat' => $data->late,
        ];
    }
}
