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
            'name' => 'required|string|max:255', // Example validation
            'executive_table' => 'nullable|string|max:255', // Validation for executive table
        ]);

        // Create a new Building record
        Building::create([
            'name' => $request->input('name'),
            'executive_table' => $request->input('executive_table'),
        ]);

        return redirect()->route('buildings.index'); // Redirect to the list or wherever necessary
    }
}
