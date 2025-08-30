<?php

namespace App\Http\Controllers\api\Prescriptions;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Prescription;
use App\Models\Slot;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PrescriptionController extends Controller
{
    public function indexSlots(Request $request){
        $user = $request->user();
         if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $page = $request->query('page', 1);
        $appointmentIds = Appointment::where('doctor_id', $user->doctor_id)
        ->pluck('id');
        $prescriptionSlots = Slot::query()
        ->where('user_id', $user->admin_id)->whereIn(
            'appointment_id',$appointmentIds
        )
        ->where('is_booked', 1)->where('status','visiting')
        ->paginate(5, ['*'], 'page', $page);



          return response()->json(['status' => true,'message'=>'prescriptionSlots retrive successfuly','appoinments'=>[
            'data' => $prescriptionSlots->items(),
            'current_page' => $prescriptionSlots->currentPage(),
            'last_page' => $prescriptionSlots->lastPage(),
            'total' => $prescriptionSlots->total(),
            'per_page' => $prescriptionSlots->perPage(),
        ]], 200);
    }

     public function store(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        // Store prescription
        $prescription = Prescription::create([
            'prescriptions'   => json_encode($request->prescritions), // array to JSON
            'patient_id'      => $request->patient_id,
            'doctor_id'       => $user->doctor_id, // logged in doctor
            'appointment_id'  => $request->appointment_id,
            'user_id'  => $user->admin_id,
        ]);

        return response()->json([
            'message' => 'Prescription created successfully',
            'data'    => $prescription
        ], 201);
    }

        public function index(Request $request){
        $user = $request->user();
         if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $page = $request->query('page', 1);
        $date = $request->query('date');
         $prescription = Prescription::with([
        'patient',        // relation in Prescription model
        'appointment'     // relation in Prescription model
        ])
        ->where('doctor_id', $user->doctor_id)        
        ->when($date, fn($q) => $q->whereDate('created_at', $date))
        ->paginate(5, ['*'], 'page', $page);




          return response()->json(['status' => true,'message'=>'prescription retrive successfuly','prescriptions'=>[
            'data' => $prescription->items(),
            'current_page' => $prescription->currentPage(),
            'last_page' => $prescription->lastPage(),
            'total' => $prescription->total(),
            'per_page' => $prescription->perPage(),
        ]], 200);
    }

public function Getstatements(Request $request)
{
    $user = $request->user();
    if (!$user) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    $page = $request->query('page', 1);
    $date = $request->query('date');

    $appointments = Appointment::with('patient', 'slot')
        ->where('doctor_id', $user->doctor_id)
        ->when($date, fn($q) => $q->whereDate('created_at', $date))
        ->paginate(5, ['*'], 'page', $page);
    return response()->json([
        'status' => true,
        'message' => 'Statements retrieved successfully',
        'appointments' => [
            'data' => $appointments->items(),
            'current_page' => $appointments->currentPage(),
            'last_page' => $appointments->lastPage(),
            'total' => $appointments->total(),
            'per_page' => $appointments->perPage(),
        ]
    ], 200);
}

public function DoctordailyAppointmentCash(Request $request)
{
    $date = $request->date ? Carbon::parse($request->date) : Carbon::today();
    $user = $request->user();
    $page = $request->query('page', 1);

    $query = Appointment::with(['patient'])
        ->whereDate('created_at', $date)
        ->where('user_id', $user->admin_id);
    
    $appointments = $query->paginate(5, ['*'], 'page', $page);
    $totalAmount = $query->sum('amount');
    $totalQty = $query->count();
    return response()->json([
         'appointments' => [
            'data' => $appointments->items(),
            'current_page' => $appointments->currentPage(),
            'last_page' => $appointments->lastPage(),
            'per_page' => $appointments->perPage(),
            'total' => $appointments->total(),
         ],
        'total_amount' => $totalAmount,
        'total_appoinments'=>$totalQty
    ]);
}

}
