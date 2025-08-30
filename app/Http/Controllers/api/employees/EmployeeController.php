<?php

namespace App\Http\Controllers\api\employees;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $employees = Employee::where('user_id',$user->id)->latest()->get();
        return response()->json($employees, 200);
    }
        // Store a new employee
    public function store(Request $request)
    {
        $mainuser = $request->user();
        if($mainuser->role !== 'admin'){
            return response()->json([
            'message' => 'you are not authorized',
        ], 401);
        }
         $validate = Validator::make(
            $request->all(),
            [
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required',
            ]
        );
        if ($validate->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'validattion Errors',
                'errors' => $validate->errors()->all()
            ], 401);
        };
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'role' => 'employee',
            'admin_id'=>$mainuser->id,
        ]);
        $employee = Employee::create([
            'name' => $request->name,
            'email' => $request->email,
            'position' => $request->position,
            'password' => $request->password,
            'user_id'=> $mainuser->id,
            'employeeId'=>$user->id
        ]);
        
        return response()->json([
            'message' => 'Employee created successfully',
            'employee' => $employee,
            'created user'=> $user
        ], 201);
    }

    // Show single employee
    public function show($id)
    {
        $employee = Employee::find($id);
        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }
        return response()->json($employee, 200);
    }

    // Update employee
    public function update(Request $request, $id)
    {
        $employee = Employee::find($id);
        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        $employee->update([
            'name' => $request->name,
            'email' => $request->email,
            'position' => $request->position,
        ]);

        return response()->json([
            'message' => 'Employee updated successfully',
            'employee' => $employee
        ], 200);
    }

    // Delete employee
    public function destroy($id)
    {
        $employee = Employee::find($id);
        $user = User::find($employee->employeeId);
        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }
         if (!$user) {
            return response()->json(['message' => 'user not found'], 404);
        }
        $employee->delete();
        $user->delete();
        return response()->json(['message' => 'Employee deleted successfully',"employee"=>$employee,'deletedUser'=>$user], 200);
    }
}
