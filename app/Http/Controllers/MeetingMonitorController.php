<?php

namespace App\Http\Controllers;

use App\Http\Resources\CustomerResource;
use App\Http\Resources\MeetingMonitorResource;
use App\Http\Resources\OpportunityResource;
use App\Models\ClientMonitor;
use App\Models\Customer;
use App\Models\MeetingMonitor;
use App\Models\Opportunity;
use App\Models\User;
use Illuminate\Http\Request;

class MeetingMonitorController extends Controller
{
    
    public function index()
    {
        //
    }

    
    public function create()
    {
        $customers = CustomerResource::collection(Customer::with('contacts')->latest()->get());
        $opportunities = OpportunityResource::collection(Opportunity::with('customer')->latest()->get());
        $users = User::where('is_active', true)->get();

        // return $opportunities;
        return inertia('CRM/MeetingMonitor/Create', compact('customers', 'opportunities', 'users'));
    }

    
    public function store(Request $request)
    {
        $request->validate([
            'opportunity_id' => 'required',
            'time' => 'required',
            'customer_id' => 'required',
            'meeting_date' => 'required|date',
            'branch' => 'required|string',
            'contact_id' => 'required',
            'contact_name' => 'required|string',
            'contact_phone' => 'required|string',
            'meeting_via' => 'required|string',
            'location' => 'nullable|string',
            'description' => 'required|string',
            'participants' => 'required|array|min:1',
        ]);

        $meeting_monitor = MeetingMonitor::create($request->all() + ['seller_id' => auth()->id()]);
        
       $client_monitor = ClientMonitor::create([
            'type' => 'Reunión',
            'date' => $request->meeting_date,
            'concept' => $request->description,
            'seller_id' => auth()->id(),
            'opportunity_id' => $request->opportunity_id,
            'customer_id' => $request->customer_id,
            'monitor_id' => $meeting_monitor->id,
        ]);

        $meeting_monitor->client_monitor_id = $client_monitor->id;
        $meeting_monitor->save();
        
        return to_route('crm.client-monitors.index');
    }

    
    public function show($meeting_monitor_id)
    {
        $meeting_monitor = MeetingMonitorResource::make(MeetingMonitor::with('seller', 'opportunity', 'customer', 'contact')->find($meeting_monitor_id));

        // return $meeting_monitor;
        return inertia('CRM/MeetingMonitor/Show', compact('meeting_monitor'));
    }

    
    public function edit($meeting_monitor_id)
    {
        $meeting_monitor = MeetingMonitorResource::make(MeetingMonitor::with('opportunity', 'customer', 'contact')->find($meeting_monitor_id));
        $customers = CustomerResource::collection(Customer::with('contacts')->latest()->get());
        $opportunities = OpportunityResource::collection(Opportunity::with('customer')->latest()->get());
        $users = User::where('is_active', true)->get();

        // return $meeting_monitor;

        return inertia('CRM/MeetingMonitor/Edit', compact('meeting_monitor', 'opportunities', 'customers', 'users'));
    }

    
    public function update(Request $request, MeetingMonitor $meeting_monitor)
    {
        $request->validate([
            'opportunity_id' => 'required',
            'time' => 'required',
            'customer_id' => 'required',
            'meeting_date' => 'required|date',
            'branch' => 'required|string',
            'contact_id' => 'required',
            'contact_name' => 'required|string',
            'contact_phone' => 'required|string',
            'meeting_via' => 'required|string',
            'location' => 'nullable|string',
            'description' => 'required|string',
            'participants' => 'required|array|min:1',
        ]);

        $meeting_monitor->update($request->all());
                
        $client_monitor = ClientMonitor::where('opportunity_id', $meeting_monitor->opportunity_id)->first();
        

        $client_monitor->update([
            'date' => $request->meeting_date,
            'concept' => $request->description,
            'oportunity_id' => $request->opportunity_id,
            'company_id' => $request->customer_id,
        ]);
        
        return to_route('crm.meeting-monitors.show', ['meeting_monitor'=> $meeting_monitor]);
    }

    
    public function destroy($meeting_monitor_id)
    {
        $meeting_monitor = MeetingMonitor::find($meeting_monitor_id);
        $client_monitor = ClientMonitor::where('monitor_id', $meeting_monitor->id)->where('type', 'Reunión')->first();
        $client_monitor->delete();
        $meeting_monitor->delete();

        return to_route('crm.client-monitors.index');
    }
}
