<?php

namespace App\Http\Controllers\api\medicine;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use Illuminate\Http\Request;

class medicineController extends Controller
{
public function index(Request $request)
{
    $page = $request->query('page', 1);
    $search = $request->query('search'); // search query parameter

    // Query builder
    $query = Medicine::query();

    if ($search) {
        $query->where(function($q) use ($search) {
            $q->where('medicine_name', 'like', "%{$search}%")
              ->orWhere('generic_name', 'like', "%{$search}%");
        });
    }

    // Pagination, 10 per page
    $medicines = $query->paginate(10, ['*'], 'page', $page);

    return response()->json([
        'status' => true,
        'message' => 'Medicines retrieved successfully',
        'data' => [
            'medicines' => $medicines->items(),
            'current_page' => $medicines->currentPage(),
            'last_page' => $medicines->lastPage(),
            'total' => $medicines->total(),
            'per_page' => $medicines->perPage(),
        ]
    ], 200);
    }
    public function store(Request $request)
    {
        $medicine = Medicine::create($request->all());
        return response()->json($medicine, 201);
    }

    // Show single medicine
    public function show($id)
    {
        $medicine = Medicine::findOrFail($id);
        return response()->json($medicine);
    }


}
