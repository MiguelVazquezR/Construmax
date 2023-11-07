<?php

namespace App\Http\Controllers;

use App\Http\Resources\CustomerResource;
use App\Http\Resources\EmailMonitorResource;
use App\Http\Resources\OpportunityResource;
use App\Models\ClientMonitor;
use App\Models\Customer;
use App\Models\EmailMonitor;
use App\Models\Opportunity;
use App\Models\User;
use Illuminate\Http\Request;

class EmailMonitorController extends Controller
{
    
    public function index()
    {
        //
    }

    
    public function create()
    {
        $customers = CustomerResource::collection(Customer::with('contacts')->latest()->get());
        $opportunities = OpportunityResource::collection(Opportunity::with('customer.contacts')->latest()->get());
        $users = User::whereNotIn('id', [1])->where('is_active', true)->get();

        return inertia('CRM/EmailMonitor/Create', compact('customers', 'opportunities', 'users'));
    }

    
    public function store(Request $request)
    {
        $request->validate([
            'opportunity_id' => 'required',
            'customer_id' => 'required',
            'branch' => 'required',
            'contact_id' => 'required',
            'contact_name' => 'required',
            'contact_email' => 'required',
            'subject' => 'required',
            'content' => 'required',
        ]);

        $email_monitor = EmailMonitor::create($request->all() + ['seller_id' => auth()->id()]);

        $client_monitor = ClientMonitor::create([
            'type' => 'Correo electrónico',
            'date' => now(),
            'concept' => $request->subject,
            'seller_id' => auth()->id(),
            'opportunity_id' => $request->opportunity_id,
            'customer_id' => $request->customer_id,
            'monitor_id' => $email_monitor->id,
        ]);

        $email_monitor->client_monitor_id = $client_monitor->id;
        $email_monitor->save();

        // // enviar correo a contacto
        // if (app()->environment() == 'production') {
        //     Mail::to($request->contact_email)->send(new EmailMonitorTemplateMail($request->subject, $request->content));
        // }

        return to_route('crm.client-monitors.index');
    }

    
    public function show($email_monitor_id)
    {
        $email_monitor = EmailMonitorResource::make(EmailMonitor::with('seller', 'opportunity', 'customer')->find($email_monitor_id));

        return inertia('CRM/EmailMonitor/Show', compact('email_monitor'));
    }

    
    public function edit(EmailMonitor $emailMonitor)
    {
        //
    }

    
    public function update(Request $request, EmailMonitor $emailMonitor)
    {
        //
    }

    
    public function destroy($email_monitor_id)
    {
        $email_monitor = EmailMonitor::find($email_monitor_id);
        $client_monitor = ClientMonitor::where('monitor_id', $email_monitor->id)->where('type', 'Correo electrónico')->first();
        $client_monitor->delete();
        $email_monitor->delete();

        return to_route('crm.client-monitors.index');
    }
}
