<?php

namespace App\Http\Controllers;

use App\Http\Resources\CustomerResource;
use App\Http\Resources\MeetingMonitorResource;
use App\Http\Resources\OpportunityResource;
use App\Models\Calendar;
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
        $users = User::whereNotIn('id', [1])->where('is_active', true)->get();
        $opportunity_id = request('opportunityId');

        return inertia('CRM/MeetingMonitor/Create', compact('customers', 'opportunities', 'users', 'opportunity_id'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'opportunity_id' => 'required',
            'time' => 'required',
            'customer_id' => 'required',
            'meeting_date' => 'required|date',
            'branch' => 'required|string|max:255',
            'contact_id' => 'required',
            // 'contact_name' => 'required|string',
            // 'contact_phone' => 'required|string',
            'meeting_via' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'description' => 'required|string|max:350',
            'participants' => 'required|array|min:1',
        ]);

        $meeting_monitor = MeetingMonitor::create($request->all() + ['seller_id' => auth()->id()]);

        // crear registro en tabla de seguimiento integral
        $client_monitor = ClientMonitor::create([
            'type' => 'Reunión',
            'date' => $request->meeting_date,
            'concept' => $request->description,
            'seller_id' => auth()->id(),
            'opportunity_id' => $request->opportunity_id,
            'customer_id' => $request->customer_id,
            'monitor_id' => $meeting_monitor->id,
        ]);

        $participants = [];
        // procesar arreglo de participantes
        foreach ($request->participants as $participantId) {
            $participants[] = [
                "user_id" => $participantId,
                "status" => "Pendiente",
            ];
        }
        // crear registro de calendario
        Calendar::create([
            'type' => 'Evento',
            'title' => "Cita con $request->contact_name",
            'location' => $request->location,
            'description' => "$request->description. Hora de cita: $request->time",
            'is_full_day' => 1,
            'start_date' => $request->meeting_date,
            'participants' => $participants,
            'user_id' => auth()->id(),
        ]);

        $meeting_monitor->client_monitor_id = $client_monitor->id;
        $meeting_monitor->save();

        return to_route('crm.client-monitors.index');
    }


    public function show($meeting_monitor_id)
    {
        $meeting_monitor = MeetingMonitorResource::make(MeetingMonitor::with('seller', 'opportunity', 'customer', 'contact')->find($meeting_monitor_id));

        return inertia('CRM/MeetingMonitor/Show', compact('meeting_monitor'));
    }


    public function edit($meeting_monitor_id)
    {
        $meeting_monitor = MeetingMonitorResource::make(MeetingMonitor::with('opportunity', 'customer', 'contact')->find($meeting_monitor_id));
        $customers = CustomerResource::collection(Customer::with('contacts')->latest()->get());
        $opportunities = OpportunityResource::collection(Opportunity::with('customer')->latest()->get());
        $users = User::whereNotIn('id', [1])->where('is_active', true)->get();

        return inertia('CRM/MeetingMonitor/Edit', compact('meeting_monitor', 'opportunities', 'customers', 'users'));
    }


    public function update(Request $request, MeetingMonitor $meeting_monitor)
    {
        $request->validate([
            'opportunity_id' => 'required',
            'time' => 'required',
            'customer_id' => 'required',
            'meeting_date' => 'required|date',
            'branch' => 'required|string|max:255',
            'contact_id' => 'required',
            // 'contact_name' => 'required|string',
            // 'contact_phone' => 'required|string',
            'meeting_via' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'description' => 'required|string|max:350',
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

        return to_route('crm.meeting-monitors.show', ['meeting_monitor' => $meeting_monitor]);
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
