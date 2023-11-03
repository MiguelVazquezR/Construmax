<?php

namespace App\Http\Controllers;

use App\Http\Resources\ClientMonitorResource;
use App\Models\ClientMonitor;
use Illuminate\Http\Request;

class ClientMonitorController extends Controller
{
    
    public function index()
    {
        $client_monitors = ClientMonitorResource::collection(ClientMonitor::with('customer', 'seller', 'opportunity', 'emailMonitor', 'paymentMonitor', 'meetingMonitor')->latest()->get());

        // return $client_monitors;

        return inertia('CRM/ClientMonitor/Index', compact('client_monitors'));
    }

    
    public function create()
    {
        //
    }

    
    public function store(Request $request)
    {
        //
    }

    
    public function show(ClientMonitor $client_monitor)
    {
        // 
    }

    
    public function edit(ClientMonitor $client_monitor)
    {
        //
    }

    
    public function update(Request $request, ClientMonitor $client_monitor)
    {
        //
    }

    
    public function destroy(ClientMonitor $client_monitor)
    {
        $client_monitor->delete();
    }
}
