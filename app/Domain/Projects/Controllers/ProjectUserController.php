<?php

namespace App\Domain\Projects\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\Projects\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProjectUserController extends Controller
{
    use AuthorizesRequests;

    public function index(Project $project)
    {
        $this->authorize('view', $project);
        return view('projects.users.index', [
            'project' => $project,
            'users' => $project->users
        ]);
    }

    public function create(Project $project)
    {
        $this->authorize('asignarUsuarios', $project);
        $availableUsers = User::whereDoesntHave('projects', function ($query) use ($project) {
            $query->where('project_id', $project->id);
        })->get();

        return view('projects.users.create', [
            'project' => $project,
            'availableUsers' => $availableUsers
        ]);
    }

    public function store(Request $request, Project $project)
    {
        $this->authorize('asignarUsuarios', $project);
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'roles' => 'required|array',
            'roles.*' => 'in:member,admin,viewer',
        ]);

        foreach ($request->user_ids as $userId) {
            $project->users()->attach($userId, [
                'role' => $request->roles[$userId]
            ]);
        }

        return redirect()
            ->route('projects.users.index', $project)
            ->with('success', 'Usuarios agregados exitosamente.');
    }

    public function destroy(Project $project, User $user)
    {
        $this->authorize('asignarUsuarios', $project);
        $project->users()->detach($user->id);

        return redirect()
            ->route('projects.users.index', $project)
            ->with('success', 'Usuario removido del proyecto exitosamente.');
    }
}
