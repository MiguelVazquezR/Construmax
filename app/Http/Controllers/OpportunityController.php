<?php

namespace App\Http\Controllers;

use App\Http\Resources\OpportunityResource;
use App\Models\Opportunity;
use Illuminate\Http\Request;

class OpportunityController extends Controller
{
    public function index()
    {
        $opportunities = OpportunityResource::collection(Opportunity::latest()->get());

        return inertia('CRM/Opportunity/Index', compact('opportunities'));
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
