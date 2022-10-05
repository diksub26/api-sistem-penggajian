<?php

use App\Http\Controllers\MasterData as MS;
use Illuminate\Support\Facades\Route;

// Employee Position
Route::prefix('employe-position')
->controller(MS\EmployeePositionController::class)
->group(function () {
    Route::get('/', 'index');
    Route::post('/', 'store');
    Route::put('/{employeePosition} ', 'update');
    Route::get('/{employeePosition} ', 'get');
    Route::delete('/{employeePosition} ', 'destroy');
});

// Allowance
Route::prefix('allowance')
->controller(MS\AllowanceController::class)
->group(function () {
    Route::get('/', 'index');
    Route::post('/', 'store');
    Route::put('/{allowance} ', 'update');
    Route::get('/{allowance} ', 'get');
    Route::delete('/{allowance} ', 'destroy');
});