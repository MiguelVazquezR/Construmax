<?php

namespace App\Http\Controllers;

use App\Models\Opportunity;
use Illuminate\Http\Request;

class OpportunityController extends Controller
{
    public function index()
    {
        return inertia('CRM/Opportunity/Index');
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(Opportunity $opportunity)
    {
        //
    }

    public function edit(Opportunity $opportunity)
    {
        //
    }

    public function update(Request $request, Opportunity $opportunity)
    {
        //
    }

    public function destroy(Opportunity $opportunity)
    {
        //
    }
}
