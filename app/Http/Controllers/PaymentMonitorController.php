<?php

namespace App\Http\Controllers;

use App\Http\Resources\CustomerResource;
use App\Http\Resources\OpportunityResource;
use App\Http\Resources\PaymentMonitorResource;
use App\Models\ClientMonitor;
use App\Models\Customer;
use App\Models\Opportunity;
use App\Models\PaymentMonitor;
use Illuminate\Http\Request;

class PaymentMonitorController extends Controller
{
    
    public function index()
    {
        //
    }

    
    public function create()
    {
        $opportunities = OpportunityResource::collection(Opportunity::with('customer')->latest()->get());
        $customers = CustomerResource::collection(Customer::with('contacts')->latest()->get());
        $opportunity_id = request('opportunityId');

        return inertia('CRM/PaymentMonitor/Create', compact('opportunities', 'customers', 'opportunity_id'));
    }

    
    public function store(Request $request)
    {
        $request->validate([
            'opportunity_id' => 'required',
            'customer_id' => 'required',
            'branch' => 'required|string|max:255',
            'contact_id' => 'required',
            // 'contact_name' => 'required|string',
            // 'contact_phone' => 'required|string',
            'paid_at' => 'required',
            'amount' => 'required|numeric|min:0|max:999999.99',
            'payment_method' => 'required|string|max:255',
            'concept' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $payment_monitor = PaymentMonitor::create($request->all() + ['seller_id' => auth()->id()]);

        $payment_monitor->addAllMediaFromRequest()->each(fn ($file) => $file->toMediaCollection());


        $client_monitor = ClientMonitor::create([
            'type' => 'Pago',
            'date' => $request->paid_at,
            'concept' => $request->concept,
            'seller_id' => auth()->id(),
            'opportunity_id' => $request->opportunity_id,
            'customer_id' => $request->customer_id,
            'monitor_id' => $payment_monitor->id,
        ]);

        $payment_monitor->client_monitor_id = $client_monitor->id;
        $payment_monitor->save();
        
        return to_route('crm.client-monitors.index');
    }

    
    public function show($payment_monitor_id)
    {
        $payment_monitor = PaymentMonitorResource::make(PaymentMonitor::with('seller', 'opportunity', 'customer', 'contact', 'media')->find($payment_monitor_id));

        return inertia('CRM/PaymentMonitor/Show', compact('payment_monitor'));
    }

    
    public function edit($payment_monitor_id)
    {
        $payment_monitor = PaymentMonitorResource::make(PaymentMonitor::with('opportunity', 'customer', 'contact')->find($payment_monitor_id));
        $opportunities = OpportunityResource::collection(Opportunity::with('customer')->latest()->get());
        $customers = CustomerResource::collection(Customer::with('contacts')->latest()->get());

        return inertia('CRM/PaymentMonitor/Edit', compact('payment_monitor', 'opportunities', 'customers'));
    }

    
    public function update(Request $request, PaymentMonitor $payment_monitor)
    {
        $request->validate([
            'opportunity_id' => 'required',
            'customer_id' => 'required',
            'branch' => 'required|string|max:255',
            'contact_id' => 'required',
            // 'contact_name' => 'required|string',
            // 'contact_phone' => 'required|string',
            'paid_at' => 'required',
            'amount' => 'required|numeric|min:0|max:999999.99',
            'payment_method' => 'required|string|max:255',
            'concept' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $payment_monitor->update($request->all());

        //recupero el seguimiento integral para actualizarlo tambien
        $client_monitor = ClientMonitor::where('opportunity_id', $payment_monitor->opportunity_id)->first();
              
        $client_monitor->update([
            'date' => $request->paid_at,
            'concept' => $request->concept,
            'opportunity_id' => $request->opportunity_id,
            'customer_id' => $request->customer_id,
        ]);
        
        return to_route('crm.payment-monitors.show', ['payment_monitor'=> $payment_monitor]);
    }

    public function updateWithMedia(Request $request, PaymentMonitor $payment_monitor)
    {
        $request->validate([
            'opportunity_id' => 'required',
            'customer_id' => 'required',
            'branch' => 'required|string|max:255',
            'contact_id' => 'required',
            // 'contact_name' => 'required|string',
            // 'contact_phone' => 'required|string',
            'paid_at' => 'required',
            'amount' => 'required|numeric|min:0|max:999999.99',
            'payment_method' => 'required|string|max:255',
            'concept' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $payment_monitor->update($request->all());

        $payment_monitor->clearMediaCollection();
        $payment_monitor->addAllMediaFromRequest()->each(fn ($file) => $file->toMediaCollection());
        
         //recupero el seguimiento integral para actualizarlo tambien
         $client_monitor = ClientMonitor::where('opportunity_id', $payment_monitor->opportunity_id)->first();

         $client_monitor->update([
             'date' => $request->paid_at,
             'concept' => $request->concept,
             'opportunity_id' => $request->opportunity_id,
             'customer_id' => $request->customer_id,
         ]);
        
        return to_route('crm.payment-monitors.show', ['payment_monitor'=> $payment_monitor]);
    }

    
    public function destroy($payment_monitor_id)
    {   
        $payment_monitor = PaymentMonitor::find($payment_monitor_id);
        $client_monitor = ClientMonitor::where('monitor_id', $payment_monitor->id)->where('type', 'Pago')->first();
        $client_monitor->delete();
        $payment_monitor->delete();

        return to_route('crm.client-monitors.index');
    }
}