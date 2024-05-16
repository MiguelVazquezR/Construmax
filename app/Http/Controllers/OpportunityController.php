<?php

namespace App\Http\Controllers;

use App\Http\Resources\CustomerResource;
use App\Http\Resources\OpportunityResource;
use App\Http\Resources\TagResource;
use App\Models\Activity;
use App\Models\Customer;
use App\Models\Opportunity;
use App\Models\OpportunityTask;
use App\Models\Project;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;

class OpportunityController extends Controller
{
    public function index()
    {
        $all_opportunities = Opportunity::with('customer:id,name')->latest()->get();
        $opportunities = OpportunityResource::collection($all_opportunities->take(30));

        return inertia('CRM/Opportunity/Index', [
            'opportunities' => $opportunities,
            'total_opportunities' => $all_opportunities->count(),
        ]);
    }

    public function create()
    {
        $users = User::whereNotIn('id', [1])->get();
        $tags = TagResource::collection(Tag::where('type', 'opportunities')->get());
        $customers = CustomerResource::collection(Customer::with('contacts')->latest()->get());

        return inertia('CRM/Opportunity/Create', compact('users', 'tags', 'customers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'status' => 'required|string',
            'description' => 'nullable',
            'seller_id' => 'required',
            'tags' => 'nullable|array',
            'probability' => 'nullable|numeric|min:0|max:100',
            // 'amount' => 'required|numeric|min:0|max:99999999.99',
            'budgets' => 'nullable|array|min:1',
            'priority' => 'required|string',
            'start_date' => 'required|date',
            'close_date' => 'required|date|after:start_date',
            'service_type' => 'required|string',
            'lost_oportunity_razon' => $request->status === 'Perdida' ? 'required' : 'nullable',
            'contact_id' => $request->is_new_company ? 'nullable' : 'required',
            'customer_id' => $request->is_new_company ? 'nullable' : 'required',
            'customer_name' => $request->is_new_company ? 'required' : 'nullable',
            'branch' => $request->is_new_company ? 'nullable' : 'required',
            'contact_name' => $request->is_new_company ? 'required' : 'nullable',
            'contact_phone' => $request->is_new_company ? 'required' : 'nullable',
        ]);

        // if ($request->status == 'Cerrada') {
        //     $opportunity = Opportunity::create($validated + ['user_id' => auth()->id(), 'finished_at' => now()]);
        // } else {
        $validated['amount'] = collect($validated['budgets'])->sum('amount');
        $opportunity = Opportunity::create($validated + ['user_id' => auth()->id()]);
        // }

        // permisos
        foreach ($request->selectedUsersToPermissions as $user) {
            $permissions_array = array_map(function ($item) {
                // La función boolval() convierte un valor a booleano
                return boolval($item);
            }, $user['permissions']);
            $allowedUser = [
                "permissions" => json_encode($permissions_array), // Serializa los permisos en formato JSON
            ];
            $opportunity->users()->attach($user['id'], $allowedUser);
        }

        // etiquetas
        // Obtiene los IDs de las etiquetas seleccionadas desde el formulario
        $tagIds = $request->input('tags', []);
        // Adjunta las etiquetas al ticket utilizando la relación polimórfica
        $opportunity->tags()->attach($tagIds);

        // archivos adjuntos ----------
        $opportunity->addAllMediaFromRequest()->each(fn ($file) => $file->toMediaCollection());

        $time = \Carbon\Carbon::createFromFormat('h A', '7 PM')->format('H:i:s'); //tiempo limite de realización de tarea en formato am y pm
        //Tarea 1. Contacto al cliente
        OpportunityTask::create([
            'name' => 'Contacto al cliente',
            'limit_date' => now()->addDays(2),
            'time' =>  $time,
            'finished_at' => null,
            'description' => 'Tener contacto con el cliente',
            'priority' => 'Media',
            'reminder' => null,
            'user_id' => auth()->id(),
            'opportunity_id' => $opportunity->id,
            'asigned_id' => $request->seller_id,
        ]);
        //Tarea 2. Cotizar
        OpportunityTask::create([
            'name' => 'Cotizar',
            'limit_date' => now()->addDays(4),
            'time' =>  $time,
            'finished_at' => null,
            'description' => 'Hacer cotización',
            'priority' => 'Media',
            'reminder' => null,
            'user_id' => auth()->id(),
            'opportunity_id' => $opportunity->id,
            'asigned_id' => $request->seller_id,
        ]);
        //Tarea 3. Enviar cotización
        OpportunityTask::create([
            'name' => 'Enviar cotización',
            'limit_date' => now()->addDays(6),
            'time' =>  $time,
            'finished_at' => null,
            'description' => 'Enviar cotización al cliente',
            'priority' => 'Media',
            'reminder' => null,
            'user_id' => auth()->id(),
            'opportunity_id' => $opportunity->id,
            'asigned_id' => $request->seller_id,
        ]);

        //Crea el registro de una actividad para el historial de ese presupuesto --------------------------
        Activity::create([
            'description' => 'creó el presupuesto',
            'user_id' => auth()->id(),
            'opportunity_id' => $opportunity->id,
        ]);

        return to_route('crm.opportunities.index');
    }

    public function show(Opportunity $opportunity)
    {
        $opportunity = OpportunityResource::make($opportunity->fresh(['contact', 'tags', 'media', 'users', 'user', 'seller', 'survey', 'activities' => ['user'], 'clientMonitors' => ['emailMonitor', 'paymentMonitor', 'meetingMonitor', 'seller'], 'opportunityTasks' => ['asigned', 'media', 'opportunity', 'user', 'comments.user']]));
        $opportunities = Opportunity::latest()->get(['id', 'name']);
        $defaultTab = request('defaultTab');

        return inertia('CRM/Opportunity/Show', compact('opportunity', 'opportunities', 'defaultTab'));
    }

    public function edit(Opportunity $opportunity)
    {
        $opportunity = $opportunity->fresh(['tags', 'users']);
        $users = User::whereNotIn('id', [1])->get();
        $tags = TagResource::collection(Tag::where('type', 'opportunities')->get());
        $customers = CustomerResource::collection(Customer::with('contacts')->latest()->get());

        return inertia('CRM/Opportunity/Edit', compact('users', 'tags', 'customers', 'opportunity'));
    }

    public function update(Request $request, Opportunity $opportunity)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'status' => 'required|string',
            'description' => 'nullable',
            'seller_id' => 'required',
            'tags' => 'nullable|array',
            'probability' => 'nullable|numeric|min:0|max:100',
            // 'amount' => 'required|numeric|min:0|max:99999999.99',
            'budgets' => 'nullable|array|min:1',
            'priority' => 'required|string',
            'start_date' => 'required|date',
            'close_date' => 'required|date|after:start_date',
            'service_type' => 'required|string',
            'lost_oportunity_razon' => $request->status === 'Perdida' ? 'required' : 'nullable',
            'contact_id' => $request->is_new_company ? 'nullable' : 'required',
            'customer_id' => $request->is_new_company ? 'nullable' : 'required',
            'customer_name' => $request->is_new_company ? 'required' : 'nullable',
            'branch' => $request->is_new_company ? 'nullable' : 'required',
            'contact_name' => $request->is_new_company ? 'required' : 'nullable',
            'contact_phone' => $request->is_new_company ? 'required' : 'nullable',
        ]);

        // if ($request->status == 'Cerrada' || $request->status == 'Pagado') {
        //     $opportunity->update($validated + ['finished_at' => now()]);
        // } else {
        $validated['amount'] = collect($validated['budgets'])->sum('amount');
        $opportunity->update($validated);
        // }

        // permisos
        // Eliminar todos los permisos actuales para el presupuesto
        $opportunity->users()->detach();
        foreach ($request->selectedUsersToPermissions as $user) {
            $allowedUser = [
                "permissions" => json_encode($user['permissions']), // Serializa los permisos en formato JSON
            ];
            $opportunity->users()->attach($user['id'], $allowedUser);
        }

        // etiquetas
        // Obtiene los IDs de las etiquetas seleccionadas desde el formulario
        $tagIds = $request->input('tags', []);
        // Adjunta las etiquetas a el presupuesto utilizando la relación polimórfica
        $opportunity->tags()->sync($tagIds);

        return to_route('crm.opportunities.show', $opportunity->id);
    }

    public function updateWithMedia(Request $request, Opportunity $opportunity)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'status' => 'required|string',
            'description' => 'nullable',
            'seller_id' => 'required',
            'tags' => 'nullable|array',
            'probability' => 'nullable|numeric|min:0|max:100',
            // 'amount' => 'required|numeric|min:0|max:99999999.99',
            'budgets' => 'nullable|array|min:1',
            'priority' => 'required|string',
            'start_date' => 'required|date',
            'close_date' => 'required|date|after:start_date',
            'service_type' => 'required|string',
            'lost_oportunity_razon' => $request->status === 'Perdida' ? 'required' : 'nullable',
            'contact_id' => $request->is_new_company ? 'nullable' : 'required',
            'customer_id' => $request->is_new_company ? 'nullable' : 'required',
            'customer_name' => $request->is_new_company ? 'required' : 'nullable',
            'branch' => $request->is_new_company ? 'nullable' : 'required',
            'contact_name' => $request->is_new_company ? 'required' : 'nullable',
            'contact_phone' => $request->is_new_company ? 'required' : 'nullable',
        ]);

        // if ($request->status == 'Cerrada' || $request->status == 'Pagado') {
        //     $opportunity->update($validated + ['finished_at' => now()]);
        // } else {
        $validated['amount'] = collect($validated['budgets'])->sum('amount');
        $opportunity->update($validated);
        // }

        // permisos
        // Eliminar todos los permisos actuales para el presupuesto
        $opportunity->users()->detach();
        foreach ($request->selectedUsersToPermissions as $user) {
            $permissions_array = array_map(function ($item) {
                // La función boolval() convierte un valor a booleano
                return boolval($item);
            }, $user['permissions']);
            $allowedUser = [
                "permissions" => json_encode($permissions_array), // Serializa los permisos en formato JSON
            ];
            $opportunity->users()->attach($user['id'], $allowedUser);
        }

        // etiquetas
        // Obtiene los IDs de las etiquetas seleccionadas desde el formulario
        $tagIds = $request->input('tags', []);
        // Adjunta las etiquetas a el presupuesto utilizando la relación polimórfica
        $opportunity->tags()->sync($tagIds);

        // archivos adjuntos
        $opportunity->addAllMediaFromRequest()->each(fn ($file) => $file->toMediaCollection());

        return to_route('crm.opportunities.show', $opportunity->id);
    }

    public function destroy(Opportunity $opportunity)
    {
        // eliminar tareas y comentarios de tickets
        $tasks = $opportunity->project?->tasks ?? [];
        foreach ($tasks as $task) {
            $task->comments()->delete();
            $task->delete();
        }

        // eliminar actividades y comentarios de presupuesto
        $tasks = $opportunity->opportunityTasks;
        foreach ($tasks as $task) {
            $task->comments()->delete();
            $task->delete();
        }

        // eliminar presupuesto
        $opportunity->delete();

        return to_route('crm.opportunities.index');
    }

    public function updateStatus(Request $request, $opportunity_id)
    {
        $opportunity = Opportunity::find($opportunity_id);

        if ($request->status == 'Facturado') {
            $opportunity->update([
                'status' => $request->status,
                'finished_at' => now(),
                'paid_at' => null,
                'lost_oportunity_razon' => null,
            ]);
        } elseif ($request->status == 'Pagado') {
            $opportunity->update([
                'status' => $request->status,
                'paid_at' => now(),
                'lost_oportunity_razon' => null,
            ]);
        } elseif ($request->status == "Perdido") {
            $opportunity->update([
                'status' => $request->status,
                'finished_at' => null,
                'paid_at' => null,
                'lost_oportunity_razon' => $request->lost_oportunity_razon,
            ]);
        } else {
            $opportunity->update([
                'status' => $request->status,
                'finished_at' => null,
                'paid_at' => null,
                'lost_oportunity_razon' => null,
            ]);
        }

        //Crea el registro de una actividad para el historial de ese presupuesto
        Activity::create([
            'description' => 'cambió el estatus de el presupuesto a "' . $request->status . '"',
            'user_id' => auth()->id(),
            'opportunity_id' => $opportunity->id,
        ]);

        $opportunity->load('activities.user'); //carga la relación de activities

        return response()->json(['item' => OpportunityResource::make($opportunity)]);
    }

    //-----------Revisa si el presupuesto ya tiene una orden de venta creada, si no la tiene, redirecciona al formulario para crearla
    public function createProject(Request $request, $opportunity_id)
    {
        $opportunity = Opportunity::find($opportunity_id);

        $project = Project::where('opportunity_id', $opportunity_id)->first(); //Busca un ticket de este presupuesto

        if ($project != null) {
            return response()->json(['message' => 'Ya existe un ticket de este presupuesto']);
        }
    }

    public function getItemsByPage($currentPage)
    {
        $offset = $currentPage * 30;

        // Consulta para obtener presupuestos en los que el usuario está involucrado
        $opportunities = Opportunity::with('customer:id,name')
            ->latest()
            ->get()
            ->splice($offset)
            ->take(30);

        $opportunities = OpportunityResource::collection($opportunities);

        return response()->json(['items' => $opportunities]);
    }
}
