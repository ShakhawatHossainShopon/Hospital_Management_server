<?php

namespace App\Http\Controllers\api\Test;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function index(Request $request){
        $test = $request->user()->tests()->with('groupe')->get();
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

        $test = $user->tests()->create([
            'item_name'    => $request->item_name,
            'code'         => $request->code,
            'groupe_id'    => $request->groupe_id,
            'unit_price'   => $request->unit_price,
            'max_discound' => $request->max_discound,
            'des'          => $request->des,
        ]);

        return response()->json([
            'message' => 'Test created successfully',
            'test'    => $test
        ], 201);
    }
}
