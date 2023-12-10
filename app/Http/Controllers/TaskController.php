<?php

namespace App\Http\Controllers;

use App\Http\Resources\TaskResource;
use App\Models\Comment;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Notifications\AssignedTaskNotification;
use App\Notifications\MentionInCommentNotification;
use App\Notifications\NewCommentNotification;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function create(Request $request)
    {
        $projects = Project::with(['users'])->latest()->get();
        $users = User::whereNotIn('id', [1])->where('is_active', true)->get();
        $parent_id = $request->input('projectId') ?? 1;

        return inertia('PMS/Task/Create', compact('projects', 'users', 'parent_id'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required',
            'name' => 'required|string|max:255',
            'description' => 'nullable',
            'department' => 'required|string',
            'participants' => 'required|array|min:1',
            'priority' => 'required|string',
            'start_date' => 'required|date',
            'limit_date' => 'required|date',
            'start_time' => 'nullable',
            'limit_time' => 'nullable',
            'media' => 'nullable',
            // 'reminder' => 'required',
        ]);

        $task = Task::create($request->except('participants') + ['user_id' => auth()->id()]);

        foreach ($request->participants as $user_id) {
            // Adjuntar el usuario a la tarea
            $task->users()->attach($user_id);
            // notificar a usuarios que no sean el que crea la tarea
            $user = User::find($user_id);
            if ($user->id !== auth()->id()) {
                $user->notify(new AssignedTaskNotification($task, auth()->user()->name));
            }
        }

        $task->addAllMediaFromRequest('media')->each(fn ($file) => $file->toMediaCollection());

        return to_route('pms.projects.show', ['project' => $request->project_id, 'defaultTab' => 2]);
    }


    public function show(Task $task)
    {
        $task = TaskResource::make(Task::with(['project.users', 'user', 'users', 'comments.user', 'media'])->find($task->id));

        return inertia('PMS/Task/Show', compact('task'));
    }


    public function edit(Task $task)
    {
        //
    }


    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'status' => 'required|string|max:255',
            'description' => 'required',
            'start_date' => 'required|date',
            'limit_date' => 'required|date',
            'department' => 'required|string',
            'priority' => 'nullable|string',
            'participants' => 'required|array|min:1',
        ]);

        $task->update($validated);

        $task->users()->sync($request->participants);

        if ($request->comment) {
            $comment = new Comment([
                'content' => $request->comment,
                'user_id' => auth()->id(),
            ]);
            $task->comments()->save($comment);
        }

        $this->handleUpdatedTaskStatus($task->project_id);

        return to_route('pms.projects.show', ['project' => $task->project_id, 'defaultTab' => 2]);
    }


    public function destroy(Task $task)
    {
        $task->comments()->delete();
        $task->delete();

        return to_route('pms.projects.show', ['project' => $task->project_id, 'defaultTab' => 2]);
    }


    public function comment(Request $request, Task $task)
    {

        $comment = new Comment([
            'content' => $request->comment,
            'user_id' => auth()->id(),
        ]);
        $task->comments()->save($comment);

        // notificar a usuarios participantes
        foreach ($task->users as $user) {
            if ($user->id !== auth()->id()) {
                $user->notify(new NewCommentNotification('Tarea', $task->name, 'projects', route('pms.tasks.show', $task), auth()->user()->name));
            }
        }

        // notificat a mencionados 
        $mentions = $request->mentions;
        foreach ($mentions as $mention) {
            $user = User::find($mention['id']);
            $user->notify(new MentionInCommentNotification('Tarea', $task->name, 'projects', route('pms.tasks.show', $task), auth()->user()->name));
        }

        return response()->json(['item' => $comment->fresh('user')]);
    }

    public function pausePlayTask(Task $task, Request $request)
    {
        if ($task->is_paused) {
            $task->update([
                'is_paused' => false,
                'pausa_reazon' => null
            ]);
        } else {
            $task->update([
                'is_paused' => true,
                'pausa_reazon' => $request->reazon
            ]);
        }
        $task->save();

        return response()->json(['item' => TaskResource::make($task->fresh(['project.users', 'user', 'users', 'comments.user', 'media']))]);
    }

    public function updateStatus(Task $task, Request $request)
    {
        $task->update([
            'status' => $request->status,
            'pausa_reazon' => null,
            'is_paused' => 0,
        ]);
        $this->handleUpdatedTaskStatus($task->project_id);

        return response()->json([]);
    }

    public function getLateTasks()
    {
        $late_tasks = Task::with(['users', 'project'])->where('status', '!=', 'Terminada')->whereDate('limit_date', '<', today())->get();

        $currentDate = today();

        $late_tasks = $late_tasks->map(function ($task) use ($currentDate) {
            $lateDays = $task->limit_date->diffInDays($currentDate); // Calcula la diferencia en días entre end_date y la fecha actual
            $task['late_days'] = $lateDays; // Agrega la propiedad "late_days" al objeto de la tarea
            return $task;
        });

        return response()->json(['items' => $late_tasks]);
    }

    public function taskFormat($task_id)
    {
        $task = TaskResource::make(Task::with('project.users', 'user')->find($task_id));

        return inertia('PMS/Project/TaskFormat', compact('task'));
    }

    private function handleUpdatedTaskStatus($project_id)
    {
        // Obtén el proyecto al que pertenece la tarea
        $project = Project::with('tasks')->find($project_id);

        // Verifica si todas las tareas del proyecto están terminadas y actualiza propiedad finished_at
        if ($project->tasks->where('status', 'Terminada')->count() === $project->tasks->count()) {
            $project->finished_at = now();
            $project->save();
        } else if ($project->finished_at !== null) {
            $project->finished_at = null;
            $project->save();
        }
    }
}
