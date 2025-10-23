<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AnimalManagementController extends Controller
{
    public function home(){
        return view('animal-management.main');
    }
}
