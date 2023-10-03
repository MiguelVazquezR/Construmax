<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PMSController extends Controller
{
    public function dashboard()
    {
        return inertia('PMS/Dashboard');
    }
}
