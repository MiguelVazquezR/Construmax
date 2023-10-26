<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = UserResource::collection(User::whereNotNull('employee_properties')->latest()->get());
        return inertia('User/Index', compact('users'));
    }

    public function create()
    {
        return inertia('User/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'employee_properties.department' => 'required|string|max:255',
            'employee_properties.position' => 'required|string|max:255',
            'employee_properties.phone' => 'required|string|max:15',
        ]);

        $user = User::create($request->all() + ['password' => bcrypt('Construmax123')]);

        return to_route('users.show', $user->id);
    }

    public function show(User $user)
    {
        $users = User::whereNotNull('employee_properties')->get();

        return inertia('User/Show', compact('user', 'users'));
    }

    public function edit(User $user)
    {
        return inertia('User/Edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'employee_properties.department' => 'required|string|max:255',
            'employee_properties.position' => 'required|string|max:255',
            'employee_properties.phone' => 'required|string|max:15',
        ]);

        $user->update($request->all());

        return to_route('users.show', $user->id);
    }

    public function getNotifications()
    {
        $items = auth()->user()->notifications;

        return response()->json(compact('items'));
    }

    public function deleteNotifications()
    {
        auth()->user()->notifications()->delete();

        return response()->json(['message' => "Se han eliminado todas las notificaciones"]);
    }

    public function readNotifications()
    {
        $unread = auth()->user()->unreadNotifications->count();
        auth()->user()->notifications->markAsRead();

        return response()->json(compact('unread'));
    }

    public function toggleStatus(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);

        return to_route('users.show', $user->id);
    }
    
    public function destroy(User $user)
    {
        $user->delete();

        return to_route('users.index');
    }
}
