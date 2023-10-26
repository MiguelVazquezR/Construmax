<?php

use App\Http\Controllers\CRMController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OpportunityController;
use App\Http\Controllers\OpportunityTaskController;
use App\Http\Controllers\PMSController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectGroupController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


// projects routes
Route::get('pms/dashboard', [PMSController::class, 'dashboard'])->middleware('auth')->name('pms.dashboard');
Route::resource('projects', ProjectController::class)->middleware('auth')->names('pms.projects');
Route::resource('tasks', TaskController::class)->middleware('auth')->names('pms.tasks');
Route::resource('project-groups', ProjectGroupController::class)->middleware('auth')->names('pms.project-groups');
Route::post('projects/update-with-media/{project}', [ProjectController::class, 'updateWithMedia'])->name('pms.projects.update-with-media')->middleware('auth');
Route::resource('tags', TagController::class)->middleware('auth')->names('pms.tags');
Route::post('tasks-{task}-comment', [TaskController::class, 'comment'])->name('pms.tasks.comment')->middleware('auth');
Route::put('tasks-{task}-pause-play', [TaskController::class, 'pausePlayTask'])->name('pms.tasks.pause-play')->middleware('auth');
Route::put('tasks-{task}-update-status', [TaskController::class, 'updateStatus'])->name('pms.tasks.update-status')->middleware('auth');
Route::get('tasks-late-tasks', [TaskController::class, 'getLateTasks'])->middleware('auth')->name('pms.tasks.get-late-tasks');
Route::get('/tasks-format/{task_id}', [TaskController::class, 'taskFormat'])->middleware('auth')->name('pms.tasks-format');

// crm routes
Route::get('crm/dashboard', [CRMController::class, 'dashboard'])->middleware('auth')->name('crm.dashboard');
Route::resource('customers', CustomerController::class)->middleware('auth')->names('crm.customers');
Route::resource('opportunities', OpportunityController::class)->middleware('auth')->names('crm.opportunities');
Route::put('/opportunities/update-status/{opportunity_id}', [OpportunityController::class, 'updateStatus'])->name('crm.opportunities.update-status')->middleware('auth');

// ------- CRM (opportunityTasks Routes)  ---------
Route::resource('opportunity-tasks', OpportunityTaskController::class)->except(['store', 'create'])->middleware('auth');
Route::get('opportunity-tasks/create/{opportunity_id}', [OpportunityTaskController::class, 'create'])->name('crm.opportunity-tasks.create')->middleware('auth');
Route::post('opportunity-tasks/store/{opportunity_id}', [OpportunityTaskController::class, 'store'])->name('crm.opportunity-tasks.store')->middleware('auth');
Route::post('opportunity-tasks/{opportunity_task}/comment', [OpportunityTaskController::class, 'comment'])->name('crm.opportunity-tasks.comment')->middleware('auth');
Route::put('opportunity-tasks/mark-as-done/{opportunityTask}', [OpportunityTaskController::class, 'markAsDone'])->name('crm.opportunity-tasks.mark-as-done')->middleware('auth');

// settings routes
Route::resource('settings', SettingController::class)->middleware('auth');

// users routes
Route::resource('users', UserController::class)->middleware('auth');
Route::get('users-get-notifications', [UserController::class, 'getNotifications'])->name('users.get-user-notifications')->middleware('auth');
Route::delete('users-delete-notifications', [UserController::class, 'deleteNotifications'])->name('users.delete-user-notifications')->middleware('auth');
Route::post('users-read-notifications', [UserController::class, 'readNotifications'])->name('users.read-user-notifications')->middleware('auth');
Route::put('users-{user}-toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status')->middleware('auth');

// ------- Roles and permissions Routes ---------
Route::get('role-permission', [RolePermissionController::class, 'index'])->middleware('auth')->name('settings.role-permission.index');
Route::put('role-permission/{role}/edit-role', [RolePermissionController::class, 'updateRole'])->middleware('auth')->name('settings.role-permission.update-role');
Route::post('role-permission/store-role', [RolePermissionController::class, 'storeRole'])->middleware('auth')->name('settings.role-permission.store-role');
Route::delete('role-permission/{role}/destroy-role', [RolePermissionController::class, 'deleteRole'])->middleware('auth')->name('settings.role-permission.delete-role');
Route::put('role-permission/{permission}/edit-permission', [RolePermissionController::class, 'updatePermission'])->middleware('auth')->name('settings.role-permission.update-permission');
Route::post('role-permission/store-permission', [RolePermissionController::class, 'storePermission'])->middleware('auth')->name('settings.role-permission.store-permission');
Route::delete('role-permission/{permission}/destroy-permission', [RolePermissionController::class, 'deletePermission'])->middleware('auth')->name('settings.role-permission.delete-permission');

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
