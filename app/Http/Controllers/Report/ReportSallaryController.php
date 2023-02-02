<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Attendance\AttendanceImportConfig;
use App\Models\Attendance\Leave;
use Illuminate\Http\Request;
use  Carbon\Carbon;

class ReportSallaryController extends Controller
{
    public function getAvailiable()
    {
        $this->data = AttendanceImportConfig::whereHas("attendanceSummary", function($q){
            $q->where("is_final", 1);
        })
        ->get()
        ->transform(function($data) {
            return [
                "id" => $data->id,
                "month" => $this->_getMonthName($data->month),
                "year" => $data->year,
                "dayOfWork" => $data->day_of_work,
                "startPeriod" => $data->start_period,
                "endPeriod" => $data->end_period,
            ];
        });
        return $this->sendResponse();
    }

    public function get(AttendanceImportConfig $attendanceConfig)
    {
        $attendanceConfig->load("attendanceSummary.salary", "attendanceSummary.employee.position");
        $this->data["report"] = $attendanceConfig->attendanceSummary;  
        $this->data["title"] = "Laporan Gaji Karyawan Bulan " . $this->_getMonthName($attendanceConfig->month). " " . $attendanceConfig->year;  
        $this->data["period"] = $attendanceConfig->start_period. " s/d " . $attendanceConfig->end_period;  

        return $this->sendResponse();
    }
}
