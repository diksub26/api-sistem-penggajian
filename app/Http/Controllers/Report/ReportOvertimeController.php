<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Attendance\Leave;
use App\Models\Attendance\Overtime;
use Illuminate\Http\Request;
use  Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportOvertimeController extends Controller
{
    public function getPerMonth(Request $request)
    {
        $payload = $this->validatingRequest($request, [
            'monthYear' => 'required|date_format:Y-m',
        ]);

        $payload = $payload->validated();
        $payloadArr = explode("-", $payload["monthYear"]);
        $overtime =  Overtime::select(DB::raw("SUM(total) as totalCount"), "employee_id")
        ->where("status", 2)
        ->whereMonth('overtime_date', $payloadArr[1])
        ->whereYear('overtime_date', $payloadArr[0])
        ->groupBy("employee_id")
        ->with("employee.position");
        $data = [];

        Carbon::setlocale("id");
        $month = Carbon::create($payload["monthYear"]."-01")
        ->format("F Y");

        foreach($overtime->get() as $val) {
            $data[] = [
                'employeeName' => $val->employee->fullname,
                'position' => $val->employee->position->position_name,
                'total' => $val->totalCount,
            ];
        }
        $this->data["report"] = $data;   
        $this->data["month"] = $month;   

        return $this->sendResponse();
    }
}
