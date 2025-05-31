<?php

namespace App\Domain\Tasks\Policies;

use App\Models\User;
use App\Domain\Tasks\Models\Task;
use App\Domain\Vulnerabilities\Models\Vulnerability; // Needed for context
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class TaskPolicy
{
    use HandlesAuthorization;

    /**
     * Grant all abilities to admin users.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }
        return null; // Continue to other policy methods
    }

    /**
     * Determine whether the user can view any tasks related to a specific vulnerability.
     * This method is context-dependent on the vulnerability.
     */
    public function viewAnyInVulnerability(User $user, Vulnerability $vulnerability): bool
    {
        // If user is admin (handled by before), or leader with 'ver tareas' permission, allow.
        if ($user->hasPermissionTo('ver tareas') && $user->hasRole('lider')) {
            return true;
        }
        // Members with 'ver tareas' can view tasks if they can view the parent vulnerability
        // (which implies they are part of the project).
        if ($user->hasPermissionTo('ver tareas') && $user->hasRole('miembro')) {
            // We need to ensure the member can view the vulnerability itself.
            // This check relies on VulnerabilityPolicy@view logic.
            // A simple check here is if the vulnerability's project contains the user.
            return $vulnerability->project->users->contains($user->id);
        }
        return false;
    }

    /**
     * Determine whether the user can view the task.
     */
    public function view(User $user, Task $task): bool
    {
        // If user is admin (handled by before), or leader with 'ver tareas' permission, allow.
        if ($user->hasPermissionTo('ver tareas') && $user->hasRole('lider')) {
            return true;
        }
        // Members with 'ver tareas' can view the task if they can view its parent vulnerability.
        if ($user->hasPermissionTo('ver tareas') && $user->hasRole('miembro')) {
            return $task->vulnerability->project->users->contains($user->id);
        }
        return false;
    }

    /**
     * Determine whether the user can create tasks for a given vulnerability.
     */
    public function create(User $user, Vulnerability $vulnerability): bool
    {
        // If user is admin (handled by before), or leader with 'crear tareas' permission, allow.
        if ($user->hasPermissionTo('crear tareas') && $user->hasRole('lider')) {
            return true;
        }
        // Members with 'crear tareas' can create tasks if the parent vulnerability is assigned to them.
        if ($user->hasPermissionTo('crear tareas') && $user->hasRole('miembro')) {
            return $vulnerability->assigned_user_id == $user->id;
        }
        return false;
    }

    /**
     * Determine whether the user can update the task.
     */
    public function update(User $user, Task $task): bool
    {
        // If user is admin (handled by before), or leader with 'editar tareas' permission, allow.
        if ($user->hasPermissionTo('editar tareas') && $user->hasRole('lider')) {
            return true;
        }
        // Members with 'editar tareas' can update tasks if its parent vulnerability is assigned to them.
        if ($user->hasPermissionTo('editar tareas') && $user->hasRole('miembro')) {
            return $task->vulnerability->assigned_user_id == $user->id;
        }
        return false;
    }

    /**
     * Determine whether the user can delete the task.
     */
    public function delete(User $user, Task $task): bool
    {
        // If user is admin (handled by before), or leader with 'eliminar tareas' permission, allow.
        if ($user->hasPermissionTo('eliminar tareas') && $user->hasRole('lider')) {
            return true;
        }
        // Members generally should not delete tasks unless the parent vulnerability is assigned to them.
        // For stricter control, members might not be allowed to delete tasks at all.
        // Let's allow members to delete tasks if they have 'eliminar tareas' AND parent vulnerability is assigned.
        if ($user->hasPermissionTo('eliminar tareas') && $user->hasRole('miembro')) {
            return $task->vulnerability->assigned_user_id == $user->id;
        }
        return false;
    }

    /**
     * Determine whether the user can assign users to the task.
     * (This might be a separate permission like 'asignar tareas')
     */
    public function assignUser(User $user, Task $task): bool
    {
        // If user is admin (handled by before), or leader with 'asignar tareas' permission, allow.
        if ($user->hasPermissionTo('asignar tareas') && $user->hasRole('lider')) {
            return true;
        }
        // Members typically don't assign tasks to others.
        return false;
    }

    /**
     * Determine whether the user can view any task models (general list).
     */
    public function viewAny(User $user): bool
    {
        // Admin is handled by 'before' method.
        // Lider can view if they have 'ver tareas'.
        if ($user->hasRole('lider') && $user->hasPermissionTo('ver tareas')) {
            return true;
        }
        // Miembro can view if they have 'ver tareas' (controller will filter).
        if ($user->hasRole('miembro') && $user->hasPermissionTo('ver tareas')) {
            return true;
        }
        return false;
    }
}
