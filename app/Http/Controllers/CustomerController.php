<?php

namespace App\Http\Controllers;

use App\Http\Resources\CustomerResource;
use App\Http\Resources\TagResource;
use App\Models\Contact;
use App\Models\Customer;
use App\Models\Tag;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = CustomerResource::collection(Customer::with(['contacts'])->latest()->get());

        return inertia('CRM/Customer/Index', compact('customers'));
    }

    public function create()
    {
        $tags = TagResource::collection(Tag::where('type', 'crm')->get());

        return inertia('CRM/Customer/Create', compact('tags'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'rfc' => 'required|string',
            'contacts' => 'array|min:1',
            'invoicing_method' => 'required',
            'currency' => 'required',
            'payment_method' => 'required',
            'invoice_use' => 'required',
        ]);

        $customer = Customer::create($request->all() + ['user_id' => auth()->id()]);

        foreach ($request->input('contacts') as $contact) {
            //relacionar contacto con cliente
            $contact = new Contact([
                'name' => $contact['name'],
                'email' => $contact['email'],
                'phone' => $contact['phone'],
                'position' => $contact['position'],
                'additional' => ['branches' => $contact['branches']],
                'user_id' => auth()->id(),
            ]);
            $customer->contacts()->save($contact);
        }

        // etiquetas
        // Obtiene los IDs de las etiquetas seleccionadas desde el formulario
        $tagIds = $request->input('tags', []);
        // Adjunta las etiquetas al cliente utilizando la relación polimórfica
        $customer->tags()->attach($tagIds);


        return to_route('crm.customers.index');
    }

    public function show(Customer $customer)
    {
        $customers = CustomerResource::collection(Customer::with(['user', 'tags', 'contacts', 'opportunities', 'clientMonitors' => ['emailMonitor', 'paymentMonitor', 'meetingMonitor', 'seller']])->latest()->get());

        return inertia('CRM/Customer/Show', compact('customer', 'customers'));
    }

    public function edit(Customer $customer)
    {
        $tags = TagResource::collection(Tag::where('type', 'crm')->get());
        $customer = $customer->fresh(['contacts', 'tags']);

        return inertia('CRM/Customer/Edit', compact('customer', 'tags'));
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name' => 'required|string',
            'rfc' => 'required|string',
            'contacts' => 'required|array|min:1',
            'invoicing_method' => 'required',
            'payment_method' => 'required',
            'invoice_use' => 'required',
        ]);

        $customer->update($request->all());

        // etiquetas
        // Obtiene los IDs de las etiquetas seleccionadas desde el formulario
        $tagIds = $request->input('tags', []);
        // Adjunta las etiquetas al cliente utilizando la relación polimórfica
        $customer->tags()->sync($tagIds);

        // Obtén los IDs de los contactos proporcionados en el formulario
        $existingContactIds = collect($request->input('contacts'))->pluck('id')->toArray();
        // Recorre los contactos proporcionados en el formulario
        foreach ($request->input('contacts') as $contactData) {
            $contact = null;

            // Comprueba si el contacto ya existe por ID
            if (isset($contactData['id'])) {
                $contact = Contact::find($contactData['id']);
            }

            // Si el contacto no existe, crea uno nuevo
            if (!$contact) {
                $contact = new Contact([
                    'user_id' => auth()->id(),
                ]);
            }

            // Actualiza los datos del contacto
            $contact->fill([
                'name' => $contactData['name'],
                'email' => $contactData['email'],
                'phone' => $contactData['phone'],
                'position' => $contactData['position'],
                'additional' => ['branches' => $contactData['branches']],
            ]);

            // Guarda el contacto en la relación
            $customer->contacts()->save($contact);
        }

        // Elimina los contactos que no están en el formulario
        $customer->contacts()->whereNotIn('id', $existingContactIds)->delete();

        return to_route('crm.customers.show', $customer->id);
    }

    public function destroy(Customer $customer)
    {
        //
    }
}
