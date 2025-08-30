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
    public function Adminindex(Request $request)
    {
    $userId = $request->user()->admin_id;

    // Sum of paid_amount from bills for this user
    $totalPaidBills = Bill::where('user_id', $userId)->sum('paid_amount');

    // Sum of amount from appointments for this user
    $totalPaidAppointments = Appointment::where('user_id', $userId)->sum('amount');

            return response()->json([
                'total_paid_bills' => $totalPaidBills,
                'total_paid_appointments' => $totalPaidAppointments,
                'grand_total' => $totalPaidBills + $totalPaidAppointments,
            ]);
    }

    public function index(Request $request)
    {
    $userId = $request->user()->id;

    // Sum of paid_amount from bills for this user
    $totalPaidBills = Bill::where('user_id', $userId)->sum('paid_amount');

    // Sum of amount from appointments for this user
    $totalPaidAppointments = Appointment::where('user_id', $userId)->sum('amount');

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

    public function EmployeedailyCash(Request $request)
{
    $user = $request->user(); // authenticated user
    $date = $request->date ? Carbon::parse($request->date) : Carbon::today();

    $totalPaidBills = Bill::where('user_id', $user->admin_id)
        ->whereDate('created_at', $date)
        ->sum('paid_amount');

    $totalPaidAppointments = Appointment::where('user_id', $user->admin_id)
        ->whereDate('created_at', $date)
        ->sum('amount');

    $totalExpenses = DailyExpense::where('user_id', $user->admin_id)
        ->whereDate('created_at', $date)
        ->sum('price');

    $expenses = DailyExpense::where('user_id', $user->admin_id)
        ->whereDate('created_at', $date)
        ->get();

    return response()->json([
        'user_id' => $user->admin_id,
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

    public function addDailyExpenseEmployee(Request $request)
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
    $userId = $request->user()->admin_id;

    $query = Appointment::whereDate('created_at', $date);

    if ($userId) {
        $query->where('user_id', $userId);
    }

    $appointments = $query->get();
    $totalAppointments = $query->sum('amount');

    return response()->json([
        'date' => $date->format('Y-m-d'),
        'user_id' => $userId,
        'appointments_count' => $appointments->count(),
        'total_appointments_amount' => $totalAppointments,
        'appointments' => $appointments,
    ]);
}


public function AdmindailyAppointmentCash(Request $request)
{
    $date = $request->date ? Carbon::parse($request->date) : Carbon::today();
    $userId = $request->user()->id;

    $query = Appointment::whereDate('created_at', $date);

    if ($userId) {
        $query->where('user_id', $userId);
    }

    $appointments = $query->get();
    $totalAppointments = $query->sum('amount');

    return response()->json([
        'date' => $date->format('Y-m-d'),
        'user_id' => $userId,
        'appointments_count' => $appointments->count(),
        'total_appointments_amount' => $totalAppointments,
        'appointments' => $appointments,
    ]);
}


}
