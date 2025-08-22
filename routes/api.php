<?php

use App\Http\Controllers\api\appoinments\AppoinmentController;
use App\Http\Controllers\api\auth\AuthController;
use App\Http\Controllers\api\Bills\BillController;
use App\Http\Controllers\api\doctors\DoctorsController;
use App\Http\Controllers\api\employees\EmployeeController;
use App\Http\Controllers\api\groupe\GroupeController;
use App\Http\Controllers\api\patients\PatientController;
use App\Http\Controllers\api\References\ReferencesController;
use App\Http\Controllers\api\Scedule\SceduleController;
use App\Http\Controllers\Api\Services\ServiceController;
use App\Http\Controllers\api\Test\TestController;
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
        Route::patch('/{id}', [PatientController::class, 'update']);
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
     Route::patch('slot/status', [SceduleController::class, 'updateStatus']); 
});
});

Route::prefix('appoinment')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/create', [AppoinmentController::class, 'store']);
        Route::post('/save', [AppoinmentController::class, 'storeWithUser']);
        Route::patch('/payAppoinment', [AppoinmentController::class, 'payAppointment']);
        Route::get('/', [AppoinmentController::class, 'index']);
        Route::get('/{id}', [AppoinmentController::class, 'appoinmentById']);
        Route::get('/doctor/{id}', [AppoinmentController::class, 'AppoinmentsByDoctorId']);
    });
});

Route::prefix('test')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/', [TestController::class, 'index']);
        Route::post('/', [TestController::class, 'store']);
        Route::delete('/{id}', [TestController::class, 'destroy']);
    });
});

Route::prefix('groupe')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/', [GroupeController::class, 'store']);
        Route::get('/', [GroupeController::class, 'index']);
    });
});

Route::prefix('references')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/', [ReferencesController::class, 'store']);
        Route::get('/', [ReferencesController::class, 'index']);
        Route::get('/{id}', [ReferencesController::class, 'single']);
        Route::patch('/{id}', [ReferencesController::class, 'update']);
        Route::delete('/{id}', [ReferencesController::class, 'destroy']);
    });
});

Route::prefix('services')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/', [ServiceController::class, 'store']);
        Route::get('/', [ServiceController::class, 'index']);
        Route::get('/{id}', [ServiceController::class, 'show']);
        Route::patch('/{id}', [ServiceController::class, 'update']);
        Route::delete('/{id}', [ServiceController::class, 'destroy']);
    });
});

Route::prefix('bills')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/', [BillController::class, 'store']);
        Route::get('/', [BillController::class, 'index']);
        Route::get('/duebills', [BillController::class, 'Dueindex']);
        Route::get('/reports', [BillController::class, 'reports']);
        Route::get('/billData/{patientId}', [BillController::class, 'gatBillData']);
    });
});

Route::prefix('employees')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/', [EmployeeController::class, 'store']);
        Route::get('/', [EmployeeController::class, 'index']);
        Route::patch('/', [EmployeeController::class, 'update']);
        Route::delete('/{id}', [EmployeeController::class, 'destroy']);
    });
});
