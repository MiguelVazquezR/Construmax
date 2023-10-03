<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CRMController extends Controller
{
    public function dashboard()
    {
        return inertia('CRM/Dashboard');
    }
}
