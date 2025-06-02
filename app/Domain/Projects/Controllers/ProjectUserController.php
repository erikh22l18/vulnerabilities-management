<?php

namespace App\Domain\Projects\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\Projects\Models\Project;
use App\Models\User;
use Illuminate\Http\Request; // Will be replaced by FormRequest in store method
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Domain\Projects\Requests\AssignProjectUsersRequest; // Added
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * Controller for managing user assignments and roles within a specific project.
 *
 * @package App\Domain\Projects\Controllers
 */
class ProjectUserController extends Controller
{
    use AuthorizesRequests; // Laravel's trait for handling authorization

    /**
     * Display a listing of users associated with the specified project.
     *
     * @param \App\Domain\Projects\Models\Project $project The project instance.
     * @return \Illuminate\View\View Returns the view listing users associated with the project.
     * @throws \Illuminate\Auth\Access\AuthorizationException If the user is not authorized to view the project.
     */
    public function index(Project $project): View
    {
        // Authorize if the user can view the project (which implies viewing its user assignments).
        $this->authorize('view', $project);
        return view('projects.users.index', [
            'project' => $project,
            'users' => $project->users // Assumes 'users' relationship is defined in Project model
        ]);
    }

    /**
     * Show the form for assigning new users to a project and defining their roles.
     * Lists users who are not already associated with this project.
     *
     * @param \App\Domain\Projects\Models\Project $project The project instance.
     * @return \Illuminate\View\View Returns the user assignment form view.
     * @throws \Illuminate\Auth\Access\AuthorizationException If the user is not authorized to manage user assignments for this project.
     */
    public function create(Project $project): View
    {
        // Authorize if the user can perform user assignment actions on this project.
        // 'asignarUsuarios' should be a defined ability in ProjectPolicy.
        $this->authorize('asignarUsuarios', $project);

        $organizationId = $project->organization_id;

        // Fetch users from the same organization who are not already part of this project.
        $availableUsers = User::where('organization_id', $organizationId)
            ->whereDoesntHave('projects', function ($query) use ($project) {
                $query->where('projects.id', $project->id); // Check against the id column of the projects table
            })
            ->orderBy('name')
            ->get();

        return view('projects.users.create', [
            'project' => $project,
            'availableUsers' => $availableUsers
        ]);
    }

    /**
     * Store new user assignments (with roles) for a project.
     *
     * @param \Illuminate\Http\Request $request The HTTP request containing user IDs and their roles.
     * @param \App\Domain\Projects\Models\Project $project The project instance.
     * @return \Illuminate\Http\RedirectResponse Redirects to the project's user list with a success message.
     * @throws \Illuminate\Auth\Access\AuthorizationException If the user is not authorized.
     * @throws \Illuminate\Validation\ValidationException If request validation fails.
     */
    public function store(AssignProjectUsersRequest $request, Project $project): RedirectResponse
    {
        // Authorization and validation (including organization check and role values)
        // are handled by AssignProjectUsersRequest.
        $validatedData = $request->validated();

        // Detach all existing users first to handle removals and role changes cleanly.
        // Or, if you want to preserve users not mentioned in the request, this logic needs adjustment.
        // For a "sync" like operation, detaching all and re-attaching is simpler.
        // However, the original logic was additive. Let's stick to additive but ensure roles are updated.
        // $project->users()->detach(); // Uncomment for a full sync approach

        $usersToSync = [];
        foreach ($validatedData['user_ids'] as $userId) {
            // The AssignProjectUsersRequest ensures roles are provided and valid for given user_ids if roles array is structured as roles[user_id]
            // If roles array is indexed, this logic needs to be different.
            // Assuming roles is an associative array keyed by user_id from the form,
            // and validated by 'roles.*'.
            $role = $validatedData['roles'][$userId] ?? 'miembro'; // Default role if not specified for a user_id

            // Ensure user belongs to the project's organization (this is now in FormRequest's withValidator)
            // $user = User::find($userId);
            // if ($user && $user->organization_id == $project->organization_id) {
            //    $usersToSync[$userId] = ['role' => $role];
            // }
            // The FormRequest already filtered user_ids by organization.
            // So, all user_ids in $validatedData['user_ids'] are valid to be added.
            $usersToSync[$userId] = ['role' => $role];
        }

        // Using sync instead of attach to handle updates and removals gracefully.
        // If a user_id is in $usersToSync, they will be attached or their role updated.
        // If a user was previously attached but not in $usersToSync, they will be detached.
        $project->users()->sync($usersToSync);

        return redirect()
            ->route('projects.users.index', $project)
            ->with('success', 'Usuarios agregados al proyecto exitosamente.');
    }

    /**
     * Remove (detach) a user from a project.
     *
     * @param \App\Domain\Projects\Models\Project $project The project instance.
     * @param \App\Models\User $user The user to detach from the project.
     * @return \Illuminate\Http\RedirectResponse Redirects to the project's user list with a success message.
     * @throws \Illuminate\Auth\Access\AuthorizationException If the user is not authorized.
     */
    public function destroy(Project $project, User $user): RedirectResponse
    {
        $this->authorize('asignarUsuarios', $project); // Or a more specific 'removeUser' policy

        // Detach the user from the project. This removes the entry from the pivot table.
        $project->users()->detach($user->id);

        return redirect()
            ->route('projects.users.index', $project)
            ->with('success', 'Usuario removido del proyecto exitosamente.');
    }
}
