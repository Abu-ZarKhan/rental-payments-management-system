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
            'guest_chair' => 'nullable|string|max:255',
            'staff_workstations' => 'nullable|string|max:255',
            'staff_chairs' => 'nullable|string|max:255',
            'cabinet' => 'nullable|string|max:255',
            'conference_room' => 'nullable|string|max:255',
            'sofa' => 'nullable|string|max:255',
            'cleaning' => 'nullable|string|max:255',
            'parking' => 'nullable|string|max:255',
            'drinking_water' => 'nullable|string|max:255',
            'electricity' => 'nullable|string|max:255',
            'internet' => 'nullable|string|max:255',
            'refreshment_tea_coffee' => 'nullable|string|max:255',
        ]);

        // Create a new Building record
        Building::create([
            'name' => $request->input('name'),
            'executive_table' => $request->input('executive_table'),
            'executive_chair' => $request->input('executive_chair'), // Save executive_chair
            'guest_chair' => $request->input('guest_chair'),
            'staff_workstations' => $request->input('staff_workstations'),
            'staff_chairs' => $request->input('staff_chairs'),
            'cabinet' => $request->input('cabinet'),
            'conference_room' => $request->input('conference_room'),
            'sofa' => $request->input('sofa'),
            'cleaning' => $request->input('cleaning'),
            'parking' => $request->input('parking'),
            'drinking_water' => $request->input('drinking_water'),
            'electricity' => $request->input('electricity'),
            'internet' => $request->input('internet'),
            'refreshment_tea_coffee' => $request->input('refreshment_tea_coffee'),
        ]);

        return redirect()->route('buildings.index'); // Redirect to the list or wherever necessary
    }
}
