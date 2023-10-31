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
        $customers = CustomerResource::collection(Customer::latest()->get());

    //return $customers;

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
            'invoicing_method' => 'required',
            'payment_method' => 'required',
            'invoice_use' => 'required',
        ]);

       $customer = Customer::create($request->all() + ['user_id' => auth()->id()]);

       foreach ($$request->input('contacts') as $contact) {
           //relacionar contacto con cliente
           $contact = new Contact([
               'name' => $contact['name'],
               'email' => $contact['email'],
               'phone' => $contact['phone'],
               'position' => $contact['position'],
               'additional' => ['branches' => $contact['branches']],
           ]);
           $customer->contacts()->save($contact);
       }
       
        return to_route('crm.customers.index');
    }

    public function show(Customer $customer)
    {
        $customers = CustomerResource::collection(Customer::with('user')->latest()->get());

        //return $customers;

        return inertia('CRM/Customer/Show', compact('customer', 'customers'));
    }

    public function edit(Customer $customer)
    {
        $tags = TagResource::collection(Tag::where('type', 'crm')->get());

        return inertia('CRM/Customer/Edit', compact('customer', 'tags'));
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name' => 'required|string',
            'rfc' => 'required|string',
            'branches' => 'required|array|min:1',
            'contact_name' => 'required|string',
            'contact_email' => 'nullable|email',
            'contact_phone' => 'required|string',
            'invoicing_method' => 'required',
            'payment_method' => 'required',
            'invoice_use' => 'required',
        ]);

       $customer->update($request->all());

       //recupero el contacto aguardado al cliente editado para eliminarlo y guardar el otro
       $contact = Contact::where('contactable_id', $customer->id)->first();

       //crea el contacto en su tabla polimorfica
        $contact->name = $request->input('contact_name');
        $contact->email = $request->input('contact_email');
        $contact->phone = $request->input('contact_phone');
        $contact->user_id = auth()->id();
        $contact->contactable_type = Customer::class;
        $contact->contactable_id = $customer->id;
        $contact->save();
       
        return to_route('crm.customers.index');
    }

    public function destroy(Customer $customer)
    {
        //
    }
}
