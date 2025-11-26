<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Animal; // ✅ Import the model here

class AnimalManagementController extends Controller
{
    public function home(){
        return view('animal-management.main');
    }
    public function index()
    {
        $animals = Animal::all();
        return view('animal-management.index', compact('animals'));
    }

    // Show form to create new animal
    public function create()
    {
        return view('animal-management.create');
    }

    // Store new animal
    public function store(Request $request)
    {
        $data = $request->validate([
            'species' => 'required|string|max:255',
            'health_details' => 'nullable|string',
            'age' => 'nullable|integer',
            'gender' => 'nullable|string',
            'adoption_status' => 'nullable|string',
            'arrival_date' => 'nullable|date',
            'medical_status' => 'nullable|string',
            'rescueID' => 'nullable|integer',
            'slotID' => 'nullable|integer',
            'vaccinationID' => 'nullable|integer',
        ]);

        Animal::create($data);
        return redirect()->route('animals.index')->with('success', 'Animal created successfully!');
    }

    // Show single animal details
    public function show(Animal $animal)
    {
        return view('animal-management.show', compact('animal'));
    }

    // Show form to edit an animal
    public function edit(Animal $animal)
    {
        return view('animal-management.edit', compact('animal'));
    }

    // Update an animal
    public function update(Request $request, Animal $animal)
    {
        $data = $request->validate([
            'species' => 'required|string|max:255',
            'health_details' => 'nullable|string',
            'age' => 'nullable|integer',
            'gender' => 'nullable|string',
            'adoption_status' => 'nullable|string',
            'arrival_date' => 'nullable|date',
            'medical_status' => 'nullable|string',
            'rescueID' => 'nullable|integer',
            'slotID' => 'nullable|integer',
            'vaccinationID' => 'nullable|integer',
        ]);

        $animal->update($data);
        return redirect()->route('animals.index')->with('success', 'Animal updated successfully!');
    }

    // Delete an animal
    public function destroy(Animal $animal)
    {
        $animal->delete();
        return redirect()->route('animals.index')->with('success', 'Animal deleted successfully!');
    }
    

}
