<?php

namespace App\Domain\Users\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * Controller for managing role assignments for a user.
 * Uses Spatie's Laravel Permission library for role management.
 *
 * @package App\Domain\Users\Controllers
 */
class UserRoleController extends Controller
{
    use AuthorizesRequests; // Laravel's trait for handling authorization

    /**
     * Show the form for editing the roles of a specific user.
     * Displays all available roles and the user's current permissions (derived from their roles).
     *
     * @param \App\Models\User $user The user whose roles are to be edited.
     * @return \Illuminate\View\View Returns the role editing form view.
     * @throws \Illuminate\Auth\Access\AuthorizationException If the current user is not authorized to assign roles.
     */
    public function edit(User $user): View
    {
        // Authorize if the current user can perform 'assignRoles' action (on the $user model or globally).
        // 'assignRoles' should be a defined ability, possibly in UserPolicy or AuthServiceProvider.
        $this->authorize('assignRoles', $user);

        $roles = Role::all(); // Get all available roles.
        // Get all permissions the user has, including those from roles and direct permissions.
        $permissions = $user->getAllPermissions();
        
        return view('users.roles', compact('user', 'roles', 'permissions'));
    }

    /**
     * Update the roles assigned to a specific user.
     * Uses Spatie's `syncRoles` to replace existing roles with the new set.
     *
     * @param \Illuminate\Http\Request $request The HTTP request containing the new role IDs.
     * @param \App\Models\User $user The user whose roles are to be updated.
     * @return \Illuminate\Http\RedirectResponse Redirects to the user index (or a relevant page) with a success message.
     * @throws \Illuminate\Auth\Access\AuthorizationException If the current user is not authorized to assign roles.
     * @throws \Illuminate\Validation\ValidationException If request validation fails.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $this->authorize('assignRoles', $user);

        // Validate that 'roles' is an array and each role ID exists in the 'roles' table.
        $validated = $request->validate([
            'roles' => 'sometimes|array', // 'sometimes' if roles can be empty, 'required' otherwise
            'roles.*' => 'exists:roles,id' // Each item in roles array must be an existing role ID
        ]);

        $roleNames = [];
        if (!empty($validated['roles'])) {
            // Convert role IDs from the request to role names, as Spatie's syncRoles often prefers names.
            // Alternatively, Spatie might accept IDs directly, check your version/setup.
            $roleNames = Role::whereIn('id', $validated['roles'])
                            ->pluck('name')
                            ->toArray();
        }

        // Synchronize the user's roles. This removes any roles not in $roleNames
        // and adds any new ones.
        $user->syncRoles($roleNames);

        // Consider redirecting to a more specific user management page if 'users.index' is too general.
        // For example, redirect back to the user's profile or the edit roles page.
        return redirect()->route('admin.users.index')->with('success', 'Roles actualizados exitosamente.');
        // If you want to redirect to a general user listing that might not be admin-specific:
        // return redirect()->route('users.index')->with('success', 'Roles actualizados exitosamente.');
        // Or, if you have a specific route for showing a user:
        // return redirect()->route('users.show', $user)->with('success', 'Roles actualizados exitosamente.');
    }
}