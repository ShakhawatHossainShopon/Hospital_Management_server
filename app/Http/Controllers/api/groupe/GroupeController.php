<?php

namespace App\Http\Controllers\api\groupe;

use App\Http\Controllers\Controller;
use App\Models\Groupe;
use Illuminate\Http\Request;

class GroupeController extends Controller
{
    public function store(Request $request){
        $groupe = Groupe::create([
            'name'=>$request->name,
            'des'=>$request->des,
            'user_id'=>$request->user()->id,
        ]);

        return response()->json([
            'message' => 'Groupe created successfully',
            'groupe' => $groupe,
        ], 201);
    }   
    public function index(Request $request){
        $groupe = $request->user()->groupes;

        if(!$groupe){
            return response()->json([
            'message' => 'Groupe not found',
        ], 404);
        }

        return response()->json([
            'message' => 'Groupe Retrieve successfully',
            'groupe'=>$groupe
        ], 200);
    }
}
