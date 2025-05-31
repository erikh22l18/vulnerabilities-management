<?php

namespace App\Domain\Users\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Grant all abilities to admin users.
     */
    public function before(User $currentUser, string $ability): ?bool
    {
        if ($currentUser->hasRole('admin')) {
            return true;
        }
        return null; // Continue to other policy methods
    }

    /**
     * Determine whether the user can view any models.
     * The $targetUser parameter is not strictly needed for viewAny but kept for consistency in some policy generators.
     */
    public function viewAny(User $currentUser): bool
    {
        return $currentUser->hasPermissionTo('ver usuarios');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $currentUser, User $targetUser): bool
    {
        return $currentUser->hasPermissionTo('ver usuarios');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $currentUser): bool
    {
        // Only admin can create users, implicitly handled by 'before' method or direct permission check.
        return $currentUser->hasPermissionTo('crear usuarios');
    }

    /**
     * Determine whether the user can update the model.
     * This typically refers to editing profile information, changing roles, etc.
     */
    public function update(User $currentUser, User $targetUser): bool
    {
        // Admin can edit anyone (handled by before).
        // Leaders can edit users if they have 'editar usuarios' (though seeder doesn't give this to them explicitly, so this will be false for leaders).
        // Users might be able to edit their own profile, which would be a separate check:
        // if ($currentUser->id === $targetUser->id) { return true; }
        // For now, sticking to role-based general user editing permission.
        return $currentUser->hasPermissionTo('editar usuarios');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $currentUser, User $targetUser): bool
    {
        // Only admin can delete users.
        return $currentUser->hasPermissionTo('eliminar usuarios');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $currentUser, User $targetUser): bool
    {
        return $currentUser->hasPermissionTo('eliminar usuarios'); // Assuming restore is tied to delete perm
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $currentUser, User $targetUser): bool
    {
        return $currentUser->hasPermissionTo('eliminar usuarios');
    }

    /**
     * Determine whether the user can assign roles to other users.
     * This corresponds to the 'asignar usuarios' permission for Leaders.
     */
    public function assignRoles(User $currentUser, User $targetUser): bool
    {
        // Admin can (handled by before).
        // Leaders can if they have the specific permission.
        if ($currentUser->hasRole('lider') && $currentUser->hasPermissionTo('asignar usuarios')) {
            return true;
        }
        return false;
    }
}
