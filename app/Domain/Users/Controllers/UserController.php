<?php

namespace App\Domain\Users\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Domain\Organizations\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Domain\Users\Requests\StoreUserRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', User::class);
        // Añadido paginación y ordenamiento por nombre
        $users = User::with('organization')->orderBy('name')->paginate(10); 
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $this->authorize('create', User::class);
        $organizations = Organization::all();
        $roles = Role::all();
        return view('users.create', compact('organizations', 'roles'));
    }

    public function store(StoreUserRequest $request)
    {
        $this->authorize('create', User::class);
        try {
            $user = User::create([
                'name' => $request->name,
                'identification' => $request->identification,
                'email' => $request->email,
                'area' => $request->area,
                'organization_id' => $request->organization_id,
                'password' => Hash::make($request->password),
            ]);

            if ($request->role) {
                $user->assignRole($request->role);
            }

            return redirect()
                ->route('users.index')
                ->with('success', 'Usuario registrado correctamente.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al crear el usuario: ' . $e->getMessage());
        }
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user);
        $organizations = Organization::all();
        $roles = Role::all();
        $currentRole = $user->roles->first();

        return view('users.edit', compact('user', 'organizations', 'roles', 'currentRole'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'identification' => 'required|string|unique:users,identification,' . $user->id,
                'area' => 'nullable|string|max:100',
                'organization_id' => 'required|exists:organizations,id',
                'role' => 'required|exists:roles,name',
                'password' => 'nullable|min:8|confirmed'
                
            ]);

            $userData = [
                'name' => $request->name,
                'identification' => $request->identification,
                'area' => $request->area,
                'organization_id' => $request->organization_id,
            ];

            // Only update password if provided
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $user->update($userData);

            // Update role if changed
            if ($request->role !== $user->roles->first()?->name) {
                $this->authorize('assignRoles', $user);
                $user->syncRoles([$request->role]);
            }

            return redirect()
                ->route('users.index')
                ->with('success', 'Usuario actualizado correctamente.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al actualizar el usuario: ' . $e->getMessage());
        }
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);
        $user->delete();
        return redirect()->route('users.index')->with('success', 'Usuario eliminado.');
    }
}
