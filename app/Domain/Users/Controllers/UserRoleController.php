<?php

namespace App\Domain\Users\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserRoleController extends Controller
{
    use AuthorizesRequests;

    public function edit(User $user)
    {
        $this->authorize('assignRoles', $user);
        $roles = Role::all();
        $permissions = $user->getAllPermissions();
        
        return view('users.roles', compact('user', 'roles', 'permissions'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('assignRoles', $user);
        $validated = $request->validate([
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id'
        ]);

        // Get the role names from the validated IDs
        $roleNames = Role::whereIn('id', $validated['roles'])
                        ->pluck('name')
                        ->toArray();

        $user->syncRoles($roleNames);

        return redirect()->route('users.index')
            ->with('success', 'Roles actualizados exitosamente.');
    }
}