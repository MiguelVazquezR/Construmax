<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProjectGroupResource;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\TagResource;
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
        $projects = ProjectResource::collection(Project::with(['tasks', 'owner'])->latest()->paginate(30));

        return inertia('PMS/Project/Index', compact('projects'));
    }

    public function create()
    {
        $customers = Customer::with(['opportunities'])->get();
        $project_groups = ProjectGroupResource::collection(ProjectGroup::all());
        $tags = TagResource::collection(Tag::where('type', 'projects')->get());
        $users = User::where('is_active', true)->get();

        return inertia('PMS/Project/Create', compact('customers', 'project_groups', 'tags', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'currency' => 'required|string|max:255',
            'address' => 'required|string',
            'invoice_type' => 'required|string|max:255',
            'is_strict' => 'boolean',
            'is_internal' => 'boolean',
            'budget' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'limit_date' => 'required|date',
            'project_group_id' => 'required|numeric|min:1',
            'user_id' => 'required|numeric|min:1',
            'opportunity_id' => 'required|numeric|min:1',
            'owner_id' => 'required|numeric|min:1',
        ]);

        $project = Project::create($validated);

        // permisos
        foreach ($request->selectedUsersToPermissions as $user) {
            $allowedUser = [
                "permissions" => json_encode($user['permissions']), // Serializa los permisos en formato JSON
            ];
            $project->users()->attach($user['id'], $allowedUser);
        }

        // archivos adjuntos
        $project->addAllMediaFromRequest()->each(fn ($file) => $file->toMediaCollection());

        return to_route('pms.projects.show', $project);
    }

    public function show(Project $project)
    {
        $project = ProjectResource::make(Project::with(['tasks' => ['participants', 'project', 'user'], 'projectGroup', 'opportunity.customer'])->find($project->id));
        $projects = ProjectResource::collection(Project::with(['tasks' => ['participants', 'project', 'user', 'comments.user', 'media'], 'user', 'opportunity.customer', 'projectGroup'])->latest()->get());
        $users = User::all();

        return inertia('PMS/Project/Show', compact(['project', 'projects', 'users']));
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
