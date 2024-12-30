<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployeeLeaveController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::group(['middleware' => ['auth:api']], function () {
        Route::prefix('employee')->group(function () {
            Route::post('store', [EmployeeController::class, 'store'])->middleware('permissions:employee-store');
            Route::patch('update/{id}', [EmployeeController::class, 'update'])->middleware('permissions:employee-update');
            Route::delete('delete/{id}', [EmployeeController::class, 'delete'])->middleware('permissions:employee-delete');
            Route::get('getById/{id}', [EmployeeController::class, 'getById'])->middleware('permissions:employee-getById');
            Route::get('getAll', [EmployeeController::class, 'getAll'])->middleware('permissions:employee-getAll');
        });

        Route::prefix('leave')->group(function () {
            Route::post('store', [EmployeeLeaveController::class, 'store'])->middleware('permissions:leave-store');
            Route::get('getById/{id}', [EmployeeLeaveController::class, 'getById'])->middleware('permissions:leave-getById');
        });
    });
});
