<?php
namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\MasterData\EmployeePosition;
use Illuminate\Http\Request;

class EmployeePositionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $employeePosition = EmployeePosition::orderBy('position_name', 'asc')->get();
        $this->data = [];

        foreach($employeePosition as $val) {
            array_push($this->data, [
                'id'   => $val->id,
                'name' => $val->position_name,
                'salary' => $val->position_salary
            ]);
        };

        return $this->sendResponse();        
    }


    public function get(EmployeePosition $employeePosition)
    {
        $this->data = [
            'id' => $employeePosition->id,
            'name' => $employeePosition->position_name,
            'salary' => $employeePosition->position_salary
        ];

        return $this->sendResponse();        

    }
    
    public function store(Request $request)
    {
        $payload = $this->validatingRequest($request, [
            'name' => 'required|max:255',
            'salary' => 'required|numeric'
        ]);

        if($payload->fails()) return $this->sendResponse();

        $payload = $payload->validated();

        EmployeePosition::create([
            'position_name' => $payload['name'],
            'position_salary' => $payload['salary'],
        ]);

        $this->messages = "Data berhasil disimpan.";
        return $this->sendResponse();        
    }

    public function update(EmployeePosition $employeePosition, Request $request)
    {
        $payload = $this->validatingRequest($request, [
            'name' => 'required|max:255',
            'salary' => 'required|numeric'
        ]);

        if($payload->fails()) return $this->sendResponse();

        $payload = $payload->validated();

        $employeePosition->position_name = $payload['name'];
        $employeePosition->position_salary = $payload['salary'];
        $employeePosition->save();

        $this->messages = "Data berhasil diupdate.";
        return $this->sendResponse();  
    }

    public function destroy(EmployeePosition $employeePosition)
    {
        $employeePosition->delete();
        $this->messages = "Data berhasil dihapus.";
        return $this->sendResponse();  
    }
}
