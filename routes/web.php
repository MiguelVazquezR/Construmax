<?php

use App\Http\Controllers\ClientMonitorController;
use App\Http\Controllers\CRMController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\EmailMonitorController;
use App\Http\Controllers\MeetingMonitorController;
use App\Http\Controllers\OpportunityController;
use App\Http\Controllers\OpportunityTaskController;
use App\Http\Controllers\PaymentMonitorController;
use App\Http\Controllers\PMSController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectGroupController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Artisan;
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
Route::get('/tasks-format/{task_id}', [TaskController::class, 'taskFormat'])->middleware('auth')->name('pms.tasks-format');

// crm routes
Route::get('crm/dashboard', [CRMController::class, 'dashboard'])->middleware('auth')->name('crm.dashboard');
Route::resource('customers', CustomerController::class)->middleware('auth')->names('crm.customers');
Route::resource('opportunities', OpportunityController::class)->middleware('auth')->names('crm.opportunities');
Route::put('/opportunities/update-status/{opportunity_id}', [OpportunityController::class, 'updateStatus'])->name('crm.opportunities.update-status')->middleware('auth');
Route::post('opportunities/update-with-media/{opportunity}', [OpportunityController::class, 'updateWithMedia'])->name('crm.opportunities.update-with-media')->middleware('auth');

// ------- CRM (opportunities Routes)  ---------
Route::resource('opportunity-tasks', OpportunityTaskController::class)->except(['store', 'create'])->names('crm.opportunity-tasks')->middleware('auth');
Route::get('opportunity-tasks/create/{opportunity_id}', [OpportunityTaskController::class, 'create'])->name('crm.opportunity-tasks.create')->middleware('auth');
Route::post('opportunity-tasks/store/{opportunity_id}', [OpportunityTaskController::class, 'store'])->name('crm.opportunity-tasks.store')->middleware('auth');
Route::post('opportunity-tasks/{opportunity_task}/comment', [OpportunityTaskController::class, 'comment'])->name('crm.opportunity-tasks.comment')->middleware('auth');
Route::put('opportunity-tasks/mark-as-done/{opportunityTask}', [OpportunityTaskController::class, 'markAsDone'])->name('crm.opportunity-tasks.mark-as-done')->middleware('auth');

// ------- CRM (Client monior Routes)  ---------
Route::resource('client-monitors', ClientMonitorController::class)->names('crm.client-monitors')->middleware('auth');

// ------- CRM (Payment monior Routes)  ---------
Route::resource('payment-monitors', PaymentMonitorController::class)->names('crm.payment-monitors')->middleware('auth');
Route::post('payment-monitors/update-with-media/{payment_monitor}', [PaymentMonitorController::class, 'updateWithMedia'])->name('crm.payment-monitors.update-with-media')->middleware('auth');

// ------- CRM (meeting monior Routes)  ---------
Route::resource('meeting-monitors', MeetingMonitorController::class)->names('crm.meeting-monitors')->middleware('auth');

// ------- CRM (email monior Routes)  ---------
Route::resource('email-monitors', EmailMonitorController::class)->names('crm.email-monitors')->middleware('auth');

// settings routes
Route::resource('settings', SettingController::class)->middleware('auth');

// users routes
Route::resource('users', UserController::class)->middleware('auth');
Route::get('users-get-notifications', [UserController::class, 'getNotifications'])->name('users.get-user-notifications')->middleware('auth');
Route::get('users-get-pendent-tasks', [UserController::class, 'getPendentTasks'])->name('users.get-pendent-tasks')->middleware('auth');
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

//artisan commands
Route::get('/storage-link', function () {
    Artisan::call('storage:link');
    return 'Virtual link created!.';
});

Route::get('/migrate', function () {
    Artisan::call('migrate');
    return 'migrations completed!.';
});
