<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProjectGroupResource;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\TagResource;
use App\Models\Customer;
use App\Models\Opportunity;
use App\Models\Project;
use App\Models\ProjectGroup;
use App\Models\Tag;
use App\Models\User;
use App\Notifications\NewProjectNotification;
use App\Notifications\UpdatedProjectNotification;
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
        $customers = Customer::with(['opportunities.project', 'contacts'])->get();
        $project_groups = ProjectGroupResource::collection(ProjectGroup::all());
        $tags = TagResource::collection(Tag::where('type', 'projects')->get());
        $users = User::whereNotIn('id', [1])->where('is_active', true)->get();
        $opportunity = Opportunity::find(request('opportunityId'));

        // return $opportunity;

        return inertia('PMS/Project/Create', compact('customers', 'project_groups', 'tags', 'users', 'opportunity'));
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
            'budget' => 'required|numeric|min:0|max:999999.99',
            'start_date' => 'required|date',
            'limit_date' => 'required|date',
            'project_group_id' => 'required|numeric|min:1',
            'user_id' => 'required|numeric|min:1',
            'contact_id' => $request->is_internal ? 'nullable' : 'required|numeric|min:1',
            // 'opportunity_id' => [Rule::requiredIf(function () use ($request) {
            //     return !$request->input('is_internal');
            // })],
            'opportunity_id' => 'nullable',
            'owner_id' => 'required|numeric|min:1',
        ]);

        $project = Project::create($validated);

        // permisos
        foreach ($request->selectedUsersToPermissions as $user) {
            $permissions_array = array_map(function ($item) {
                // La función boolval() convierte un valor a booleano
                return boolval($item);
            }, $user['permissions']);
            $allowedUser = [
                "permissions" => json_encode($permissions_array), // Serializa los permisos en formato JSON
            ];
            $project->users()->attach($user['id'], $allowedUser);

            // notificar a usuarios que no sean el que crea el ticket
            $_user = User::find($user['id']);
            if ($_user->id !== auth()->id()) {
                $_user->notify(new NewProjectNotification($project, auth()->user()->name));
            }
        }

        // etiquetas
        // Obtiene los IDs de las etiquetas seleccionadas desde el formulario
        $tagIds = $request->input('tags', []);
        // Adjunta las etiquetas al ticket utilizando la relación polimórfica
        $project->tags()->attach($tagIds);

        // archivos adjuntos
        $project->addAllMediaFromRequest()->each(fn ($file) => $file->toMediaCollection());

        return to_route('pms.projects.show', $project);
    }

    public function show(Project $project)
    {
        $project = ProjectResource::make(Project::with(['tasks' => ['users', 'project', 'user', 'comments'], 'projectGroup', 'opportunity.customer', 'tags', 'users', 'owner', 'contact', 'user'])->find($project->id));
        $projects = Project::latest()->get(['id', 'name']);
        $users = User::all();
        $defaultTab = request('defaultTab');

        return inertia('PMS/Project/Show', compact(['project', 'users', 'defaultTab', 'projects']));
    }

    public function edit(Project $project)
    {
        $project = $project->fresh(['tags', 'opportunity.customer', 'owner', 'users']);
        $customers = Customer::with(['opportunities', 'contacts'])->get();
        $project_groups = ProjectGroupResource::collection(ProjectGroup::all());
        $tags = TagResource::collection(Tag::where('type', 'projects')->get());
        $users = User::whereNotIn('id', [1])->where('is_active', true)->get();
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
            'budget' => 'required|numeric|min:0|max:999999.99',
            'start_date' => 'required|date',
            'limit_date' => 'required|date',
            'project_group_id' => 'required|numeric|min:1',
            'user_id' => 'required|numeric|min:1',
            'contact_id' => $request->is_internal ? 'nullable' : 'required|numeric|min:1',
            // 'opportunity_id' => [Rule::requiredIf(function () use ($request) {
            //     return !$request->input('is_internal');
            // })],
            'opportunity_id' => 'nullable',
            'owner_id' => 'required|numeric|min:1',
        ]);


        $project->update($validated);

        // permisos
        // Eliminar todos los permisos actuales para el ticket
        $project->users()->detach();
        foreach ($request->selectedUsersToPermissions as $user) {
            $allowedUser = [
                "permissions" => json_encode($user['permissions']), // Serializa los permisos en formato JSON
            ];
            $project->users()->attach($user['id'], $allowedUser);

            // notificar a usuarios que no sean el que edita el ticket
            $_user = User::find($user['id']);
            if ($_user->id !== auth()->id()) {
                $_user->notify(new UpdatedProjectNotification($project, auth()->user()->name));
            }
        }

        // etiquetas
        // Obtiene los IDs de las etiquetas seleccionadas desde el formulario
        $tagIds = $request->input('tags', []);
        // Adjunta las etiquetas al ticket utilizando la relación polimórfica
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
            'budget' => 'required|numeric|min:0|max:999999.99',
            'start_date' => 'required|date',
            'limit_date' => 'required|date',
            'project_group_id' => 'required|numeric|min:1',
            'user_id' => 'required|numeric|min:1',
            'contact_id' => $request->is_internal ? 'nullable' : 'required|numeric|min:1',
            // 'opportunity_id' => [Rule::requiredIf(function () use ($request) {
            //     return !$request->input('is_internal');
            // })],
            'opportunity_id' => 'nullable',
            'owner_id' => 'required|numeric|min:1',
        ]);


        $project->update($validated);

        // permisos
        // Eliminar todos los permisos actuales para el ticket
        $project->users()->detach();
        foreach ($request->selectedUsersToPermissions as $user) {
            $permissions_array = array_map(function ($item) {
                // La función boolval() convierte un valor a booleano
                return boolval($item);
            }, $user['permissions']);
            $allowedUser = [
                "permissions" => json_encode($permissions_array), // Serializa los permisos en formato JSON
            ];
            $project->users()->attach($user['id'], $allowedUser);

            // notificar a usuarios que no sean el que edita el ticket
            $_user = User::find($user['id']);
            if ($_user->id !== auth()->id()) {
                $_user->notify(new UpdatedProjectNotification($project, auth()->user()->name));
            }
        }

        // etiquetas
        // Obtiene los IDs de las etiquetas seleccionadas desde el formulario
        $tagIds = $request->input('tags', []);
        // Adjunta las etiquetas al ticket utilizando la relación polimórfica
        $project->tags()->sync($tagIds);

        // archivos adjuntos
        $project->addAllMediaFromRequest()->each(fn ($file) => $file->toMediaCollection());

        return to_route('pms.projects.show', $project);
        //event(new RecordEdited($project));
    }

    public function destroy(Project $project)
    {
        // eliminar tareas y comentarios
        $tasks = $project->tasks;
        foreach ($tasks as $task) {
            $task->comments()->delete();
            $task->delete();
        }

        // eliminar ticket
        $project->delete();

        return to_route('pms.projects.index');
    }

    public function getSelectedItem($project_id)
    {
        $project = ProjectResource::make(Project::with(['tasks' => ['users', 'project', 'user'], 'projectGroup', 'opportunity.customer', 'tags', 'users', 'owner', 'contact'])->find($project_id));

        return response()->json(['item' => $project]);
    }
}
