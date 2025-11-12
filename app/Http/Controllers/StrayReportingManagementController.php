<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;  // ✅ this line is very important
use App\Models\Image; 
use Illuminate\Support\Facades\Auth;

class StrayReportingManagementController extends Controller
{
    public function home(){
        return view('stray-reporting.main');
    }


public function create()
    {
        return view('stray-reporting.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'latitude' => 'required',
            'longitude' => 'required',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'report_status' => 'required|string|max:50',
            'description' => 'nullable|string',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Create report
        $report = Report::create([
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'report_status' => $request->report_status,
            'description' => $request->description,
            'userID' => Auth::id(),
        ]);

        // Upload multiple images (optional)
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $photo) {
                $imagePath = $photo->store('report_images', 'public');
                Image::create([
                    'reportID' => $report->id,
                    'image_path' => $imagePath,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Report successfully added!');
    }

}