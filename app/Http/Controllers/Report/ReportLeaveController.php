<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Attendance\Leave;
use Illuminate\Http\Request;
use  Carbon\Carbon;

class ReportLeaveController extends Controller
{
    public function getPerMonth(Request $request)
    {
        $payload = $this->validatingRequest($request, [
            'monthYear' => 'required|date_format:Y-m',
        ]);

        $payload = $payload->validated();
        $payloadArr = explode("-", $payload["monthYear"]);
        $leave = Leave::whereMonth('updated_at', $payloadArr[1])
        ->whereYear('updated_at', $payloadArr[0])
        ->orderBy('updated_at', 'desc')
        ->get();
        $data = [];
        
        Carbon::setlocale("id");
        $month = Carbon::create($payload["monthYear"]."-01")
        ->format("F Y");

        foreach($leave as $val) {
            $data[] = [
                'id' => $val->id,
                'managerName' => $val->manager->fullname,
                'employeeName' => $val->employee->fullname,
                'reason' => $val->reason,
                'status' => config('common.leaveStatus.'. $val->status),
                'type' => config('common.leaveType.'. $val->type),
                'startDate' => $val->start_leave,
                'endDate' => $val->end_leave,
                'amount' => $val->amount,
            ];
        }
        $this->data["report"] = $data;   
        $this->data["month"] = $month;   

        return $this->sendResponse();
    }
}
