<?php

namespace App\Domain\Tasks\Policies;

use App\Models\User;
use App\Domain\Tasks\Models\Task;
use App\Domain\Vulnerabilities\Models\Vulnerability;
use App\Domain\Projects\Models\Project; // Added this line
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
    public function create(User $user, $context = null): bool
    {
        // Basic permission check for creating any task
        // Assuming 'crear tareas' is the relevant permission.
        // Adjust if a different permission string is used or if no specific permission but only roles.
        if (method_exists($user, 'hasPermissionTo') && !$user->hasPermissionTo('crear tareas')) {
             return false;
        }

        if ($context instanceof Vulnerability) {
            // Ensure project relationship is loaded for the vulnerability
            $context->loadMissing('project');
            if (!$context->project || $context->project->status !== 'active') {
                return false;
            }
            if ($context->state === Vulnerability::STATE_CERRADA) {
                return false;
            }
        } elseif ($context instanceof Task) {
            $task = $context;
            // Ensure relationships are loaded for the task
            $task->loadMissing(['vulnerability.project', 'project']);

            if ($task->vulnerability_id && $task->vulnerability) {
                if (!$task->vulnerability->project || $task->vulnerability->project->status !== 'active') {
                    return false;
                }
                if ($task->vulnerability->state === Vulnerability::STATE_CERRADA) {
                    return false;
                }
            } elseif ($task->project_id && $task->project) {
                if ($task->project->status !== 'active') {
                    return false;
                }
            }
            // If no project/vulnerability association on the Task object,
            // no specific restriction from this part of the policy.
            // It might be a standalone task or one where association is checked by other means.
        } elseif ($context instanceof Project) {
            if ($context->status !== 'active') {
                return false;
            }
        }
        // If $context is null or another type, general task creation is allowed by this policy point.
        // It will then fall through to role-based checks.

        // Fallback to existing role-based permission if no specific context restriction applied earlier
        // and basic permission 'crear tareas' passed.
        return $user->hasRole('lider') || $user->hasRole('miembro');
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
        // Basic permission check (example, adjust if you have a specific 'update tasks' permission)
        // if (!$user->can('update tasks')) {
        //     return false;
        // }

        $task->loadMissing('vulnerability.project', 'project');

        // Check if associated with a vulnerability
        if ($task->vulnerability_id && $task->vulnerability) {
            if ($task->vulnerability->project->status !== 'active') {
                return false;
            }
            if ($task->vulnerability->state === Vulnerability::STATE_CERRADA) {
                return false;
            }
        }
        // Check if associated directly with a project (and not through a vulnerability)
        elseif ($task->project_id && $task->project) {
            if ($task->project->status !== 'active') {
                return false;
            }
        }
        // If the task has one of the IDs but the model didn't load, it's an issue.
        // Or if task must have one, and has neither. This might be better handled by validation.
        // else if ($task->vulnerability_id || $task->project_id) {
        //     return false;
        // }

        if ($user->hasRole('lider')) {
            return true;
        }

        // Miembro can update if they created the task or are assigned to it.
        // (And subject to above project/vulnerability status checks)
        if ($user->hasRole('miembro')) {
            // Potentially add more checks for miembro, e.g., if they belong to the project.
            // $projectToCheck = $task->vulnerability_id ? $task->vulnerability->project : $task->project;
            // if (!$projectToCheck || !$projectToCheck->users->contains($user->id)) {
            //     return false;
            // }
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
        // Basic permission check (example, adjust if you have a specific 'delete tasks' permission)
        // if (!$user->can('delete tasks')) {
        //     return false;
        // }

        $task->loadMissing('vulnerability.project', 'project');

        // Check if associated with a vulnerability
        if ($task->vulnerability_id && $task->vulnerability) {
            if ($task->vulnerability->project->status !== 'active') {
                return false;
            }
            if ($task->vulnerability->state === Vulnerability::STATE_CERRADA) {
                return false;
            }
        }
        // Check if associated directly with a project (and not through a vulnerability)
        elseif ($task->project_id && $task->project) {
            if ($task->project->status !== 'active') {
                return false;
            }
        }
        // If the task has one of the IDs but the model didn't load, it's an issue.
        // Or if task must have one, and has neither. This might be better handled by validation.
        // else if ($task->vulnerability_id || $task->project_id) {
        //     return false;
        // }

        if ($user->hasRole('lider')) {
            return true;
        }

        // Miembro can delete if they created the task.
        // (And subject to above project/vulnerability status checks)
        if ($user->hasRole('miembro')) {
            // Potentially add more checks for miembro, e.g., if they belong to the project.
            // $projectToCheck = $task->vulnerability_id ? $task->vulnerability->project : $task->project;
            // if (!$projectToCheck || !$projectToCheck->users->contains($user->id)) {
            //     return false;
            // }
            return $task->created_by == $user->id;
        }
        return false;
    }
}
