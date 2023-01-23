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
            'status' => 'nullable|numeric|min:1|max:3',
        ]);

        if($payload->fails()) return $this->sendResponse();

        $payload = $payload->validated();

        $leave = new Leave();
        $leave->whereMonth('updated_at', $payload["month"]);
        $leave->orderBy('updated_at', 'desc');
        $data = [];
        $month = Carbon::setlocale("id")
        ->create($payload["month"])
        ->format("F");

        foreach($leave->get() as $val) {
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

        return response();
    }
}
