<?php

namespace App\Http\Controllers\api\patients;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\Slot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PatientController extends Controller
{
    public function index(Request $request){
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        return response()->json(['message' => 'patient retrieve sucessfully',
        'status'=>true,
        'patients'=>$user->patients
        ], 200);
    }

    public function single(Request $request,$id){
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $patient = Patient::find($id);
        return response()->json(['message' => 'patient retrieve sucessfully',
        'status'=>true,
        'patient'=>$patient
        ], 200);
    }

     public function store(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all(),[
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'guardian_name' => 'string|max:255',
            'mobile_phone' => 'required|string|max:20',
            'gender' => 'required|string',
            'age' => 'required|integer|min:0|max:120',
            'birth_date' => 'date',
            'height' => 'string',
            'weight' => 'string',
            'blood_groupe' => 'string|max:5',
            'address_line' => 'string',
            'city' => 'string',
            'area' => 'string',
            'postal_code' => 'string|max:10',
        ]);

        if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation failed',
            'status' => false,
            'errors' => $validator->errors()
        ], 422);
        }

        $data = $validator->validated();
        $data['user_id'] = $user->id;

        $patient = Patient::create($data);

        return response()->json([
            'message' => 'Patient created successfully',
            'status' => true,
            'patient' => $patient
        ], 201);
    }

    public function update(Request $request, $id)
{
    $user = $request->user();
    if (!$user) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    $patient = Patient::find($id);
    if (!$patient) {
        return response()->json(['message' => 'Patient not found'], 404);
    }

    $patient->update($request->only([
        'firstname', 'lastname', 'guardian_name', 'mobile_phone', 'gender', 'age', 'birth_date',
        'height', 'weight', 'blood_groupe', 'address_line', 'city', 'area', 'postal_code'
    ]));
    $slot = Slot::where('patient_id', $patient->id)->first();
    $slot->update([
    'name' => $patient->firstname . " " .$patient->lastname  ,
    'patient_age' => $patient->age,
    'phone' => $patient->mobile_phone,
    'type'=>'Old'
    ]);
    return response()->json([
        'status' => true,
        'message' => 'Patient updated successfully',
        'patient' => $patient,
        "slot"=>$slot
    ], 200);
}

public function destroy(Request $request, $id)
{
    $user = $request->user();
    if (!$user) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    $patient = Patient::find($id);
    if (!$patient) {
        return response()->json(['message' => 'Patient not found'], 404);
    }

    $patient->delete();

    return response()->json(['message' => 'Patient deleted successfully'], 200);
}

public function getUserByPhone(Request $request, $phone)
{
    $user = $request->user();
    if (!$user) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    $patient = Patient::where('mobile_phone', $phone)->first();
    if (!$patient) {
        return response()->json(['message' => 'Patient not found'], 404);
    }
    return response()->json($patient);
}
}
