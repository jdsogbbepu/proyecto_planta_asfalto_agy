<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Users/Index', [
            'users' => User::orderBy('name')->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', 'string', Rule::in(['administrador', 'operador', 'visor'])],
            'active' => ['required', 'boolean'],
        ]);

        User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'active' => $validated['active'],
        ]);

        return redirect()->route('users.index')->with('success', 'Usuario creado correctamente.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:6'],
            'role' => ['required', 'string', Rule::in(['administrador', 'operador', 'visor'])],
            'active' => ['required', 'boolean'],
        ]);

        $userData = [
            'name' => $validated['name'],
            'username' => $validated['username'],
            'role' => $validated['role'],
            'active' => $validated['active'],
        ];

        if (!empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }

        $user->update($userData);

        // If the updated user is the current user and their active status was set to false,
        // Laravel's auth middleware or RoleMiddleware will automatically sign them out on the next request.
        return redirect()->route('users.index')->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Prevent deleting yourself
        if (auth()->id() === $user->id) {
            return redirect()->route('users.index')->with('error', 'No puedes eliminar tu propio usuario de planta.');
        }

        // Toggle active status logically (as required by "control lógico") or perform hard delete
        // If the user has other relationships in the future, hard deleting might break foreign keys.
        // We will perform a hard delete, but catch any database integrity exceptions.
        try {
            $user->delete();
            return redirect()->route('users.index')->with('success', 'Usuario eliminado correctamente.');
        } catch (\Exception $e) {
            // If they have dependencies (like ticket records, registers, etc.), deactivate them logically instead.
            $user->update(['active' => false]);
            return redirect()->route('users.index')->with('success', 'El usuario tiene registros asociados. Ha sido desactivado lógicamente.');
        }
    }
}
