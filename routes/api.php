<?php

use App\Http\Controllers\api\auth\AuthController;
use App\Http\Controllers\api\doctors\DoctorsController;
use App\Http\Controllers\api\patients\PatientController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('signup', [AuthController::class, 'signup']);
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('getuser', [AuthController::class, 'getUser']);
    });
});


Route::prefix('doctors')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/', [DoctorsController::class, 'index']);
        Route::get('/{id}', [DoctorsController::class, 'singleDoctor']);
        Route::post('/', [DoctorsController::class, 'store']);
        Route::put('/{id}', [DoctorsController::class, 'update']);
        Route::delete('/{id}', [DoctorsController::class, 'destroy']);
    });
});

Route::prefix('patients')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/', [PatientController::class, 'index']);
        Route::get('/{id}', [PatientController::class, 'single']);
        Route::post('/', [PatientController::class, 'store']);
        Route::put('/{id}', [PatientController::class, 'update']);
        Route::delete('/{id}', [PatientController::class, 'destroy']);
    });
});