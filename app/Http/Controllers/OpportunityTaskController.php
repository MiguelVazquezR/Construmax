<?php

namespace App\Http\Controllers;

use App\Http\Resources\OpportunityResource;
use App\Http\Resources\OpportunityTaskResource;
use App\Models\Activity;
use App\Models\Comment;
use App\Models\Opportunity;
use App\Models\OpportunityTask;
use App\Models\User;
use App\Notifications\MentionInCommentNotification;
use App\Notifications\NewCommentNotification;
use Illuminate\Http\Request;

class OpportunityTaskController extends Controller
{
    
    public function index()
    {
        //
    }

    
    public function create($opportunity_id)
    {
        $users = User::whereNotIn('id', [1])->where('is_active', true)->get();

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
        //Crea el registro de una actividad para el historial de esa oportunidad --------------------------
        Activity::create([
            'description' => 'eliminó la actividad "' . $opportunity_task->name . '"',
            'user_id' => auth()->id(),
            'opportunity_id' => $opportunity_task->opportunity_id,
        ]);

        $opportunity_id = $opportunity_task->opportunity_id; // guarda el id de la oportunidad antes de eliminar la tarea.

        $opportunity_task->delete();

        return response()->json(['item' => OpportunityResource::make(Opportunity::with('activities.user')->find($opportunity_id))]);
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

         // notificar a usuarios participantes
         foreach ($opportunity_task->users as $user) {
            if ($user->id !== auth()->id()) {
                $user->notify(new NewCommentNotification('actividad', $opportunity_task->name, 'opportunities', route('crm.opportunity-tasks.show', $opportunity_task), auth()->user()->name));
            }
        }

        // notificar a mencionados 
        $mentions = $request->mentions;
        foreach ($mentions as $mention) {
            $user = User::find($mention['id']);
            $user->notify(new MentionInCommentNotification('actividad', $opportunity_task->name, 'opportunities', route('crm.opportunity-tasks.show', $opportunity_task), auth()->user()->name));
        }

        return response()->json(['item' => $comment->fresh('user')]);
    }
}
