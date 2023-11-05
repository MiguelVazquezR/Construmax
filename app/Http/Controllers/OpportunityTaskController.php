<?php

namespace App\Http\Controllers;

use App\Http\Resources\OpportunityTaskResource;
use App\Models\Activity;
use App\Models\Comment;
use App\Models\OpportunityTask;
use App\Models\User;
use Illuminate\Http\Request;

class OpportunityTaskController extends Controller
{
    
    public function index()
    {
        //
    }

    
    public function create($opportunity_id)
    {
        $users = User::where('is_active', true)->get();

        return inertia('CRM/OpportunityTask/Create', compact('users', 'opportunity_id'));
    }

    
    public function store(Request $request, $opportunity_id)
    {
        $request->validate([
            'name' => 'required|string',
            'asigned_id' => 'required',
            'limit_date' => 'required|date',
            'time' => 'required',
            'priority' => 'required|string',
            'description' => 'required',
        ]);

        $opportunity_task = OpportunityTask::create($request->except('media') + [
            'user_id' => auth()->id(),
            'opportunity_id' => $opportunity_id,
        ]);

        // archivos adjuntos
        $opportunity_task->addAllMediaFromRequest()->each(fn ($file) => $file->toMediaCollection());

        //Crea el registro de una actividad para el historial de esa oportunidad --------------------------
        $asigned = User::find($request->asigned_id); //recupero el objeto del asignado a la actividad para la descripción.
        Activity::create([
            'description' => 'creó la actividad "' . $request->name . '" y la asignó a ' . $asigned->name,
            'user_id' => auth()->id(),
            'opportunity_id' => $opportunity_id,
        ]);

        return to_route('crm.opportunities.show', ['opportunity'=> $opportunity_task->opportunity_id, 'defaultTab' => 2]);
    }
    
    public function show(OpportunityTask $opportunityTask)
    {
        //
    }

    
    public function edit(OpportunityTask $opportunityTask)
    {
        //
    }

    
    public function update(Request $request, OpportunityTask $opportunity_task)
    {
        $validated = $request->validate([
            'asigned_id' => 'required',
            'limit_date' => 'required|date',
            'time' => 'required',
            'priority' => 'required|string',
            'description' => 'required',
        ]);

        $opportunity_task->update($validated); 

        if ($request->comment) {
            $comment = new Comment([
                'content' => $request->comment,
                'user_id' => auth()->id(),
            ]);
            $opportunity_task->comments()->save($comment);
        }

        //Crea el registro de una actividad para el historial de esa oportunidad --------------------------
        $asigned = User::find($request->asigned_id); //recupero el objeto del asignado a la actividad para la descripción.
        Activity::create([
            'description' => 'editó la actividad "' . $opportunity_task->name . '" y la asignó a ' . $asigned->name,
            'user_id' => auth()->id(),
            'opportunity_id' => $opportunity_task->opportunity_id,
        ]);

        return response()->json(['item' => OpportunityTaskResource::make($opportunity_task->fresh(['asigned','opportunity.activities.user','user','comments.user']))]);
    }

    
    public function destroy(OpportunityTask $opportunity_task)
    {
        $opportunity_task->delete();
    }

    public function markAsDone($opportunity_task_id)
    {
        $opportunity_task = OpportunityTask::find($opportunity_task_id);
        // return $opportunity_task;
        $opportunity_task->update([
            'finished_at' => now()
        ]);

        //Crea el registro de una actividad para el historial de esa oportunidad --------------------------
        Activity::create([
            'description' => 'marcó como terminada la actividad "' . $opportunity_task->name . '"',
            'user_id' => auth()->id(),
            'opportunity_id' => $opportunity_task->opportunity_id,
        ]);

        return response()->json(['item' => OpportunityTaskResource::make($opportunity_task->fresh(['asigned','opportunity.activities.user']))]);
    }

    public function comment(Request $request, OpportunityTask $opportunity_task)
    {

        $comment = new Comment([
            'content' => $request->comment,
            'user_id' => auth()->id(),
        ]);

        $opportunity_task->comments()->save($comment);

        return response()->json(['item' => $comment->fresh('user')]);
    }
}
