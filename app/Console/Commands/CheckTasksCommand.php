<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Notifications\TaskAboutToExpireNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckTasksCommand extends Command
{
    protected $signature = 'tasks:check';

    protected $description = 'Check tasks that should be completed today.';

    public function handle()
    {
        $tasks = Task::whereDate('limit_date', now())->get();

        foreach ($tasks as $task) {
            $task->users->each(fn ($user) => $user->notify(new TaskAboutToExpireNotification($task)));
            $this->info("Task: {$task->name} should be completed today!");
        }

        Log::info('tasks:check executed successfully. There where ' . $tasks->count() . ' task(s)');
    }
}
