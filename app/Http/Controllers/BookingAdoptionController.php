<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\User;
use App\Models\Animal;
use Illuminate\Http\Request;

class BookingAdoptionController extends Controller
{
    public function home(){
        return view('booking-adoption.main');
    }

    // View all bookings
    public function index()
    {
        $bookings = Booking::with('user', 'animals')->get();
        return view('booking-adoption.index', compact('bookings'));
    }

    // Show form to create booking
    public function create()
    {
        $users = User::all();
        $animals = Animal::all();
        return view('booking-adoption.create', compact('users', 'animals'));
    }

    // Store booking
    public function store(Request $request)
    {
        $validated = $request->validate([
            'appointment_date' => 'required|date',
            'booking_time' => 'required',
            'status' => 'required|string',
            'userID' => 'required|exists:users,id',
            'animals' => 'required|array',
            'animals.*' => 'exists:animal,id',
        ]);

        $booking = Booking::create([
            'appointment_date' => $validated['appointment_date'],
            'booking_time' => $validated['booking_time'],
            'status' => $validated['status'],
            'userID' => $validated['userID'],
        ]);

        $booking->animals()->sync($validated['animals']);

        return redirect()->route('booking:index')->with('success', 'Booking created successfully!');
    }

    // Show edit form
    public function edit(Booking $booking)
    {
        $users = User::all();
        $animals = Animal::all();
        $booking->load('animals');
        return view('booking-adoption.edit', compact('booking', 'users', 'animals'));
    }

    // Update booking
    public function update(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'appointment_date' => 'required|date',
            'status' => 'required|string',
            'userID' => 'required|exists:users,id',
            'animals' => 'required|array',
            'animals.*' => 'exists:animal,id',
        ]);

        $booking->update([
            'appointment_date' => $validated['appointment_date'],
            'status' => $validated['status'],
            'userID' => $validated['userID'],
        ]);

        $booking->animals()->sync($validated['animals']);

        return redirect()->route('booking:index')->with('success', 'Booking updated successfully!');
    }

    // Delete booking
    public function destroy(Booking $booking)
    {
        $booking->animals()->detach();
        $booking->delete();
        return redirect()->route('booking:index')->with('success', 'Booking deleted successfully!');
    }

    // View single booking
    public function show(Booking $booking)
    {
        $booking->load('user', 'animals', 'adoption');
        return view('booking-adoption.show', compact('booking'));
    }
}
