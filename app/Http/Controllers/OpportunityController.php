<?php

namespace App\Http\Controllers;

use App\Http\Resources\CustomerResource;
use App\Http\Resources\OpportunityResource;
use App\Http\Resources\TagResource;
use App\Models\Customer;
use App\Models\Opportunity;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;

class OpportunityController extends Controller
{
    public function index()
    {
        $opportunities = OpportunityResource::collection(Opportunity::with('contact', 'opportunityTasks')->latest()->get());
        
        // return $opportunities; 

        return inertia('CRM/Opportunity/Index', compact('opportunities'));
    }

    public function create()
    {
        $users = User::all();
        $tags = TagResource::collection(Tag::where('type', 'opportunities')->get());
        $customers = CustomerResource::collection(Customer::with('contacts')->latest()->get());

        // return $customers;

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
            'amount' => 'required|numeric|min:0',
            'priority' => 'required|string',
            'start_date' => 'required|date',
            'close_date' => 'required|date|after:start_date',
            'service_type' => 'required|string',
            // 'type_access_project' => 'required|string',
            'lost_oportunity_razon' => $request->status === 'Perdida' ? 'required' : 'nullable',
            'contact_id' => $request->is_new_company ? 'nullable' : 'required',
            'customer_id' => $request->is_new_company ? 'nullable' : 'required',
            'customer_name' => $request->is_new_company ? 'required' : 'nullable',
            'branch' => $request->is_new_company ? 'nullable' : 'required',
            'contact_name' => $request->is_new_company ? 'required' : 'nullable',
            'contact_phone' => $request->is_new_company ? 'required' : 'nullable',
        ]);

        if ($request->status == 'Cerrada') {

            $opportunity = Opportunity::create($validated + ['user_id' => auth()->id(), 'finished_at' => now()]);
        } else {

            $opportunity = Opportunity::create($validated + ['user_id' => auth()->id()]);
        }

        // permisos
        // foreach ($request->selectedUsersToPermissions as $user) {
        //     $allowedUser = [
        //         "permissions" => json_encode($user['permissions']), // Serializa los permisos en formato JSON
        //     ];
        //     $opportunity->users()->attach($user['id'], $allowedUser);
        // }

        // etiquetas
        // Obtiene los IDs de las etiquetas seleccionadas desde el formulario
        $tagIds = $request->input('tags', []);
        // Adjunta las etiquetas al proyecto utilizando la relación polimórfica
        $opportunity->tags()->attach($tagIds);

        // archivos adjuntos
        $opportunity->addAllMediaFromRequest()->each(fn ($file) => $file->toMediaCollection());

        return to_route('crm.opportunities.index');
    }

    public function show(Opportunity $opportunity)
    {
        $opportunities = OpportunityResource::collection(Opportunity::with(['contact', 'tags', 'media', 'user', 'seller','clientMonitors' => ['emailMonitor', 'paymentMonitor', 'meetingMonitor', 'seller'], 'opportunityTasks' => ['asigned', 'media', 'opportunity', 'user', 'comments.user']])->latest()->get());

        // return $opportunities;

        return inertia('CRM/Opportunity/Show', compact('opportunity', 'opportunities'));
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
        $opportunity->delete();
    }

    public function updateStatus(Request $request, $opportunity_id)
    {
        $opportunity = Opportunity::find($opportunity_id);

        if ($request->status == 'Cerrada') {
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
        } elseif ($request->status == 'Perdida') {
            $opportunity->update([
                'status' => $request->status,
                'finished_at' => null,
                'paid_at' => null,
                'lost_oportunity_razon' => $request->lost_oportunity_razon,
            ]);
        }
        else {
            $opportunity->update([
                'status' => $request->status,
                'finished_at' => null,
                'paid_at' => null,
                'lost_oportunity_razon' => null,
            ]);
        }

        return response()->json(['item' => OpportunityResource::make($opportunity)]);
    }
}
