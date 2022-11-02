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
        $now = new \DateTime('now');
        $diffInHours = $now->diff($request->user()->currentAccessToken()->created_at)->h;
        $token = str_replace("Bearer ", '', $request->header('Authorization'));

        if($diffInHours >= 12 ) {
            $request->user()->currentAccessToken()->delete();
            $token = null;
        }
        $this->_setData($request, $token);
        return $this->sendResponse();
    }

    public function _setData(Request $request, $token = null)
    {
        $user = $request->user();
        $dataEmployee = $user->employee;
        $this->data = [
            'token' => $token == null ? $user->createToken("devkotes", [$user->role])->plainTextToken : $token,
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
