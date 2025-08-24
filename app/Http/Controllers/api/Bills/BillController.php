<?php

namespace App\Http\Controllers\api\Bills;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Patient;
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
    'user_id' => $user->id,
    ]);

    return response()->json([
    'message' => 'Bill created successfully',
    'bill' => $bill
    ], 201);

    }

    public function index(Request $request){
    $user = $request->user();
    if (!$user) {
    return response()->json(['message' => 'Unauthorized'], 401);
    }

    $page = $request->query('page', 1);
    $search = $request->query('mobile_phone');
    $from = $request->query('from');
    $to = $request->query('to');
    $filter = $request->query('filter'); // day, week, month





    $bills = $user->bills()->with(['patient:id,firstname,lastname,mobile_phone'])->when(
        $search,function ($query) use ($search){
            $query->whereHas('patient',function ($q) use ($search){
                $q->where('mobile_phone', 'like', "%{$search}%");
            });
        }
    )->when($from && $to, function ($query) use ($from, $to) {
            $query->whereBetween('created_at', [$from, $to]);
        })->when(!$from && !$to && $filter, function ($query) use ($filter) {
            if ($filter === 'day') {
                $query->whereDate('created_at', now()->toDateString());
            } elseif ($filter === 'week') {
                $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
            } elseif ($filter === 'month') {
                $query->whereMonth('created_at', now()->month)
                      ->whereYear('created_at', now()->year);
            }
        })->orderBy('created_at', 'desc')->paginate(15, ['*'], 'page', $page);

    return response()->json([
    'message' => 'Bill retrieve successfully',
    'data' => $bills->items(),
            'current_page' => $bills->currentPage(),
            'last_page' => $bills->lastPage(),
            'total' => $bills->total(),
            'per_page' => $bills->perPage(),
    ], 200);
    }

   public function Dueindex(Request $request){
    $user = $request->user();
    if (!$user) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    $page   = $request->query('page', 1);
    $search = $request->query('mobile_phone');

    $query = $user->bills()
        ->with(['patient:id,firstname,lastname,mobile_phone'])
        ->where('due_status', 1)
        ->when($search, function ($query) use ($search) {
            $query->whereHas('patient', function ($q) use ($search) {
                $q->where('mobile_phone', 'like', "%{$search}%");
            });
        });

    // Total due amount (with filter if applied)
    $totalDueAmount = $query->sum('due_amount');

    // Paginate
    $bills = $query->latest()->paginate(15, ['*'], 'page', $page);

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

    $page = $request->query('page', 1);
    $search = $request->query('mobile_phone'); // search input

    // Paid bills query with patient info and phone search
    $query = $user->bills()
        ->where('due_status', 0)
        ->with(['patient:id,firstname,lastname,mobile_phone'])
        ->when($search, function ($q) use ($search) {
            $q->whereHas('patient', function ($p) use ($search) {
                $p->where('mobile_phone', 'like', "%{$search}%");
            });
        });

    // Paginate
    $paidBills = $query->latest()->paginate(15, ['*'], 'page', $page);

    // Counts
    $allBillsCount = $user->bills()->count();
    $paidCount = $query->count();
    $dueCount = $user->bills()->where('due_status', 1)->count();
    $totalPaidAmount = $query->sum('paid_amount');
    $totalDueAmount = $user->bills()->where('due_status', 1)->sum('due_amount');

    return response()->json([
        'message'      => 'Paid bills retrieved successfully',
        'data'         => $paidBills->items(),
        'current_page' => $paidBills->currentPage(),
        'last_page'    => $paidBills->lastPage(),
        'total'        => $paidBills->total(),
        'per_page'     => $paidBills->perPage(),
        'counts' => [
            'all'              => $allBillsCount,
            'paid'             => $paidCount,
            'due'              => $dueCount,
            'total_paid_amount'=> $totalPaidAmount,
            'total_due_amount' => $totalDueAmount,
        ]
    ], 200);
}

    public function gatBillData(Request $request,$patientId){
    $user = $request->user();
    if (!$user) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
    $patient = Patient::select('id','firstname','lastname','gender','age','mobile_phone')->find($patientId);

    
    return response()->json([
        'message' => 'BillData retrieved successfully',
        'patient' => $patient,
        'doctors' => $user->doctors()->select('id', 'firstname','lastname','title')->get(),
        'refs' => $user->references()->select('id', 'fullname')->get(),
        'employees' => $user->employees()->select('id', 'name')->get(),
        'services' => $user->services()->select('id', 'service_name','unit_price')->get(),
        'tests' => $user->tests()->select('id', 'item_name','unit_price')->get(),
    ], 200);
    }

}
