<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Slot;  
use App\Models\Image;  
use App\Models\Rescue; 
use Illuminate\Support\Facades\Auth;

class AnimalManagementController extends Controller
{
    public function home(){
        return view('animal-management.main');
    }
}
