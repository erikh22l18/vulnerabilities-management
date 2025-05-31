<?php

namespace App\Domain\Organizations\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\Organizations\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;

class OrganizationUserController extends Controller
{
    public function index(Organization $organization)
    {
        $users = $organization->users()->paginate(10);
        return view('organizations.users.index', compact('organization', 'users'));
    }

    public function create(Organization $organization)
    {
        // Obtener usuarios que no pertenecen a ninguna organización
        $availableUsers = User::whereNull('organization_id')->get();

        return view('organizations.users.create', compact('organization', 'availableUsers'));
    }

    public function store(Request $request, Organization $organization)
    {
        if ($request->has('create_new')) {
            // Crear nuevo usuario
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
            ]);

            User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => bcrypt($validated['password']),
                'organization_id' => $organization->id
            ]);

            return redirect()->route('organizations.users.index', $organization)
                ->with('success', 'Usuario creado y agregado exitosamente.');
        } else {
            // Agregar usuarios existentes
            $validated = $request->validate([
                'user_ids' => 'required|array',
                'user_ids.*' => 'exists:users,id'
            ]);

            User::whereIn('id', $validated['user_ids'])
                ->update(['organization_id' => $organization->id]);

            return redirect()->route('organizations.users.index', $organization)
                ->with('success', 'Usuarios agregados exitosamente.');
        }
    }

    public function edit(Organization $organization, User $user)
    {
        return view('organizations.users.edit', compact('organization', 'user'));
    }

    public function update(Request $request, Organization $organization, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->update($validated);

        return redirect()->route('organizations.users.index', $organization)
            ->with('success', 'Usuario actualizado exitosamente.');
    }

    public function destroy(Organization $organization, User $user)
    {
        // En lugar de $user->delete();
        $user->update(['organization_id' => null]);

        return redirect()->route('organizations.users.index', $organization)
            ->with('success', 'Usuario removido de la organización exitosamente.');
    }
}
