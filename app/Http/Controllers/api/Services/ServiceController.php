<?php

namespace App\Http\Controllers\Api\Services;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    // List all services
    public function index(Request $request)
    {
        $user = $request->user();
        $services = $user->services;
        return response()->json($services, 200);
    }

    // Store a new service
    public function store(Request $request)
    {

        $user = $request->user();
        $service = Service::create([
            'service_name' => $request->service_name,
            'unit_price'   => $request->unit_price,
            'des'          => $request->des,
            'user_id'=>$user->id
        ]);

        return response()->json(['status' => true,'message'=>'service store successfuly','services'=>$service], 201);
    }

    // Show single service
    public function show($id)
    {
        $service = Service::find($id);

        if (!$service) {
            return response()->json(['message' => 'Service not found'], 404);
        }

        return response()->json(['status' => true,'message'=>'service retrieve successfuly','services'=>$service], 200);
    }

    // Update a service
    public function update(Request $request, $id)
    {
        $service = Service::find($id);

        if (!$service) {
            return response()->json(['message' => 'Service not found'], 404);
        }

        $service->update([
            'service_name' => $request->service_name,
            'unit_price'   => $request->unit_price,
            'des'          => $request->des,
        ]);

        return response()->json(['status' => true,'message'=>'service update successfuly','services'=>$service], 200);
    }

    // Delete a service
    public function destroy($id)
    {
        $service = Service::find($id);

        if (!$service) {
            return response()->json(['message' => 'Service not found'], 404);
        }

        $service->delete();

        return response()->json(['message' => 'Service deleted'], 200);
    }
}
