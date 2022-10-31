<?php

use App\Http\Controllers\EmployeeController;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group(["prefix" => "/master-data"],__DIR__."/master-data.php");
Route::prefix("employee")
->controller(EmployeeController::class)
->group(function() {
    Route::post('/', 'store');
    Route::put('/{employee} ', 'update');
    Route::get('/full-info/{employee} ', 'getFullInfo');
    Route::get('/{employee} ', 'get');
    Route::delete('/{employee} ', 'destroy');

    Route::post('/add-allowance/{employee}', 'addAllowance');
    Route::get('/get-allowance/{employee}', 'getAllowance');
    Route::delete('/delete-allowance/{allowance} ', 'destroyAllowance');

    Route::post('/add-salary-cut/{employee}', 'addSalaryCut');
    Route::get('/get-salary-cut/{employee}', 'getSalaryCut');
    Route::delete('/delete-salary-cut/{salaryCut} ', 'destroySalaryCut');
});