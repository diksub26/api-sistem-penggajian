<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\MasterData\MasterAllowance;
use Illuminate\Http\Request;

class AllowanceController extends Controller
{
    private $_storeValidationRules = [
        'name' => 'required|max:255',
        'amount' => 'required|numeric'
    ];

    public function index()
    {
        $allowance = MasterAllowance::orderBy('allowance_name', 'asc')->get();
        $this->data = [];

        foreach($allowance as $val) {
            array_push($this->data, $this->_mapResponseForGet($val));
        };

        return $this->sendResponse();        
    }


    public function get(MasterAllowance $allowance)
    {
        $this->data = $this->_mapResponseForGet($allowance);

        return $this->sendResponse();        

    }

    private function _mapResponseForGet(MasterAllowance $allowance)
    {
        return [
            'id'        => $allowance->id,
            'name'      => $allowance->allowance_name,
            'amount'    => $allowance->allowance_amount
        ];
    }
    
    public function store(Request $request)
    {
        $payload = $this->validatingRequest($request, $this->_storeValidationRules);

        if($payload->fails()) return $this->sendResponse();

        $payload = $payload->validated();

        MasterAllowance::create([
            'allowance_name' => $payload['name'],
            'allowance_amount' => $payload['amount'],
        ]);

        $this->message = "Data berhasil disimpan.";
        return $this->sendResponse();        
    }

    public function update(MasterAllowance $allowance, Request $request)
    {
        $payload = $this->validatingRequest($request, $this->_storeValidationRules);

        if($payload->fails()) return $this->sendResponse();

        $payload = $payload->validated();

        $allowance->allowance_name = $payload['name'];
        $allowance->allowance_amount = $payload['amount'];
        $allowance->save();

        $this->message = "Data berhasil diupdate.";
        return $this->sendResponse();  
    }

    public function destroy(MasterAllowance $allowance)
    {
        $allowance->delete();
        $this->message = "Data berhasil dihapus.";
        return $this->sendResponse();  
    }
}
