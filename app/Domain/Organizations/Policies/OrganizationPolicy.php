<?php

namespace App\Domain\Organizations\Policies;

use App\Models\User;
use App\Domain\Organizations\Models\Organization;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrganizationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any organizations.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('ver organizaciones');
    }

    /**
     * Determine whether the user can view the organization.
     */
    public function view(User $user, Organization $organization): bool
    {
        if (!$user->hasPermissionTo('ver organizaciones')) {
            return false;
        }

        if ($user->hasRole(['admin', 'lider'])) {
            return true;
        }

        return $user->organization_id === $organization->id;
    }

    /**
     * Determine whether the user can create organizations.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the organization.
     */
    public function update(User $user, Organization $organization): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the organization.
     */
    public function delete(User $user, Organization $organization): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the organization.
     */
    public function restore(User $user, Organization $organization): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the organization.
     */
    public function forceDelete(User $user, Organization $organization): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can view projects of the organization.
     */
    public function viewProjects(User $user, Organization $organization): bool
    {
        // Permiso básico
        if (!$user->hasPermissionTo('ver proyectos')) {
            return false;
        }

        // Administradores y líderes pueden ver proyectos de todas las organizaciones
        if ($user->hasRole(['admin', 'lider'])) {
            return true;
        }

        // Miembros solo pueden ver proyectos de su propia organización
        return $user->organization_id === $organization->id;
    }

    /**
     * Determine whether the user can add projects to the organization.
     */
    public function addProjects(User $user, Organization $organization): bool
    {
        // Permiso básico
        if (!$user->hasPermissionTo('crear proyectos')) {
            return false;
        }

        // Administradores pueden agregar proyectos a cualquier organización
        if ($user->hasRole('admin')) {
            return true;
        }

        // Líderes solo pueden agregar proyectos a su organización
        if ($user->hasRole('lider')) {
            return $user->organization_id === $organization->id;
        }

        return false;
    }
}