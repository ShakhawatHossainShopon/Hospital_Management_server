<?php

namespace App\Http\Controllers\api\auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function signup(Request $request)
    {
        $validate = Validator::make(
            $request->all(),
            [
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required',
                'role' => 'required|in:user,doctor'
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
            'role' => $request->role,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'User Created Succesfully',
            'user' => $user
        ], 200);
    }
    public function login(Request $request)
    {
        $validate = Validator::make(
            $request->all(),
            [
                'email' => 'required',
                'password' => 'required',
            ]
        );

        if ($validate->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Authentication Errors',
                'errors' => $request
            ], 404);
        };



        if (Auth::attempt(['email' => $request->email, 'password' => $request->password,'role' => 'user'])) {
            /** @var \App\Models\User $authUser */
            $authUser = Auth::user();
            $authUser->tokens()->delete();
            $token = $authUser->createToken('Api Token')->plainTextToken;

            return response()->json([
                'status' => true,
                'message' => 'User Logged in Succesfully',
                'token' => $token,
                'token_type' => 'bearer',
                'userid'=> $authUser->id,
                'role' => $authUser->role
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Email And Password Not Match',
                'errors' => $validate->errors()->all()
            ], 401);
        };
    }
    public function logout(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();
        return response()->json([
            'status' => true,
            'message' => 'You Have Logout Succesfully',
            'user' => $user
        ], 200);
    }
    public function getUser(Request $request){
   
        if (!$request->user()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    return response()->json([
        'status' => true,
        'message' => 'Uuse retrive Succesfully',
        'user' => $request->user(),
        'token'=> $request->user()->currentAccessToken()->token
        ], 200);
    }
}
