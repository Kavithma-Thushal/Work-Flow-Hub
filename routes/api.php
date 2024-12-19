<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployeeController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::group(['middleware' => ['auth:api']], function () {
        Route::prefix('employee')->group(function () {
            Route::post('save', [EmployeeController::class, 'save'])->middleware('permissions:employee-save');
            Route::patch('update/{id}', [EmployeeController::class, 'update'])->middleware('permissions:employee-update');
            Route::delete('delete/{id}', [EmployeeController::class, 'delete'])->middleware('permissions:employee-delete');
            Route::get('getById/{id}', [EmployeeController::class, 'getById'])->middleware('permissions:employee-getById');
        });
    });
});
