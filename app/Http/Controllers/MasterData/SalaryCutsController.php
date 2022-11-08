<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\MasterData\MasterSalaryCuts;
use Illuminate\Http\Request;

class SalaryCutsController extends Controller
{
    private $_storeValidationRules = [
        'name' => 'required|max:255',
        'amount' => 'required|numeric',
        "type" => 'required|in:amount,percentage'
    ];

    public function index()
    {
        $salaryCuts = MasterSalaryCuts::orderBy('salary_cuts_name', 'asc')->get();
        $this->data = [];

        foreach($salaryCuts as $val) {
            array_push($this->data, $this->_mapResponseForGet($val));
        };

        return $this->sendResponse();        
    }


    public function get(MasterSalaryCuts $salaryCuts)
    {
        $this->data = $this->_mapResponseForGet($salaryCuts);

        return $this->sendResponse();        

    }

    private function _mapResponseForGet(MasterSalaryCuts $salaryCuts)
    {
        return [
            'id'        => $salaryCuts->id,
            'name'      => $salaryCuts->salary_cuts_name,
            'amount'    => $salaryCuts->salary_cuts_amount,
            'type'      => $salaryCuts->salary_cuts_type
        ];
    }
    
    public function store(Request $request)
    {
        $payload = $this->validatingRequest($request, $this->_storeValidationRules);

        if($payload->fails()) return $this->sendResponse();

        $payload = $payload->validated();

        MasterSalaryCuts::create([
            'salary_cuts_name' => $payload['name'],
            'salary_cuts_amount' => $payload['amount'],
            'salary_cuts_type' => $payload['type'],
        ]);

        $this->message = "Data berhasil disimpan.";
        return $this->sendResponse();        
    }

    public function update(MasterSalaryCuts $salaryCuts, Request $request)
    {
        $payload = $this->validatingRequest($request, $this->_storeValidationRules);

        if($payload->fails()) return $this->sendResponse();

        $payload = $payload->validated();

        $salaryCuts->salary_cuts_name = $payload['name'];
        $salaryCuts->salary_cuts_amount = $payload['amount'];
        $salaryCuts->salary_cuts_type = $payload['type'];
        $salaryCuts->save();

        $this->message = "Data berhasil diupdate.";
        return $this->sendResponse();  
    }

    public function destroy(MasterSalaryCuts $salaryCuts)
    {
        $salaryCuts->delete();
        $this->message = "Data berhasil dihapus.";
        return $this->sendResponse();  
    }
}
