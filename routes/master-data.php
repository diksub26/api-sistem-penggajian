<?php

use App\Http\Controllers\MasterData\EmployeePositionController;
use Illuminate\Support\Facades\Route;

// Employee Position
Route::prefix('employe-position')
->controller(EmployeePositionController::class)
->group(function () {
    Route::get('/', 'index');
    Route::post('/', 'store');
    Route::put('/{employeePosition} ', 'update');
    Route::get('/{employeePosition} ', 'get');
    Route::delete('/{employeePosition} ', 'destroy');
});