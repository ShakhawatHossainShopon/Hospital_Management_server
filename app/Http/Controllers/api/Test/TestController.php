<?php

namespace App\Http\Controllers\api\Test;

use App\Http\Controllers\Controller;
use App\Models\Test;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function index(Request $request){
        $user = $request->user();
        $test = Test::with('groupe')
        ->where('user_id', $user->id)
        ->get();
        if(!$test){
            return response()->json([
            'message'=>'test not found',
        ],404);
        }
        return response()->json([
            'message'=>'Services Retrive Successfully',
            'test'=>  $test
        ],200);
    }

     public function store(Request $request)
    {
        $user = $request->user();

        $test = Test::create([
            'item_name'    => $request->item_name,
            'code'         => $request->code,
            'groupe_id'    => $request->groupe_id,
            'unit_price'   => $request->unit_price,
            'max_discound' => $request->max_discound,
            'des'          => $request->des,
            'user_id'      => $user->id
        ]);

        return response()->json([
            'message' => 'Test created successfully',
            'test'    => $test
        ], 201);
    }
    public function destroy($id)
    {
    $test = Test::find($id);

    if (!$test) {
        return response()->json([
            'message' => 'Test not found',
        ], 404);
    }

    $test->delete();

    return response()->json([
        'message' => 'Test deleted successfully',
    ], 200);
}

}
