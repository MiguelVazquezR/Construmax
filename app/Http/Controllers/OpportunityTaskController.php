<?php

namespace App\Http\Controllers;

use App\Http\Resources\OpportunityTaskResource;
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

        return response()->json(['item' => OpportunityTaskResource::make($opportunity_task->fresh(['asigned','opportunity','user','comments.user']))]);
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
