<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Transaction\Allowance;
use App\Models\Transaction\SalaryCut;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    private $_storeValidationRules = [
        'fullname' => 'required',
        'gender' => 'required|in:L,P',
        'placeOfBirth' => 'required',
        'dob' => 'required|date:Y-m-d',
        'address' => 'required',
        'religion' => 'required|in:ISLAM,HINDU,BUDHA,KRISTEN KATOLIK,KRISTEN PROTESTAN,KONGHUCU',
        'noHp' => 'required|max:15',
        'employeePositionId' => 'required|exists:App\Models\MasterData\EmployeePosition,id',
        'assignmentDate' => 'required|date:Y-m-d',
        'division' => 'required',
        'functionalSalary' => 'required|numeric',
        'role' => 'required|in:admin,karyawan,manajer'
    ];

    public function store(Request $request)
    {
        $this->_storeValidationRules['email'] = 'required|email|unique:users,email';
        $this->_storeValidationRules['noInduk'] = 'required|max:20|unique:App\Models\Employee,no_induk';

        $payload = $this->validatingRequest($request, $this->_storeValidationRules);

        if($payload->fails()) return $this->sendResponse();

        $payload = $payload->validated();

        return $this->dbTransaction( function() use($payload) {
            $employe = Employee::create([
                'no_induk' => $payload['noInduk'],
                'fullname' => $payload['fullname'],
                'gender' => $payload['gender'],
                'place_of_birth' => $payload['placeOfBirth'],
                'dob' => $payload['dob'],
                'address' => $payload['address'],
                'religion' => $payload['religion'],
                'no_hp' => $payload['noHp'],
                'employee_position_id' => $payload['employeePositionId'],
                'assignment_date' => $payload['assignmentDate'],
                'division' => $payload['division'],
                'functional_salary' => $payload['functionalSalary'],
            ]);
            $employe->user()->create([
                'employee_id' => $employe->id,
                'email' => $payload['email'],
                'role' => $payload['role'],
                'password' => Hash::make($payload['noInduk']),
            ]);
            $this->message = "Data berhasil disimpan.";
            return $this->sendResponse();        
        });
    }

    public function update(Employee $employee, Request $request)
    {
        $this->_storeValidationRules['email'] = 'required|email';
        $this->_storeValidationRules['noInduk'] = 'required|max:20';
        $payload = $this->validatingRequest($request, $this->_storeValidationRules);

        if($payload->fails()) return $this->sendResponse();

        $payload = $payload->validated();

        $checkEmail = User::select('id')->where('email', $payload['email'])->first();
        if($checkEmail) {
            if($employee->user->id != $checkEmail->id) return $this->sendErrorResponse("Email sudah terpakai.");
        }

        $checkNoInduk = Employee::select('id')->where('no_induk', $payload['noInduk'])->first();
        if($checkNoInduk) {
            if($employee->id != $checkNoInduk->id) return $this->sendErrorResponse("No Induk sudah terpakai.");
        }

        return $this->dbTransaction( function() use($payload, $employee) {
            $employee->no_induk = $payload['noInduk'];
            $employee->fullname = $payload['fullname'];
            $employee->gender = $payload['gender'];
            $employee->place_of_birth = $payload['placeOfBirth'];
            $employee->dob = $payload['dob'];
            $employee->address = $payload['address'];
            $employee->religion = $payload['religion'];
            $employee->no_hp = $payload['noHp'];
            $employee->employee_position_id = $payload['employeePositionId'];
            $employee->assignment_date = $payload['assignmentDate'];
            $employee->division = $payload['division'];
            $employee->functional_salary = $payload['functionalSalary'];
            $employee->save();
    
            $employee->user->email = $payload['email'];
            $employee->user->role = $payload['role'];
            $employee->user->save();
            $this->message = "Data berhasil diperbaharui.";
            return $this->sendResponse();    
        });
    }

    public function get(Request $request)
    {
        $employes = Employee::with('user')
        ->with('position')
        ->orderBy('updated_at', 'desc')->get();

        foreach ($employes as $employee) {
            $this->data[]= [
                'id' => $employee->id,
                'noInduk' => $employee->no_induk,
                'fullname' => $employee->fullname,
                'gender' => $employee->gender,
                'placeOfBirth' => $employee->place_of_birth,
                'dob' => $employee->dob,
                'address' => $employee->address,
                'religion' => $employee->religion,
                'noHp' => $employee->no_hp,
                'assignmentDate' => $employee->assignment_date,
                'division' => $employee->division,
                'functionalSalary' => $employee->functional_salary,
                'email' => $employee->user->email,
                'role' => $employee->user->role,
                'employeePositionId' => $employee->position->id,
                'employeePosition' => $employee->position->position_name,
            ];
        }

        return $this->sendResponse(); 
    }

    public function getByUUID(Employee $employee)
    {
        $this->data = [
            'id' => $employee->id,
            'noInduk' => $employee->no_induk,
            'fullname' => $employee->fullname,
            'gender' => $employee->gender,
            'placeOfBirth' => $employee->place_of_birth,
            'dob' => $employee->dob,
            'address' => $employee->address,
            'religion' => $employee->religion,
            'noHp' => $employee->no_hp,
            'assignmentDate' => $employee->assignment_date,
            'division' => $employee->division,
            'functionalSalary' => $employee->functional_salary,
            'email' => $employee->user->email,
            'role' => $employee->user->role,
            'employeePositionId' => $employee->position->id,
            'employeePosition' => $employee->position->position_name,
        ];

        return $this->sendResponse(); 
    }

    public function getFullInfo(Employee $employee)
    {
        $this->data = [
            'employee' => [
                'id' => $employee->id,
                'noInduk' => $employee->no_induk,
                'fullname' => $employee->fullname,
                'gender' => $employee->gender,
                'placeOfBirth' => $employee->place_of_birth,
                'dob' => $employee->dob,
                'address' => $employee->address,
                'religion' => $employee->religion,
                'noHp' => $employee->no_hp,
                'assignmentDate' => $employee->assignment_date,
                'division' => $employee->division,
                'functionalSalary' => $employee->functional_salary,
            ],
            'user' => [
                'email' => $employee->user->email,
                'role' => $employee->user->role,
            ],
            'employeePosition' => [
                'positionName' => $employee->position->position_name,
                'positionSalary' => $employee->position->position_salary,
            ]
        ];


        if(sizeof($employee->allowance) > 0) {
            $allowance = [];
            foreach($employee->allowance as $val) {
                $allowance[] = [
                    'name' => $val->masterAllowance->allowance_name,
                    'amount' => $val->masterAllowance->allowance_amount,
                ];
            }

            $this->data['allowance'] = $allowance;
        }

        if(sizeof($employee->salaryCut) > 0) {
            $salaryCut = [];
            foreach($employee->salaryCut as $val) {
                $salaryCut[] = [
                    'name' => $val->masterSalaryCut->salary_cuts_name,
                    'amount' => $val->masterSalaryCut->salary_cuts_amount,
                    'type' => $val->masterSalaryCut->salary_cuts_type,
                ];
            }

            $this->data['salaryCut'] = $salaryCut;
        }
        return $this->sendResponse(); 
    }

    public function destroy(Employee $employee)
    {
        return $this->dbTransaction( function() use ($employee) {
            $employee->delete();
            $this->message = "Data berhasil dihapus.";
            return $this->sendResponse();  
        });
    }

    public function addAllowance(Employee $employee, Request $request)
    {
        $payload = $this->validatingRequest($request, [
            'msAllowanceId' => 'required|exists:App\Models\MasterData\MasterAllowance,id'
        ]);

        if($payload->fails()) return $this->sendResponse();

        $payload = $payload->validated();
        $employee->allowance()->create(
            ['master_allowance_id' => $payload['msAllowanceId'] ]
        );

        $this->message = 'Tunjangan berhasil ditambahkan.';
        return $this->sendResponse();
    }

    public function getAllowance(Employee $employee)
    {
        $allowances = $employee->allowance ?? [];

        foreach ($allowances as $value) {
            $this->data[] = [
                'id'  => $value->id ?? '',
                'name' => $value->masterAllowance->allowance_name ?? '',
                'amount' => $value->masterAllowance->allowance_amount ?? '',
            ];
            
        }
        return $this->sendResponse();
    }

    public function destroyAllowance(Allowance $allowance)
    {
        $allowance->delete();
        $this->message = 'Tunjangan berhasil dihapus.';
        return $this->sendResponse();
    }

    public function addSalaryCut(Employee $employee, Request $request)
    {
        $payload = $this->validatingRequest($request, [
            'msSalaryCutId' => 'required|exists:App\Models\MasterData\MasterSalaryCuts,id'
        ]);

        if($payload->fails()) return $this->sendResponse();

        $payload = $payload->validated();
        $employee->salaryCut()->create(
            ['master_salary_cut_id' => $payload['msSalaryCutId'] ]
        );

        $this->message = 'Potongan Gaji berhasil ditambahkan.';
        return $this->sendResponse();
    }

    public function getSalaryCut(Employee $employee)
    {
        $salaryCuts = $employee->salaryCut ?? [];

        foreach ($salaryCuts as $value) {
            $this->data[] = [
                'id'  => $value->id ?? '',
                'name' => $value->masterSalaryCut->salary_cuts_name ?? '',
                'amount' => $value->masterSalaryCut->salary_cuts_amount ?? '',
                'type' => $value->masterSalaryCut->salary_cuts_type ?? '',
            ];
            
        }
        return $this->sendResponse();
    }

    public function destroySalaryCut(SalaryCut $salaryCut)
    {
        $salaryCut->delete();
        $this->message = 'Potongan Gaji berhasil dihapus.';
        return $this->sendResponse();
    }

    public function getManager(Request $request)
    {
        $employes = Employee::whereHas('user', function($q){
            $q->where('role', 'manajer');
        })
        ->orderBy('fullname', 'desc')->get();

        foreach ($employes as $employee) {
            $this->data[]= [
                'value' => $employee->id,
                'text' => $employee->no_induk . '-' .$employee->fullname . '-' .$employee->division,
            ];
        }

        return $this->sendResponse(); 
    }
}
