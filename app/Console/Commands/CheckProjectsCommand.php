<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Models\Task;
use App\Notifications\ProjectAboutToExpireNotification;
use App\Notifications\TaskAboutToExpireNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckProjectsCommand extends Command
{
    protected $signature = 'projects:check';

    protected $description = 'Check projects that should be completed today.';

    public function handle()
    {
        $projects = Project::whereDate('limit_date', now())->get();

        foreach ($projects as $project) {
            $project->users->each(fn ($user) => $user->notify(new ProjectAboutToExpireNotification($project)));
            $this->info("Project: {$project->name} should be completed today!");
        }

        Log::info('projects:check executed successfully. There where ' . $projects->count() . ' project(s)');
    }
}
