<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Validator;
use DB;
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $statusCode = 200;
    protected $message = "success";
    protected $data = null;
    protected $error = null;

    protected function sendErrorResponse($message = '') {
        $this->statusCode = 500;
        $this->message = $message;

        return $this->sendResponse();
    }

    protected function sendResponse() {
        return response()
        ->json([
            'message' => $this->message,
            'data' => $this->data,
            'error' => $this->error,
        ], $this->statusCode );
    }

    public function validatingRequest(Request $request, $rules, $custommessage = []) {
        $validator = Validator::make($request->all(), $rules, $custommessage);

        if ($validator->fails()) {
            $this->error = $validator->errors()->all();
            $this->message = implode(',', $validator->errors()->all());
            $this->statusCode = 422;
        }
        
        return $validator;
    }

    protected function dbTransaction( $callback ){
        DB::beginTransaction();
        try {
            $val = $callback();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendErrorResponse($e->getMessage());
        }

        if(isset($val)) return $val;
    }
}
