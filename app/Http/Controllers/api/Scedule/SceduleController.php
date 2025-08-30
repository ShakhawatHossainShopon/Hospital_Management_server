<?php

namespace App\Http\Controllers\api\Scedule;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Scedule;
use App\Models\Slot;
use DateTime;
use Illuminate\Http\Request;

class SceduleController extends Controller
{
    public function index(Request $request){
        $user = $request->user();

        if (!$user) {
        return response()->json(['message' => 'Unauthorized'], 401);
        }

    // manually fetch doctor IDs without relation
        $doctorIds = Doctor::where('user_id', $user->admin_id)->pluck('id');

        $schedules = Scedule::whereIn('doctor_id', $doctorIds)->get();

        return response()->json([
        'message'   => 'Schedules retrieved successfully',
        'status'    => true,
        'schedules' => $schedules
        ], 200);
    }
    public function indexAdmin(Request $request){
        $user = $request->user();

        if (!$user) {
        return response()->json(['message' => 'Unauthorized'], 401);
        }

    // manually fetch doctor IDs without relation
        $doctorIds = Doctor::where('user_id', $user->admin_id)->pluck('id');

        $schedules = Scedule::whereIn('doctor_id', $doctorIds)->get();

        return response()->json([
        'message'   => 'Schedules retrieved successfully',
        'status'    => true,
        'schedules' => $schedules
        ], 200);
    }

    public function getDoctorSchedules(Request $request, $doctorId)
{
     $user = $request->user();

    if (!$user) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    $doctor = Doctor::where('id', $doctorId)
        ->where('user_id', $user->admin_id)
        ->first();

    if (!$doctor) {
        return response()->json(['message' => 'Doctor not found or unauthorized'], 404);
    }

    $schedules = Scedule::where('doctor_id', $doctorId)->get();

    return response()->json([
        'message' => 'Schedules retrieved successfully',
        'status' => true,
        'schedules' => $schedules
    ], 200);
    }

public function getDoctorDaySchedules(Request $request, $doctorId, $day)
{
    $user = $request->user();

    if (!$user) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    // Check if the doctor belongs to this user
    $doctor = Doctor::where('id', $doctorId)
        ->where('user_id', $user->admin_id)
        ->first();

    if (!$doctor) {
        return response()->json(['message' => 'Doctor not found or unauthorized'], 404);
    }

    // Validate day is between 0 and 6
    if (!in_array($day, range(0, 6))) {
        return response()->json(['message' => 'Invalid day index (must be 0 to 6)'], 422);
    }

    // Fetch schedules for that doctor and day
    $schedules = Scedule::where('doctor_id', $doctorId)
        ->where('day', $day)
        ->get();

    return response()->json([
        'message'   => 'Schedules retrieved successfully',
        'status'    => true,
        'schedules' => $schedules,
    ], 200);
}

 public function store(Request $request)
    {
        $user = $request->user();
        $schedule = Scedule::create([
            'day' => $request->day,
            'startTime' => $request->startTime,
            'time_indicator' => $request->time_indicator,
            'capacity' => $request->capacity,
            'duration' => $request->duration,
            'doctor_id' => $request->doctor_id,
            'user_id' => $user->admin_id
        ]);
         $slots = $this->generateSlots($request->startTime, $request->capacity, $request->duration);
        foreach ($slots as $time) {
            Slot::create([
                'time' => $time,
                'scedule_id' => $schedule->id,
                'time_indicator' => $request->time_indicator,
                'user_id' => $user->admin_id
            ]);
        }

        return response()->json([
            'message' => 'Schedule and slots created successfully',
            'schedule' => $schedule
        ]);
    }
    private function generateSlots($startTime, $capacity, $duration)
    {
        $slots = [];
        $current = new DateTime($startTime);

        for ($i = 0; $i < $capacity; $i++) {
            $slots[] = $current->format('H:i:s');
            $current->modify("+{$duration} minutes");
        }

        return $slots;
    }


public function getSlotsByDay(Request $request)
{
    $user = $request->user();

    if (!$user) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    $doctorId = $request->doctor_id;
    $day = $request->day; // 0=Sunday, 6=Saturday

    // Check doctor belongs to this user
    $doctor = Doctor::where('id', $doctorId)
        ->where('user_id', $user->admin_id)
        ->select('firstname', 'lastname', 'title', 'degree_name', 'speciality', 'bmdc_code')
        ->first();

    if (!$doctor) {
        return response()->json([
            'status'  => false,
            'message' => 'Doctor not found or unauthorized'
        ], 404);
    }

    // Get schedule with slots
    $schedule = Scedule::with('slots')
        ->where('doctor_id', $doctorId)
        ->where('day', $day)
        ->first();

    if (!$schedule) {
        return response()->json([
            'status'  => false,
            'message' => 'No schedule found for this doctor and day'
        ], 404);
    }

    return response()->json([
        'status'  => true,
        'slots'   => $schedule->slots,
        'doctor'  => $doctor
    ], 200);
}

public function destroySlot(Request $request){
    $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    $slot = Slot::find($request->id);
    $slot->delete();

    return response()->json([
        'message' => 'Slot deleted successfully',
    ]);
}

public function destroyScedule(Request $request){
    $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    $slot = Scedule::find($request->id);
    $slot->delete();

    return response()->json([
        'message' => 'Schedule deleted successfully',
    ]);
}

public function updateStatus(Request $request)
{
    $user = $request->user();
    if (!$user) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    $slot = Slot::find($request->id);
    if (!$slot) {
        return response()->json(['message' => 'Slot not found'], 404);
    }

    $slot->status = $request->status;
    $slot->save();

    return response()->json([
        'message' => 'Schedule status updated successfully',
        'status' => $slot->status,
    ]);
}


}
