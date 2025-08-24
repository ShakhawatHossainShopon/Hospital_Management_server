<?php

namespace App\Http\Controllers\api\Accounts;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Bill;
use App\Models\DailyExpense;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AccountsController extends Controller
{
     public function index()
    {
        // Sum of paid_amount from bills
        $totalPaidBills = Bill::sum('paid_amount');

        // Sum of amount from appointments
        $totalPaidAppointments = Appointment::sum('amount');

        return response()->json([
            'total_paid_bills' => $totalPaidBills,
            'total_paid_appointments' => $totalPaidAppointments,
            'grand_total' => $totalPaidBills + $totalPaidAppointments,
        ]);
    }

    public function dailyCash(Request $request)
{
    $user = $request->user(); // authenticated user
    $date = $request->date ? Carbon::parse($request->date) : Carbon::today();

    $totalPaidBills = Bill::where('user_id', $user->id)
        ->whereDate('created_at', $date)
        ->sum('paid_amount');

    $totalPaidAppointments = Appointment::where('user_id', $user->id)
        ->whereDate('created_at', $date)
        ->sum('amount');

    $totalExpenses = DailyExpense::where('user_id', $user->id)
        ->whereDate('created_at', $date)
        ->sum('price');

    $expenses = DailyExpense::where('user_id', $user->id)
        ->whereDate('created_at', $date)
        ->get();

    return response()->json([
        'user_id' => $user->id,
        'date' => $date->format('Y-m-d'),
        'total_paid_bills' => $totalPaidBills,
        'total_paid_appointments' => $totalPaidAppointments,
        'total_expenses' => $totalExpenses,
        'grand_total' => ($totalPaidBills + $totalPaidAppointments) - $totalExpenses,
        'expenses_record' => $expenses
    ]);
}


    public function addDailyExpense(Request $request)
    {
        $expense = DailyExpense::create([
            'name' => $request->name,
            'price' => $request->price,
            'user_id' => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'Expense added successfully',
            'expense' => $expense
        ]);
    }

    public function dailyAppointmentCash(Request $request)
{
    $date = $request->date ? Carbon::parse($request->date) : Carbon::today();
    $doctorId = $request->doctor_id;

    $query = Appointment::whereDate('created_at', $date);

    if ($doctorId) {
        $query->where('doctor_id', $doctorId);
    }
    $appointments = $query->get();
    $totalAppointments = $query->sum('amount');

    return response()->json([
        'date' => $date->format('Y-m-d'),
        'doctor_id' => $doctorId,
        'appointments_count' => $appointments->count(),
        'total_appointments_amount' => $totalAppointments,
        'appointments' => $appointments,
    ]);
}


}
