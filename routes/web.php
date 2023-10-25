<?php

use App\Http\Controllers\CRMController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OpportunityController;
use App\Http\Controllers\PMSController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectGroupController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\TaskController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


// projects routes
Route::get('pms/dashboard', [PMSController::class, 'dashboard'])->middleware('auth')->name('pms.dashboard');
Route::resource('projects', ProjectController::class)->middleware('auth')->names('pms.projects');
Route::resource('tasks', TaskController::class)->middleware('auth')->names('pms.tasks');
Route::resource('project-groups', ProjectGroupController::class)->middleware('auth')->names('pms.project-groups');
Route::resource('tags', TagController::class)->middleware('auth')->names('pms.tags');
Route::post('tasks-{task}-comment', [TaskController::class, 'comment'])->name('pms.tasks.comment')->middleware('auth');
Route::put('tasks-{task}-pause-play', [TaskController::class, 'pausePlayTask'])->name('pms.tasks.pause-play')->middleware('auth');
Route::put('tasks-{task}-update-status', [TaskController::class, 'updateStatus'])->name('pms.tasks.update-status')->middleware('auth');
Route::get('tasks-late-tasks', [TaskController::class, 'getLateTasks'])->middleware('auth')->name('pms.tasks.get-late-tasks');

// crm routes
Route::get('crm/dashboard', [CRMController::class, 'dashboard'])->middleware('auth')->name('crm.dashboard');
Route::resource('customers', CustomerController::class)->middleware('auth')->names('crm.customers');
Route::resource('opportunities', OpportunityController::class)->middleware('auth')->names('crm.opportunities');

// settings routes
Route::resource('settings', SettingController::class)->middleware('auth');

// users routes
Route::resource('users', SettingController::class)->middleware('auth');


// default routes
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});
