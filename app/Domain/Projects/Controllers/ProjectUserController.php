<?php

namespace App\Domain\Projects\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\Projects\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
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
    public function store(Request $request, Project $project): RedirectResponse
    {
        $this->authorize('asignarUsuarios', $project);

        // Validate incoming data.
        $validated = $request->validate([
            'user_ids' => 'required|array', // Expect an array of user IDs
            'user_ids.*' => 'exists:users,id', // Each user ID must exist
            'roles' => 'required|array', // Expect an array of roles, indexed by user_id
            'roles.*' => 'required|string|in:member,admin,viewer', // Validate role values (adjust as per your defined roles)
        ]);

        // Attach each selected user to the project with their specified role.
        foreach ($validated['user_ids'] as $userId) {
            // Ensure a role is provided for each user ID.
            if (isset($validated['roles'][$userId])) {
                $project->users()->attach($userId, [
                    'role' => $validated['roles'][$userId] // Pivot table 'role' column
                ]);
            }
        }

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
