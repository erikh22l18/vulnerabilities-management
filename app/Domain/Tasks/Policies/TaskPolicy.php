<?php

namespace App\Domain\Tasks\Policies;

use App\Models\User;
use App\Domain\Tasks\Models\Task;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Policy for Task model.
 * Defines authorization rules for task-related actions.
 *
 * @package App\Domain\Tasks\Policies
 */
class TaskPolicy
{
    use HandlesAuthorization;

    /**
     * Perform pre-authorization checks.
     * Admins are granted all permissions.
     *
     * @param  \App\Models\User  $user
     * @param  string  $ability
     * @return bool|null
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }
        return null; // Let other methods handle authorization
    }

    /**
     * Determine whether the user can view any tasks.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        // Lider can view any tasks.
        // Miembro can also view tasks, controller already filters by their projects.
        return $user->hasRole('lider') || $user->hasRole('miembro');
        // Or, for more specific permission:
        // return $user->can('view any tasks');
    }

    /**
     * Determine whether the user can view the given task.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Domain\Tasks\Models\Task  $task
     * @return bool
     */
    public function view(User $user, Task $task): bool
    {
        if ($user->hasRole('lider')) {
            return true;
        }

        // Miembro can view if task is in their project or assigned to them.
        if ($user->hasRole('miembro')) {
            $isAssignedToTask = $task->assigned_to == $user->id;
            // Ensure project relationship is loaded or handle potential null project
            $isTaskInTheirProject = $task->project && $task->project->users->contains($user->id);
            return $isAssignedToTask || $isTaskInTheirProject;
        }
        return false;
    }

    /**
     * Determine whether the user can create tasks.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function create(User $user): bool
    {
        // Lider can create tasks.
        // Miembro can create tasks (controller might add further project-specific restrictions).
        return $user->hasRole('lider') || $user->hasRole('miembro');
        // Or, for more specific permission:
        // return $user->can('create tasks');
    }

    /**
     * Determine whether the user can update the given task.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Domain\Tasks\Models\Task  $task
     * @return bool
     */
    public function update(User $user, Task $task): bool
    {
        if ($user->hasRole('lider')) {
            return true;
        }

        // Miembro can update if they created the task or are assigned to it.
        if ($user->hasRole('miembro')) {
            return $task->created_by == $user->id || $task->assigned_to == $user->id;
        }
        return false;
    }

    /**
     * Determine whether the user can delete the given task.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Domain\Tasks\Models\Task  $task
     * @return bool
     */
    public function delete(User $user, Task $task): bool
    {
        if ($user->hasRole('lider')) {
            return true;
        }

        // Miembro can delete if they created the task.
        if ($user->hasRole('miembro')) {
            return $task->created_by == $user->id;
        }
        return false;
    }
}
