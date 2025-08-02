<?php

namespace App\Http\Controllers\api\appoinments;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Slot;
use Illuminate\Http\Request;

class AppoinmentController extends Controller
{
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
}
