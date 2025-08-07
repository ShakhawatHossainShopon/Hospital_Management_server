<?php

namespace App\Http\Controllers\api\appoinments;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Slot;
use Illuminate\Http\Request;

class AppoinmentController extends Controller
{
    public function index(Request $request){
        $user = $request->user();
        if(!$user){
        return response()->json([
        'message' => 'Unothorized',
        ],401);
        }

        $appoinments = Appointment::with('patient','slot')->get();
        return response()->json([
        'message' => 'Appointment Retrive successfully',
        'appointment' => $appoinments,
    ]);
    }

    public function appoinmentById(Request $request){
        $user = $request->user();
        if(!$user){
        return response()->json([
        'message' => 'Unothorized',
        ],401);
        }

        $appoinments = Appointment::with('doctor','patient')->where('id', $request->id)->first();
        return response()->json([
        'message' => 'Appointment Retrive successfully',
        'appointment' => $appoinments,
    ]);
    }
    public function AppoinmentsByDoctorId(Request $request){
        $user = $request->user();
        if(!$user){
        return response()->json([
        'message' => 'Unothorized',
        ],401);
        }

        $appointments = Appointment::with('patient','slot')
        ->where('doctor_id', $request->id)
        ->get();
        return response()->json([
        'message' => 'Appointment retrieved successfully',
        'appointment'=>$appointments
    ]);
    }
    public function store(Request $request){
        $user = $request->user();
        if(!$user){
        return response()->json([
        'message' => 'Unothorized',
        ],401);
        }
        $user_id = $user->id;
        $patient_id = $request->patient_id;  
        $doctor_id = $request->doctor_id; 
        $slot_id = $request->slot_id;
        $patient = $user->patients()->where('id', $patient_id)->first();
    if (!$patient) {
    return response()->json(['message' => 'Patient not found'], 404);
    }
        $patient_name = $patient->firstname . ' ' . $patient->lastname;
        $patient_phone = $patient->mobile_phone;
        $patient_type = 'Old';
        $patient_age = $patient->age;

        $appoinment = Appointment::create([
            'patient_id' => $patient_id,
            'doctor_id' => $doctor_id,
            'user_id' => $user_id,
        ]);
        

        $slot = Slot::find($slot_id);
        if(!$slot){
        return response()->json([
        'message' => 'Slot Not found',
        ],404);
        }
        if ($slot) {
        $slot->update([
        'patient_id' => $patient_id,
        'is_booked'=> true,
        'name'=> $patient_name,
        'phone'=> $patient_phone,
        'user_id' => $user_id,
        'type' => $patient_type,
        'patient_age'=>$patient_age,
        'appointment_id'=>$appoinment->id,
        ]);
        }

    return response()->json([
    'message' => 'Appointment created and slot updated successfully',
    'appointment' => $appoinment,
    'slot'=>$slot
    ]);
    } 

    public function storeWithUser(Request $request){
        $user = $request->user();
        if(!$user){
        return response()->json([
        'message' => 'Unothorized',
        ],401);
        }
        $user_id = $user->id;
        $patient_phone = $request->mobile_phone;
        $firstname = $request->firstname;
        $lastname = $request->lastname;
        $gender = $request->gender;
        $age = $request->age;
        $note = $request->note;
        $doctor_id = $request->doctor_id;
        $user_id = $user->id;
        $slot_id = $request->slot_id;
        $patient_type = "New";
        $patient = Patient::create([
            'firstname'=>$firstname,
            'lastname'=>$lastname,
            'mobile_phone'=>$patient_phone,
            'gender'=>$gender,
            'age'=>$age,
            'user_id'=>$user_id
        ]);
         
        $appoinment = Appointment::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor_id,
            'user_id' => $user_id,
        ]);

        $slot = Slot::find($slot_id);
        if(!$slot){
        return response()->json([
        'message' => 'Slot Not found',
        ],404);
        }
        if ($slot) {
        $slot->update([
        'patient_id' => $patient->id,
        'is_booked'=> true,
        'name'=> $patient->firstname . " " . $patient->lastname,
        'phone'=> $patient->mobile_phone,
        'user_id' => $user_id,
        'type' => $patient_type,
        'patient_age'=>$patient->age,
        'appointment_id'=>$appoinment->id,
        'note' => $note
        ]);
        return response()->json([
    'message' => 'Appointment created and slot updated successfully',
    'appointment' => $appoinment,
    'slot'=>$slot
    ]);
        }
    }

    public function payAppointment(Request $request){
        $appointmentId = $request->appointmentId;
        $amount = $request->amount;

         $appointment = Appointment::find($appointmentId);
         if(!$appointment){
        return response()->json([
        'message' => 'appoinemnt Not found',
        ],404);
        }
        $appointment->update([
            'payment_status' => true,
            'amount'=>$amount,
            'due_status'=>false,
            'due'=>null
        ]);
        return response()->json([
        'message' => 'Appointment paid successfully',
        'appointment' => $appointment,
    ]);
    }
}
