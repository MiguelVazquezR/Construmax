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
        $opportunities = OpportunityResource::collection(Opportunity::latest()->get());
        
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
            'close_date' => 'nullable|date',
            // 'type_access_project' => 'required|string',
            'lost_oportunity_razon' => $request->status === 'Perdida' ? 'required' : 'nullable',
            'contact' => 'required|string',
            'company_id' => $request->is_new_company ? 'nullable' : 'required',
        ]);

        if ($request->status == 'Cerrada') {

            $opportunity = Opportunity::create($validated + ['user_id' => auth()->id(), 'finished_at' => now()]);
        } else {

            $opportunity = Opportunity::create($validated + ['user_id' => auth()->id()]);
        }

        // permisos
        foreach ($request->selectedUsersToPermissions as $user) {
            $allowedUser = [
                "permissions" => json_encode($user['permissions']), // Serializa los permisos en formato JSON
            ];
            $opportunity->users()->attach($user['id'], $allowedUser);
        }

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
