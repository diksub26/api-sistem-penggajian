<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Models\Attendance\Leave;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    public function create(Request $request)
    {
        $payload = $this->validatingRequest($request, [
            'manager' => 'required|exists:App\Models\Employee,id',
            'startDate' => 'required|date:Y-m-d',
            'endDate' => 'required|date:Y-m-d',
            'reason' => 'required',
            'type' => 'required|min:1|max:5',
        ]);

        if($payload->fails()) return $this->sendResponse();

        $payload = $payload->validated();
        Leave::create([
            'employee_id' => $request->user()->employee_id,
            'start_leave' => $payload['startDate'],
            'end_leave' => $payload['endDate'],
            'manager_id' => $payload['manager'],
            'reason' => $payload['reason'],
            'type' => $payload['type'],
            'status' => 1
        ]); 

        $this->message = "Data berhasil disimpan.";
        return $this->sendResponse(); 
    }

    public function updateStatus(Leave $leave, Request $request)
    {
        $payload = $this->validatingRequest($request, [
            'status' => 'required|numeric|min:2|max:3',
        ]);

        if($payload->fails()) return $this->sendResponse();

        $payload = $payload->validated();
        $leave->status = $payload['status'];
        $leave->save();

        $this->message = "Data Cuti berhasil diperbaharui.";
        return $this->sendResponse(); 
    }

    public function get(Request $request)
    {
        $payload = $this->validatingRequest($request, [
            'status' => 'nullable|numeric|min:1|max:3',
        ]);

        if($payload->fails()) return $this->sendResponse();

        $payload = $payload->validated();

        $leave = new Leave();
        $role = $request->user()->role;
        if($role == 'karyawan') $leave->where('employee_id', $request->user()->employee_id);
        else $leave->where('manager_id', $request->user()->employee_id);
       
        if(isset($payload['status'])) $leave->where('status', $payload['status']);

        $leave->orderBy('updated_at', 'desc');
        $this->data = [];
        foreach($leave->get() as $val) {
            $this->data[] = [
                'id' => $val->id,
                'employeeName' => $val->employee->fullname,
                'managerName' => $val->manager->fullname,
                'reason' => $val->reason,
                'status' => config('common.leaveStatus.'. $val->status),
                'type' => config('common.leaveType.'. $val->type),
                'startDate' => $val->start_leave,
                'endDate' => $val->end_leave,
            ];
        }

        return $this->sendResponse();
    }

    public function getById(Leave $leave)
    {
        $this->data = [
            'id' => $leave->id,
            'employeeName' => $leave->employee->fullname,
            'managerName' => $leave->manager_id,
            'reason' => $leave->reason,
            'status' => config('common.leaveStatus.'. $leave->status),
            'type' => config('common.leaveType.'. $leave->type),
            'startDate' => $leave->start_leave,
            'endDate' => $leave->end_leave,
        ];

        return $this->sendResponse();
    }
}
