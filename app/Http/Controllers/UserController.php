<?php

namespace App\Http\Controllers;

use App\Http\Resources\NotificationResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = UserResource::collection(User::whereNotIn('id', [1])->latest()->get());
        return inertia('User/Index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();

        return inertia('User/Create', compact('roles'));
    }

    public function store(Request $request)
    {
        // dd($request->hasFile('photo'));
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255|unique:users',
            'employee_properties.department' => 'required|string|max:255',
            'employee_properties.position' => 'required|string|max:255',
            'employee_properties.phone' => 'required|string|max:15',
            // 'roles' => 'required|array|min:1',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = User::create($request->all() + ['password' => bcrypt('Construmax123')]);
        $user->syncRoles($request->roles);

        // guardar foto de perfil en caso de haberse seleccionado una
        if ($request->hasFile('photo')) {
            $this->storeProfilePhoto($request, $user);
        }

        return to_route('users.show', $user->id);
    }

    public function show(User $user)
    {
        $users = User::whereNotIn('id', [1])->get();

        return inertia('User/Show', compact('user', 'users'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $user_roles = $user->roles->pluck('id');

        return inertia('User/Edit', compact('user', 'roles', 'user_roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255|unique:users,email,' . $user->id,
            'employee_properties.department' => 'required|string|max:255',
            'employee_properties.position' => 'required|string|max:255',
            'employee_properties.phone' => 'required|string|max:15',
            'roles' => 'required|array|min:1',
            'photo' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user->update($request->all());
        $user->syncRoles($request->roles);

        return to_route('users.show', $user->id);
    }

    public function getNotifications()
    {
        $items = NotificationResource::collection(auth()->user()->notifications);

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

    public function getPendentTasks()
    {
        $pendent = auth()->user()->tasks()->whereIn('status', ['Por hacer', 'En curso'])->get();

        return response()->json(['items' => $pendent]);
    }

    public function storeProfilePhoto($request, User $user)
    {
        // Guarda la imagen en el sistema de archivos.
        $path = $request->file('photo')->store('public/profile-photos');

        // Actualiza la propiedad 'profile_photo_path' del usuario actual.
        $user->update([
            'profile_photo_path' => $path,
        ]);
    }
}
