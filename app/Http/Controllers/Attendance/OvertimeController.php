<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Models\Attendance\Overtime;
use Illuminate\Http\Request;

class OvertimeController extends Controller
{
    public function create(Request $request)
    {
        $payload = $this->validatingRequest($request, [
            'manager' => 'required|exists:App\Models\Employee,id',
            'date' => 'required|date:Y-m-d',
            'startTime' => 'required|date_format:H:i',
            'endTime' => 'required|date_format:H:i',
            'description' => 'required',
            'project' => 'required',
        ]);

        if($payload->fails()) return $this->sendResponse();

        $payload = $payload->validated();
        Overtime::create([
            'employee_id' => $request->user()->employee_id,
            'overtime_date' => $payload['date'],
            'start_time' => $payload['startTime'],
            'end_time' => $payload['endTime'],
            'manager_id' => $payload['manager'],
            'description' => $payload['description'],
            'project' => $payload['project'],
            'status' => 1
        ]); 

        $this->message = "Data berhasil disimpan.";
        return $this->sendResponse(); 
    }

    public function updateStatus(Overtime $overtime, Request $request)
    {
        $payload = $this->validatingRequest($request, [
            'status' => 'required|numeric|min:2|max:3',
        ]);

        if($payload->fails()) return $this->sendResponse();

        $payload = $payload->validated();
        $overtime->status = $payload['status'];
        $overtime->save();

        $this->message = "Data Lembur berhasil diperbaharui.";
        return $this->sendResponse(); 
    }

    public function get(Request $request)
    {
        $payload = $this->validatingRequest($request, [
            'status' => 'nullable|numeric|min:1|max:3',
        ]);

        if($payload->fails()) return $this->sendResponse();

        $payload = $payload->validated();

        $overtime = Overtime::where('employee_id', $request->user()->employee_id);
       
        if(isset($payload['status'])) $overtime->where('status', $payload['status']);

        $overtime->orderBy('updated_at', 'desc');
        $this->data = [];
        foreach($overtime->get() as $val) {
            $this->data[] = [
                'id' => $val->id,
                'date' => $val->overtime_date,
                'time' => $val->start_time ."-". $val->end_time,
                'managerName' => $val->manager->fullname,
                'project' => $val->project,
                'description' => $val->description,
                'status' => config('common.leaveStatus.'. $val->status),
            ];
        }

        return $this->sendResponse();
    }

    public function getEmployeOvertime(Request $request)
    {
        $payload = $this->validatingRequest($request, [
            'status' => 'nullable|numeric|min:1|max:3',
        ]);

        if($payload->fails()) return $this->sendResponse();

        $payload = $payload->validated();

        $overtime = Overtime::where('manager_id', $request->user()->employee_id);
       
        if(isset($payload['status'])) $overtime->where('status', $payload['status']);

        $overtime->orderBy('updated_at', 'desc');
        $this->data = [];
        foreach($overtime->get() as $val) {
            $this->data[] = [
                'id' => $val->id,
                'employeeName' => $val->employee->fullname,
                'date' => $val->overtime_date,
                'time' => $val->start_time ."-". $val->end_time,
                'startTime' => $val->start_time,
                'endTime' => $val->end_time,
                'project' => $val->project,
                'description' => $val->description,
                'status' => config('common.leaveStatus.'. $val->status),
            ];
        }

        return $this->sendResponse();
    }

    public function getById(Overtime $overtime)
    {
        $this->data = [
            'id' => $overtime->id,
            'employeeName' => $overtime->employee->fullname,
            'date' => $overtime->overtime_date,
            'startTime' => $overtime->start_time,
            'endTime' => $overtime->end_time,
            'managerName' => $overtime->manager->fullname,
            'project' => $overtime->project,
            'description' => $overtime->description,
            'status' => config('common.leaveStatus.'. $overtime->status),
        ];

        return $this->sendResponse();
    }
}
