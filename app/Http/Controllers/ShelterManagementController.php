<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ShelterManagementController extends Controller
{
    public function home(){
        return view('shelter-management.main');
    }
}
