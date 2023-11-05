<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectGroup;
use Illuminate\Http\Request;

class PMSController extends Controller
{
    public function dashboard()
    {
        $projects_progress = Project::with(['tasks.users', 'projectGroup'])->whereNull('finished_at')->get();
        $project_groups = ProjectGroup::get('name', 'id');
        $my_projects = Project::with(['tasks', 'projectGroup'])->whereNull('finished_at')->where('owner_id', auth()->id())->get();

        return inertia('PMS/Dashboard', compact('projects_progress', 'project_groups', 'my_projects'));
    }
}
