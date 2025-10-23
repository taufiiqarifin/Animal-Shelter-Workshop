<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BookingAdoptionController extends Controller
{
    public function home(){
        return view('booking-adoption.main');
    }
}
