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
    protected $messages = "success";
    protected $data = null;
    protected $error = null;

    protected function sendErrorResponse($message = '') {
        $this->statusCode = 500;
        $this->messages = $message;

        return $this->sendResponse();
    }

    protected function sendResponse() {
        return response()
        ->json([
            'messages' => $this->messages,
            'data' => $this->data,
            'error' => $this->error,
        ], $this->statusCode );
    }

    public function validatingRequest(Request $request, $rules, $customMessages = []) {
        $validator = Validator::make($request->all(), $rules, $customMessages);

        if ($validator->fails()) {
            $this->error = $validator->errors()->all();
            $this->messages = "VALIDATION_ERROR";
        }
        
        return $validator;
    }

    protected function dbTransaction( $callback ){
        DB::beginTransaction();
        try {
            $callback();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            $this->sendErrorResponse($e->getMessage());
        }
    }
}
