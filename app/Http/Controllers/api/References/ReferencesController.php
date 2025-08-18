<?php

namespace App\Http\Controllers\api\References;

use App\Http\Controllers\Controller;
use App\Models\Reference;
use Illuminate\Http\Request;

class ReferencesController extends Controller
{
    public function store(Request $request){
        $user = $request->user();

        if (!$user) {
        return response()->json(['message' => 'Unauthorized'], 401);
        }

        $ref = Reference::create([
            'fullname'=> $request->fullname,
            'rf_code'=> $request->rf_code,
            'phone' => $request->phone,
            'address'=> $request->address,
            'remarks'=> $request->remarks,
            'user_id'=> $user->id
        ]);

        return response()->json(['status' => true,'message'=>'Reference created successfuly','ref'=>$ref], 201);
     
    }

    public function update(Request $request, $id){
    $user = $request->user();
    if (!$user) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    $ref = Reference::find($id);
    if (!$ref) {
        return response()->json(['status' => false, 'message' => 'Reference not found'], 404);
    }

    $ref->update([
        'fullname' => $request->fullname,
        'rf_code'  => $request->rf_code,
        'phone'    => $request->phone,
        'address'  => $request->address,
        'remarks'  => $request->remarks,
    ]);

    return response()->json([
        'status'  => true,
        'message' => 'Reference updated successfully',
        'ref'     => $ref
    ], 200);
    }

    public function index(Request $request){
        $user = $request->user();

        if (!$user) {
        return response()->json(['message' => 'Unauthorized'], 401);
        }

        $refs = $user->references;

        return response()->json(['status' => true,'message'=>'Reference retrive successfuly','ref'=>$refs], 200);
    }


    public function single(Request $request){
        $user = $request->user();

        if (!$user) {
        return response()->json(['message' => 'Unauthorized'], 401);
        }

        $ref = Reference::find($request->id);

        if (!$ref) {
        return response()->json(['status' => false, 'message' => 'Reference not found'], 404);
        }

        return response()->json(['status' => true,'message'=>'Reference Retrive successfuly','ref'=>$ref], 200);
    }

    public function destroy(Request $request){
    $user = $request->user();

    if (!$user) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    $ref = Reference::find($request->id);

    if (!$ref) {
        return response()->json(['status' => false, 'message' => 'Reference not found'], 404);
    }

    $ref->delete();

    return response()->json([
        'status'  => true,
        'message' => 'Reference deleted successfully'
    ], 200);
    }
}
