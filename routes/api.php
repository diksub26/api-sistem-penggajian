<?php

use App\Http\Controllers\Attendance\AttendanceController;
use App\Http\Controllers\Attendance\LeaveController;
use App\Http\Controllers\Attendance\OvertimeController;
use App\Http\Controllers\Common\AuthController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\Transaction\SalaryController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix("auth")
->controller(AuthController::class)
->group(function() {
    Route::post('/login', 'loginAttempt');
    Route::middleware('auth:sanctum')->get('/me', 'info');
    Route::get('/logout', 'logout');
    Route::middleware('auth:sanctum')->get('/logout-all', 'logoutAllDevice');
});

Route::group(["prefix" => "/master-data", "middleware" => 'auth:sanctum'],__DIR__."/master-data.php");

Route::prefix("employee")
->middleware('auth:sanctum')
->controller(EmployeeController::class)
->group(function() {
    Route::post('/', 'store');
    Route::put('/{employee} ', 'update');
    Route::get('/full-info/{employee} ', 'getFullInfo');
    Route::get('/', 'get');
    Route::get('/manager', 'getManager');
    Route::get('/{employee}', 'getByUUID');
    Route::delete('/{employee}', 'destroy');

    Route::post('/add-allowance/{employee}', 'addAllowance');
    Route::get('/get-allowance/{employee}', 'getAllowance');
    Route::delete('/delete-allowance/{allowance} ', 'destroyAllowance');

    Route::post('/add-salary-cut/{employee}', 'addSalaryCut');
    Route::get('/get-salary-cut/{employee}', 'getSalaryCut');
    Route::delete('/delete-salary-cut/{salaryCut}', 'destroySalaryCut');

});

Route::prefix("leave")
->middleware('auth:sanctum')
->controller(LeaveController::class)
->group(function() {
    Route::post('/', 'create');
    Route::put('/status/{leave} ', 'updateStatus');
    Route::get('/', 'get');
    Route::get('/employee', 'getEmployeeLeave');
    Route::get('/{leave}', 'getById');
});

Route::prefix("overtime")
->middleware('auth:sanctum')
->controller(OvertimeController::class)
->group(function() {
    Route::post('/', 'create');
    Route::put('/status/{overtime} ', 'updateStatus');
    Route::get('/', 'get');
    Route::get('/employee', 'getEmployeOvertime');
    Route::get('/{overtime}', 'getById');
});

Route::prefix("attendance")
->middleware('auth:sanctum')
->controller(AttendanceController::class)
->group(function() {
    Route::post('/import', 'importFormExcel');
    Route::get('/download-template', 'downloadImportTemplate');
    Route::get('/saved-day-of-work', 'getSavedDayOfWork');
    Route::get('/', 'get');
});

Route::prefix("salary")
->middleware('auth:sanctum')
->controller(SalaryController::class)
->group(function() {
    Route::post('/mark-as-transferred/{attendance}', 'markAsTransferred');
    Route::get('/by-attendance/{attendance}', 'getByAttendanceId');
});