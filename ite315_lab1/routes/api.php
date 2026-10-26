<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    
    Route::apiResource('students', StudentController::class)->only(['index', 'show']);

  
    Route::middleware('role:admin')->group(function () {
        Route::apiResource('students', StudentController::class)->only(['store', 'update', 'destroy']);
    });
});