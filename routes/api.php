<?php

use App\Http\Controllers\api\auth\AuthController;
use App\Http\Controllers\api\doctors\DoctorsController;
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
        Route::post('/', [DoctorsController::class, 'store']);
        Route::put('/{id}', [DoctorsController::class, 'update']);
        Route::delete('/{id}', [DoctorsController::class, 'destroy']);
    });
});