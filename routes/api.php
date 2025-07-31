<?php

use App\Http\Controllers\api\appoinments\AppoinmentController;
use App\Http\Controllers\api\auth\AuthController;
use App\Http\Controllers\api\doctors\DoctorsController;
use App\Http\Controllers\api\patients\PatientController;
use App\Http\Controllers\api\Scedule\SceduleController;
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

Route::prefix('doctorName')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
    Route::get('/', [DoctorsController::class, 'DoctorsName']);
    });
});

Route::prefix('patients')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/', [PatientController::class, 'index']);
        Route::get('/{id}', [PatientController::class, 'single']);
        Route::post('/', [PatientController::class, 'store']);
        Route::put('/{id}', [PatientController::class, 'update']);
        Route::delete('/{id}', [PatientController::class, 'destroy']);
        Route::get('/search/{phone}', [PatientController::class, 'getUserByPhone']);
    });
});

Route::prefix('scedule')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
    Route::get('/', [SceduleController::class, 'index']);
    Route::get('/doctor/{doctorId}', [SceduleController::class, 'getDoctorSchedules']);
    Route::get('/doctor/{doctorId}/day/{day}', [SceduleController::class, 'getDoctorDaySchedules']);
    Route::post('/store', [SceduleController::class, 'store']);
    Route::get('/slots/doctorId/{doctor_id}/day/{day}', [SceduleController::class, 'getSlotsByDay']);
    Route::delete('{id}', [SceduleController::class, 'destroySlot']); 
     Route::delete('delete/{id}', [SceduleController::class, 'destroyScedule']);
});
});

Route::prefix('appoinment')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/create', [AppoinmentController::class, 'store']);
    });
});