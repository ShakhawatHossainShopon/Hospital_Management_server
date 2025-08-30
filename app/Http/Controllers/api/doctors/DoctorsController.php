<?php

namespace App\Http\Controllers\api\doctors;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DoctorsController extends Controller
{
    public function index(Request $request){
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $doctors = Doctor::where('user_id',$user->admin_id)->latest()->get();
        return response()->json(['message' => 'doctor retrieve sucessfully',
        'status'=>true,
        'doctors'=>$doctors
        ], 200);
    }
    public function adminIndex(Request $request){
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $doctors = $user->doctors;
        return response()->json(['message' => 'doctor retrieve sucessfully',
        'status'=>true,
        'doctors'=>$doctors
        ], 200);
    }
    public function singleDoctor(Request $request,$id){
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $doctor = Doctor::find($id);
        if (!$doctor) {
            return response()->json(['message' => 'Doctor not Found'], 404);
        }
        return response()->json(['message' => 'doctor retrieve sucessfully',
        'status'=>true,
        'doctor'=>$doctor
        ], 200);
    }
      public function store(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        if($user->role === 'admin'){
             return response()->json(['message' => 'Unauthorized Or admin cannot add Doctors'], 401);
        }
          $user = User::create([
            'name' => $request->firstname ." ". $request->lastname,
            'email' => $request->email,
            'password' => $request->password,
            'role' => 'doctor',
            'admin_id'=>$user->admin_id,
        ]);
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
            'user_id' => $user->admin_id,
            'main_user_id'=> $user->id
        ]);
        $user->update(['doctor_id' => $doctor->id]);
      

        return response()->json([
            'status' => true,
            'message' => 'Doctor created successfully',
            'doctor' => $doctor,
            'user' => $user
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
    $user = User::find($doctor->main_user_id);
    if (!$user) {
        return response()->json([
            'status' => false,
            'message' => 'user not found'
        ], 404);
    }

    $doctor->delete();
    $user->delete();
    return response()->json([
        'status' => true,
        'message' => 'Doctor deleted successfully',
        'deleted doctor'=>$doctor,
        'deleted user'=>$user,
    ], 200);
    }

    public function DoctorsName(Request $request){
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $doctors = DB::table('doctors')
        ->select('id', DB::raw("CONCAT(firstname, ' ', lastname) as name"))
        ->where('user_id', $user->admin_id)
        ->get();
        return response()->json([
        'message' => 'doctor retrieve sucessfully',
        'status'=>true,
        'doctors' => $doctors,
        'user_id'=>$user->admin_id,
        ], 200);
    }

        public function AdminDoctorsName(Request $request){
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $doctors = $user->doctors->map(function ($doctor) {
        return [
            'id' => $doctor->id,
            'name' => $doctor->firstname . ' ' . $doctor->lastname,
        ];
        });
        return response()->json([
        'message' => 'doctor retrieve sucessfully',
        'status'=>true,
        'doctors' => $doctors,
        'user_id'=>$user->id,
        ], 200);
    }

}
