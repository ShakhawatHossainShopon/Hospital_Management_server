<?php

namespace App\Http\Controllers\api\doctors;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class DoctorsController extends Controller
{
    public function index(Request $request){
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        return response()->json(['message' => 'doctor retrieve sucessfully',
        'status'=>true,
        'doctors'=>$user->doctors
        ], 200);
    }

      public function store(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Validate incoming data
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'working_place' => 'string',
            'available_days' => 'string',
            'bmdc_code' => 'required|string',
            'job_designation' => 'required|string',
            'booking_phone' => 'required|string',
            'gender' => 'required|string',
            'degree_name' => 'required|string',
            'consultancy_fee' => 'required|string',
            'mobile' => 'required|string',
            'provide_service' => 'string',
            'about' => 'string',
            'email' => 'required|email|unique:doctors,email',
            'speciality' => 'string',
            'password' => 'required|string',
            'starting_pratice' => 'date',
            'achievement' => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation Errors',
                'errors' => $validator->errors()->all()
            ], 422);
        }

        // Create doctor linked to authenticated user
        $doctor = Doctor::create([
            'title' => $request->title,
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'working_place' => $request->working_place,
            'available_days' => $request->available_days,
            'bmdc_code' => $request->bmdc_code,
            'job_designation' => $request->job_designation,
            'booking_phone' => $request->booking_phone,
            'gender' => $request->gender,
            'degree_name' => $request->degree_name,
            'consultancy_fee' => $request->consultancy_fee,
            'mobile' => $request->mobile,
            'provide_service' => $request->provide_service,
            'about' => $request->about,
            'email' => $request->email,
            'speciality' => $request->speciality,
            'password' => $request->password,
            'starting_pratice' => $request->starting_pratice,
            'achievement' => $request->achievement,
            'user_id' => $user->id,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Doctor created successfully',
            'doctor' => $doctor
        ], 201);
    }

    public function update(Request $request, $id)
    {
    $user = $request->user();
    if (!$user) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

   $doctor = Doctor::find($id);

    if (!$doctor) {
        return response()->json(['message' => 'Doctor not found'], 404);
    }

    $validator = Validator::make($request->all(), [
        'title' => 'sometimes|required|string',
        'firstname' => 'sometimes|required|string',
        'lastname' => 'sometimes|required|string',
        'working_place' => 'nullable|string',
        'available_days' => 'nullable|string',
        'bmdc_code' => 'sometimes|required|string',
        'job_designation' => 'string',
        'booking_phone' => 'sometimes|required|string',
        'gender' => 'sometimes|required|string',
        'degree_name' => 'sometimes|required|string',
        'consultancy_fee' => 'sometimes|required|string',
        'mobile' => 'sometimes|required|string',
        'provide_service' => 'nullable|string',
        'about' => 'nullable|string',
        'email' => 'sometimes|required|email|unique:doctors,email,' . $doctor->id,
        'speciality' => 'nullable|string',
        'password' => 'sometimes|required|string',
        'starting_pratice' => 'nullable|date',
        'achievement' => 'nullable|string',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'message' => 'Validation Errors',
            'errors' => $validator->errors()->all()
        ], 422);
    }

    $doctor->update($request->all());

    return response()->json([
        'status' => true,
        'message' => 'Doctor updated successfully',
        'doctor' => $doctor
    ], 200);
}
public function destroy($id){
    $doctor = Doctor::find($id);

    if (!$doctor) {
        return response()->json([
            'status' => false,
            'message' => 'Doctor not found'
        ], 404);
    }

    $doctor->delete();

    return response()->json([
        'status' => true,
        'message' => 'Doctor deleted successfully'
    ], 200);
    }

}
