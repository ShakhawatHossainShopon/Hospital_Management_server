<?php

use App\Http\Controllers\api\Accounts\AccountsController;
use App\Http\Controllers\api\appoinments\AppoinmentController;
use App\Http\Controllers\api\auth\AuthController;
use App\Http\Controllers\api\Bills\BillController;
use App\Http\Controllers\api\doctors\DoctorsController;
use App\Http\Controllers\api\employees\EmployeeController;
use App\Http\Controllers\api\groupe\GroupeController;
use App\Http\Controllers\api\medicine\medicineController;
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
        Route::get('/admin', [TestController::class, 'AdminIndex']);
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
        Route::get('/admin', [ServiceController::class, 'Adminindex']);
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
        Route::get('/admin', [BillController::class, 'AdminIndex']);
        Route::get('/duebills', [BillController::class, 'Dueindex']);
        Route::get('/admin/duebills', [BillController::class, 'AdminDueindex']);
        Route::get('/reports', [BillController::class, 'reports']);
        Route::get('/admin/reports', [BillController::class, 'Adminreports']);
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
Route::prefix('medicines')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
      Route::get('/', [medicineController::class, 'index']);
      Route::post('/', [medicineController::class, 'store']);
    });
});
Route::prefix('accounts')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/admin', [AccountsController::class, 'Adminindex']);
        Route::get('/', [AccountsController::class, 'index']);
        Route::get('/dailyCash', [AccountsController::class, 'dailyCash']);
        Route::get('/EmployeeDailyCash', [AccountsController::class, 'EmployeedailyCash']);
        Route::post('/addDailyExpense', [AccountsController::class, 'addDailyExpense']);
        Route::post('/addDailyExpenseEmployee', [AccountsController::class, 'addDailyExpenseEmployee']);
        Route::get('/daily-appointments-cash', [AccountsController::class, 'dailyAppointmentCash']);
        Route::get('admin/daily-appointments-cash', [AccountsController::class, 'AdmindailyAppointmentCash']);
    });
});

Route::prefix('admin/doctors')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/', [DoctorsController::class, 'adminIndex']);
        Route::get('/doctor-names', [DoctorsController::class, 'AdminDoctorsName']);
    });
});
Route::prefix('admin/patient')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/', [PatientController::class, 'AdminIndex']);
    });
});

Route::prefix('admin/bill')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/all-bill', [BillController::class, 'AdminIndex']);
    });
});



