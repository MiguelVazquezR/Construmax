<?php

namespace App\Http\Controllers;

use App\Models\ProjectGroup;
use Illuminate\Http\Request;

class ProjectGroupController extends Controller
{
    public function index()
    {
        //
    }

    public function create()
    {
        
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        ProjectGroup::create($request->all() + ['user_id' => auth()->id()]);

        return response()->json(['message' => 'Se ha creado un nuevo grupo']);
    }

    public function show(ProjectGroup $projectGroup)
    {
        //
    }

    public function edit(ProjectGroup $projectGroup)
    {
        //
    }

    public function update(Request $request, ProjectGroup $projectGroup)
    {
        //
    }

    public function destroy(ProjectGroup $projectGroup)
    {
        //
    }
}
