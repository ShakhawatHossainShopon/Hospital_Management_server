<?php

namespace App\Http\Controllers\api\Bills;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Doctor;
use App\Models\Employee;
use App\Models\Patient;
use App\Models\Reference;
use App\Models\Service;
use App\Models\Test;
use Illuminate\Http\Request;

class BillController extends Controller
{
    public function store(Request $request)
    {
    $user = $request->user();
    if (!$user) {
    return response()->json(['message' => 'Unauthorized'], 401);
    }
    
    $paid = $request->paid_amount;
    $total = $request->total_amount;
    $payable_amount = $request->payable_amount;

    if ($paid <= 0) {
    return response()->json([
        'message' => 'Paid amount must be greater than 0',
    ], 400);
    }

    if ($paid > $payable_amount) {
    return response()->json([
        'message' => 'Paid amount cannot be larger than total amount',
    ], 400);
    }

    $dueAmount = $payable_amount - $paid;
    $dueStatus = $dueAmount > 0; // true if some amount is due

    $bill = Bill::create([
    'invoice_data' => json_encode($request->invoice_data),
    'refer_id' => $request->refer_id,
    'doctor_id' => $request->doctor_id,
    'patient_id' => $request->patient_id,
    'total_amount' => $total,
    'paid_amount' => $paid,
    'due_status' => $dueStatus,
    'due_amount' => $dueAmount,
    'note' => $request->note,
    'discount' => $request->discount,
    'employee_id'=> $request->employee_id,
    'online_fee'=>$request->online_fee,
    'payable_amount'=> $request->payable_amount,
    'user_id' => $user->admin_id,
    ]);

    return response()->json([
    'message' => 'Bill created successfully',
    'bill' => $bill
    ], 201);

    }

public function index(Request $request)
{
    $user = $request->user();
    if (!$user) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    $page   = $request->query('page', 1);
    $search = $request->query('mobile_phone');
    $from   = $request->query('from');
    $to     = $request->query('to');
    $filter = $request->query('filter'); // day, week, month

    $bills = Bill::select('bills.*', 'patients.id as patient_id', 'patients.firstname', 'patients.lastname', 'patients.mobile_phone')
        ->join('patients', 'patients.id', '=', 'bills.patient_id')
        ->where('bills.user_id', $user->admin_id)
        ->when($search, function ($query) use ($search) {
            $query->where('patients.mobile_phone', 'like', "%{$search}%");
        })
        ->when($from && $to, function ($query) use ($from, $to) {
            $query->whereBetween('bills.created_at', [$from, $to]);
        })
        ->when(!$from && !$to && $filter, function ($query) use ($filter) {
            if ($filter === 'day') {
                $query->whereDate('bills.created_at', now()->toDateString());
            } elseif ($filter === 'week') {
                $query->whereBetween('bills.created_at', [now()->startOfWeek(), now()->endOfWeek()]);
            } elseif ($filter === 'month') {
                $query->whereMonth('bills.created_at', now()->month)
                      ->whereYear('bills.created_at', now()->year);
            }
        })
        ->orderBy('bills.created_at', 'desc')
        ->paginate(15, ['*'], 'page', $page);

    return response()->json([
        'message' => 'Bill retrieve successfully',
        'data' => $bills->items(),
        'current_page' => $bills->currentPage(),
        'last_page' => $bills->lastPage(),
        'total' => $bills->total(),
        'per_page' => $bills->perPage(),
    ], 200);
}

public function AdminIndex(Request $request)
{
    $user = $request->user();
    if (!$user) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    $page   = $request->query('page', 1);
    $search = $request->query('mobile_phone');
    $from   = $request->query('from');
    $to     = $request->query('to');
    $filter = $request->query('filter'); // day, week, month

    $bills = Bill::select('bills.*', 'patients.id as patient_id', 'patients.firstname', 'patients.lastname', 'patients.mobile_phone')
        ->join('patients', 'patients.id', '=', 'bills.patient_id')
        ->where('bills.user_id', $user->id)
        ->when($search, function ($query) use ($search) {
            $query->where('patients.mobile_phone', 'like', "%{$search}%");
        })
        ->when($from && $to, function ($query) use ($from, $to) {
            $query->whereBetween('bills.created_at', [$from, $to]);
        })
        ->when(!$from && !$to && $filter, function ($query) use ($filter) {
            if ($filter === 'day') {
                $query->whereDate('bills.created_at', now()->toDateString());
            } elseif ($filter === 'week') {
                $query->whereBetween('bills.created_at', [now()->startOfWeek(), now()->endOfWeek()]);
            } elseif ($filter === 'month') {
                $query->whereMonth('bills.created_at', now()->month)
                      ->whereYear('bills.created_at', now()->year);
            }
        })
        ->orderBy('bills.created_at', 'desc')
        ->paginate(15, ['*'], 'page', $page);

    return response()->json([
        'message' => 'Bill retrieve successfully',
        'data' => $bills->items(),
        'current_page' => $bills->currentPage(),
        'last_page' => $bills->lastPage(),
        'total' => $bills->total(),
        'per_page' => $bills->perPage(),
    ], 200);
}



public function Dueindex(Request $request)
{
    $user = $request->user();
    if (!$user) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    $page   = $request->query('page', 1);
    $search = $request->query('mobile_phone');

    // Base query with join (no relationship)
    $query = Bill::select('bills.*', 'patients.id as patient_id', 'patients.firstname', 'patients.lastname', 'patients.mobile_phone')
        ->join('patients', 'patients.id', '=', 'bills.patient_id')
        ->where('bills.user_id', $user->admin_id)
        ->where('bills.due_status', 1)
        ->when($search, function ($q) use ($search) {
            $q->where('patients.mobile_phone', 'like', "%{$search}%");
        });

    // Total due amount (with filters applied)
    $totalDueAmount = (clone $query)->sum('bills.due_amount');

    // Paginated results
    $bills = $query->orderBy('bills.created_at', 'desc')
        ->paginate(15, ['*'], 'page', $page);

    return response()->json([
        'message'          => 'Due bills retrieved successfully',
        'data'             => $bills->items(),
        'current_page'     => $bills->currentPage(),
        'last_page'        => $bills->lastPage(),
        'total'            => $bills->total(),
        'per_page'         => $bills->perPage(),
        'total_due_amount' => $totalDueAmount
    ], 200);
}


public function AdminDueindex(Request $request)
{
    $user = $request->user();
    if (!$user) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    $page   = $request->query('page', 1);
    $search = $request->query('mobile_phone');

    // Base query with join (no relationship)
    $query = Bill::select('bills.*', 'patients.id as patient_id', 'patients.firstname', 'patients.lastname', 'patients.mobile_phone')
        ->join('patients', 'patients.id', '=', 'bills.patient_id')
        ->where('bills.user_id', $user->id)
        ->where('bills.due_status', 1)
        ->when($search, function ($q) use ($search) {
            $q->where('patients.mobile_phone', 'like', "%{$search}%");
        });

    // Total due amount (with filters applied)
    $totalDueAmount = (clone $query)->sum('bills.due_amount');

    // Paginated results
    $bills = $query->orderBy('bills.created_at', 'desc')
        ->paginate(15, ['*'], 'page', $page);

    return response()->json([
        'message'          => 'Due bills retrieved successfully',
        'data'             => $bills->items(),
        'current_page'     => $bills->currentPage(),
        'last_page'        => $bills->lastPage(),
        'total'            => $bills->total(),
        'per_page'         => $bills->perPage(),
        'total_due_amount' => $totalDueAmount
    ], 200);
}


public function reports(Request $request)
{
    $user = $request->user();
    if (!$user) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    $page   = $request->query('page', 1);
    $search = $request->query('mobile_phone'); // search input

    // Base query for paid bills with patient info
    $query = Bill::select('bills.*', 'patients.id as patient_id', 'patients.firstname', 'patients.lastname', 'patients.mobile_phone')
        ->join('patients', 'patients.id', '=', 'bills.patient_id')
        ->where('bills.user_id', $user->admin_id)
        ->where('bills.due_status', 0)
        ->when($search, function ($q) use ($search) {
            $q->where('patients.mobile_phone', 'like', "%{$search}%");
        });

    // Paginated paid bills
    $paidBills = (clone $query)
        ->orderBy('bills.created_at', 'desc')
        ->paginate(15, ['*'], 'page', $page);

    // Counts and totals
    $allBillsCount   = Bill::where('user_id', $user->admin_id)->count();
    $paidCount       = (clone $query)->count();
    $dueCount        = Bill::where('user_id', $user->admin_id)->where('due_status', 1)->count();
    $totalPaidAmount = (clone $query)->sum('bills.paid_amount');
    $totalDueAmount  = Bill::where('user_id', $user->admin_id)->where('due_status', 1)->sum('bills.due_amount');

    return response()->json([
        'message'      => 'Paid bills retrieved successfully',
        'data'         => $paidBills->items(),
        'current_page' => $paidBills->currentPage(),
        'last_page'    => $paidBills->lastPage(),
        'total'        => $paidBills->total(),
        'per_page'     => $paidBills->perPage(),
        'counts' => [
            'all'               => $allBillsCount,
            'paid'              => $paidCount,
            'due'               => $dueCount,
            'total_paid_amount' => $totalPaidAmount,
            'total_due_amount'  => $totalDueAmount,
        ]
    ], 200);
}

public function Adminreports(Request $request)
{
    $user = $request->user();
    if (!$user) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    $page   = $request->query('page', 1);
    $search = $request->query('mobile_phone'); // search input

    // Base query for paid bills with patient info
    $query = Bill::select('bills.*', 'patients.id as patient_id', 'patients.firstname', 'patients.lastname', 'patients.mobile_phone')
        ->join('patients', 'patients.id', '=', 'bills.patient_id')
        ->where('bills.user_id', $user->id)
        ->where('bills.due_status', 0)
        ->when($search, function ($q) use ($search) {
            $q->where('patients.mobile_phone', 'like', "%{$search}%");
        });

    // Paginated paid bills
    $paidBills = (clone $query)
        ->orderBy('bills.created_at', 'desc')
        ->paginate(15, ['*'], 'page', $page);

    // Counts and totals
    $allBillsCount   = Bill::where('user_id', $user->id)->count();
    $paidCount       = (clone $query)->count();
    $dueCount        = Bill::where('user_id', $user->id)->where('due_status', 1)->count();
    $totalPaidAmount = (clone $query)->sum('bills.paid_amount');
    $totalDueAmount  = Bill::where('user_id', $user->id)->where('due_status', 1)->sum('bills.due_amount');

    return response()->json([
        'message'      => 'Paid bills retrieved successfully',
        'data'         => $paidBills->items(),
        'current_page' => $paidBills->currentPage(),
        'last_page'    => $paidBills->lastPage(),
        'total'        => $paidBills->total(),
        'per_page'     => $paidBills->perPage(),
        'counts' => [
            'all'               => $allBillsCount,
            'paid'              => $paidCount,
            'due'               => $dueCount,
            'total_paid_amount' => $totalPaidAmount,
            'total_due_amount'  => $totalDueAmount,
        ]
    ], 200);
}

public function gatBillData(Request $request, $patientId)
{
    $user = $request->user();
    if (!$user) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    $patient = Patient::select('id','firstname','lastname','gender','age','mobile_phone')
        ->find($patientId);

    $doctors = Doctor::select('id', 'firstname','lastname','title')
        ->where('user_id', $user->admin_id)
        ->get();

    $refs = Reference::select('id', 'fullname')
        ->where('user_id', $user->admin_id)
        ->get();

    $employees = Employee::select('id', 'name')
        ->where('user_id', $user->admin_id)
        ->get();

    $services = Service::select('id', 'service_name','unit_price')
        ->where('user_id', $user->admin_id)
        ->get();

    $tests = Test::select('id', 'item_name','unit_price')
        ->where('user_id', $user->admin_id)
        ->get();

    return response()->json([
        'message' => 'BillData retrieved successfully',
        'patient' => $patient,
        'doctors' => $doctors,
        'refs' => $refs,
        'employees' => $employees,
        'services' => $services,
        'tests' => $tests,
    ], 200);
}


}
