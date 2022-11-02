<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function loginAttempt(Request $request)
    {
        $payload = $this->validatingRequest($request, [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if($payload->fails()) return $this->sendResponse();

        $payload = $payload->validated();
        if(Auth::attempt([ 'email' => $payload['email'], 'password' => $payload['password'], ])) {
            $this->_setData($request);
            return $this->sendResponse();
        }

        return $this->sendErrorResponse("Email atau password salah.");
    }

    public function info(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        $this->_setData($request);
        return $this->sendResponse();
    }

    public function _setData(Request $request)
    {
        $user = $request->user();
        $dataEmployee = $user->employee;
        $this->data = [
            'token' => $user->createToken("devkotes", [$user->role])->plainTextToken,
            'role' => $user->role,
            'email' => $user->email,
            'fullname' => $dataEmployee->fullname,
            'employePosition' => $dataEmployee->position->position_name,
        ];
    }

    public function logout(Request $request)
    {
        if($request->user()) $request->user()->currentAccessToken()->delete();
        $this->message = 'Success.';
        return $this->sendResponse();
    }

    public function logoutAllDevice(Request $request)
    {
        if($request->user()) $request->user()->tokens()->delete();;
        $this->message = 'Success.';
        return $this->sendResponse();
    }
}
