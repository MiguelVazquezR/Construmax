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
use Illuminate\Validation\Rule;
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
        $customers = Customer::with(['opportunities', 'contacts'])->get();
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
            'address' => [Rule::requiredIf(function () use ($request) {
                return !$request->input('is_internal');
            })],
            'service_type' => 'required|string',
            'is_strict' => 'boolean',
            'is_internal' => 'boolean',
            'budget' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'limit_date' => 'required|date',
            'project_group_id' => 'required|numeric|min:1',
            'user_id' => 'required|numeric|min:1',
            'opportunity_id' => [Rule::requiredIf(function () use ($request) {
                return !$request->input('is_internal');
            })],
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

        // etiquetas
        // Obtiene los IDs de las etiquetas seleccionadas desde el formulario
        $tagIds = $request->input('tags', []);
        // Adjunta las etiquetas al proyecto utilizando la relación polimórfica
        $project->tags()->attach($tagIds);

        // archivos adjuntos
        $project->addAllMediaFromRequest()->each(fn ($file) => $file->toMediaCollection());

        return to_route('pms.projects.show', $project);
    }

    public function show(Project $project)
    {
        $project = ProjectResource::make(Project::with(['tasks' => ['users', 'project', 'user'], 'projectGroup', 'opportunity.customer', 'tags', 'users', 'owner'])->find($project->id));
        $projects = ProjectResource::collection(Project::with(['tasks' => ['users', 'project', 'user', 'comments.user', 'media'], 'user', 'users', 'opportunity.customer', 'projectGroup', 'tags', 'owner'])->latest()->get());
        $users = User::all();
        $defaultTab = request('defaultTab');

        return inertia('PMS/Project/Show', compact(['project', 'projects', 'users', 'defaultTab']));
    }

    public function edit(Project $project)
    {
        $project = $project->fresh(['tags', 'opportunity.customer', 'owner']);
        $customers = Customer::with(['opportunities'])->get();
        $project_groups = ProjectGroupResource::collection(ProjectGroup::all());
        $tags = TagResource::collection(Tag::where('type', 'projects')->get());
        $users = User::where('is_active', true)->get();
        $media = $project->getMedia()->all();

        return inertia('PMS/Project/Edit', compact('customers', 'project_groups', 'tags', 'users', 'project', 'media'));
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'currency' => 'required|string|max:255',
            'address' => [Rule::requiredIf(function () use ($request) {
                return !$request->input('is_internal');
            })],
            'service_type' => 'required|string',
            'is_strict' => 'boolean',
            'is_internal' => 'boolean',
            'budget' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'limit_date' => 'required|date',
            'project_group_id' => 'required|numeric|min:1',
            'user_id' => 'required|numeric|min:1',
            'opportunity_id' => [Rule::requiredIf(function () use ($request) {
                return !$request->input('is_internal');
            })],
            'owner_id' => 'required|numeric|min:1',
        ]);


        $project->update($validated);

        // // permisos
        // foreach ($request->selectedUsersToPermissions as $user) {
        //     $allowedUser = [
        //         "permissions" => json_encode($user['permissions']), // Serializa los permisos en formato JSON
        //     ];
        //     $project->users()->attach($user['id'], $allowedUser);
        // }

        // etiquetas
        // Obtiene los IDs de las etiquetas seleccionadas desde el formulario
        $tagIds = $request->input('tags', []);
        // Adjunta las etiquetas al proyecto utilizando la relación polimórfica
        $project->tags()->sync($tagIds);

        return to_route('pms.projects.show', $project);
        //event(new RecordEdited($project));
    }

    public function updateWithMedia(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'currency' => 'required|string|max:255',
            'address' => [Rule::requiredIf(function () use ($request) {
                return !$request->input('is_internal');
            })],
            'service_type' => 'required|string',
            'is_strict' => 'boolean',
            'is_internal' => 'boolean',
            'budget' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'limit_date' => 'required|date',
            'project_group_id' => 'required|numeric|min:1',
            'user_id' => 'required|numeric|min:1',
            'opportunity_id' => [Rule::requiredIf(function () use ($request) {
                return !$request->input('is_internal');
            })],
            'owner_id' => 'required|numeric|min:1',
        ]);


        $project->update($validated);

        // // permisos
        // foreach ($request->selectedUsersToPermissions as $user) {
        //     $allowedUser = [
        //         "permissions" => json_encode($user['permissions']), // Serializa los permisos en formato JSON
        //     ];
        //     $project->users()->attach($user['id'], $allowedUser);
        // }

        // etiquetas
        // Obtiene los IDs de las etiquetas seleccionadas desde el formulario
        $tagIds = $request->input('tags', []);
        // Adjunta las etiquetas al proyecto utilizando la relación polimórfica
        $project->tags()->sync($tagIds);

        // archivos adjuntos
        $project->addAllMediaFromRequest()->each(fn ($file) => $file->toMediaCollection());

        return to_route('pms.projects.show', $project);
        //event(new RecordEdited($project));
    }

    public function destroy(Project $project)
    {
        //
    }
}
