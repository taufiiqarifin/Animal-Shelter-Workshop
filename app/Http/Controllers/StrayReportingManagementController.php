<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StrayReportingManagementController extends Controller
{
    public function home(){
        return view('stray-reporting.main');
    }
}
