<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProjectGroupResource;
use App\Http\Resources\ProjectResource;
use App\Models\Customer;
use App\Models\Project;
use App\Models\ProjectGroup;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = ProjectResource::collection(Project::with('tasks')->latest()->paginate(30));

        return inertia('PMS/Project/Index', compact('projects'));
    }

    public function create()
    {
        $customers = Customer::all();
        $project_groups = ProjectGroupResource::collection(ProjectGroup::all());
        $tags = Tag::where('type', 'projects')->get();
        $users = User::where('is_active', true)->get();

        return inertia('PMS/Project/Create', compact('customers', 'project_groups', 'tags', 'users'));
    }

    public function store(Request $request)
    {
        //
    }

    public function show(Project $project)
    {
        //
    }

    public function edit(Project $project)
    {
        //
    }

    public function update(Request $request, Project $project)
    {
        //
    }

    public function destroy(Project $project)
    {
        //
    }
}
