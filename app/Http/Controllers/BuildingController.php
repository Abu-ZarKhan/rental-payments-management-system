<?php

// app/Http/Controllers/BuildingController.php

namespace App\Http\Controllers;

use App\Models\Building;
use Illuminate\Http\Request;

class BuildingController extends Controller
{
    public function create()
    {
        return view('buildings.create');
    }

    public function store(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'name' => 'required|string|max:255', // Validation for name
            'executive_table' => 'nullable|string|max:255', // Validation for executive_table
            'executive_chair' => 'nullable|string|max:255', // Validation for executive_chair
        ]);

        // Create a new Building record
        Building::create([
            'name' => $request->input('name'),
            'executive_table' => $request->input('executive_table'),
            'executive_chair' => $request->input('executive_chair'), // Save executive_chair
        ]);

        return redirect()->route('buildings.index'); // Redirect to the list or wherever necessary
    }
}
