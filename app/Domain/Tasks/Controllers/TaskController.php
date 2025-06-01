<?php

namespace App\Domain\Tasks\Controllers;

use \Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Domain\Tasks\Models\Task;
use App\Domain\Tasks\ViewModels\TaskIndexViewModel;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Domain\Projects\Models\Project;
use App\Models\User;
use App\Domain\Vulnerabilities\Models\Vulnerability;

class TaskController extends Controller
{
    use AuthorizesRequests;
    
    public function index()
    {
        $this->authorize('viewAny', Task::class);

        $user = Auth::user(); // Ensure $user is available
        $query = Task::with(['vulnerability', 'project', 'assignee']);

        if ($user->hasRole('miembro')) {
            $query->whereHas('vulnerability.project.users', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            });
        }

        $tasks = $query->orderBy('created_at', 'desc')
            ->paginate(10);

        $viewModel = new TaskIndexViewModel(
            title: 'Tareas',
            tasks: $tasks,
            backRoute: route('home'),
            createRoute: route('tasks.create'),
            can_create: Auth::user()->can('crear tareas'),

        );

        return view('tasks.index', compact('viewModel'));
    }

    public function show(Task $task)
    {
        $this->authorize('view', $task);

        // Load the task with its relationships
        $task->load(['project', 'assignee', 'creator']);
        $vulnerability = $task->vulnerability;


        return view('tasks.show', compact('task', 'vulnerability'));
    }

    /**
     * Show the form for creating a new task.
     *
     * @return \Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create(): \Illuminate\View\View
    {
        $this->authorize('create', Task::class);

        $projects = Project::all();
        $users = User::all();
        $vulnerabilities = Vulnerability::all();

        return view('tasks.create', compact('projects', 'users', 'vulnerabilities'));
    }

    /**
     * Store a newly created task in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $this->authorize('create', Task::class);

        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'vulnerability_id' => 'required|exists:vulnerabilities,id',
            'project_id' => 'nullable|exists:projects,id', // Made nullable to allow derivation
            'assigned_to' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
            'priority' => 'required|string|in:Baja,Media,Alta,Crítica',
            'status' => 'required|string|in:Pendiente,En Progreso,Completada',
        ]);

        $validatedData['created_by'] = auth()->id();

        // Ensure project_id is set, potentially deriving from vulnerability if not directly provided
        if (empty($validatedData['project_id']) && !empty($validatedData['vulnerability_id'])) {
            $vulnerability = Vulnerability::find($validatedData['vulnerability_id']);
            if ($vulnerability) {
                $validatedData['project_id'] = $vulnerability->project_id;
            }
        }
        // If project_id is still not set after trying to derive it, and it's required by the DB, this might fail.
        // The validation rule for project_id was made nullable; ensure DB schema allows nullable project_id for tasks
        // or always ensure it's provided/derived. For now, we assume it's okay if it ends up null if not derivable.


        $task = Task::create($validatedData);

        return redirect()->route('tasks.show', $task)->with('success', 'Tarea creada correctamente.');
    }

    /**
     * Show the form for editing the specified task.
     *
     * @param  \App\Domain\Tasks\Models\Task  $task
     * @return \Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit(Task $task): \Illuminate\View\View
    {
        $this->authorize('update', $task);

        $projects = Project::all();
        $users = User::all();
        $vulnerabilities = Vulnerability::all();

        return view('tasks.edit', compact('task', 'projects', 'users', 'vulnerabilities'));
    }

    /**
     * Update the specified task in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Domain\Tasks\Models\Task  $task
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, Task $task): \Illuminate\Http\RedirectResponse
    {
        $this->authorize('update', $task);

        $validatedData = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'vulnerability_id' => 'sometimes|required|exists:vulnerabilities,id',
            'project_id' => 'sometimes|nullable|exists:projects,id', // Made nullable
            'assigned_to' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
            'priority' => 'sometimes|required|string|in:Baja,Media,Alta,Crítica',
            'status' => 'sometimes|required|string|in:Pendiente,En Progreso,Completada',
        ]);

        // Ensure project_id is set if vulnerability_id changes or is provided
        if ($request->filled('vulnerability_id') && !empty($validatedData['vulnerability_id'])) {
            $vulnerability = Vulnerability::find($validatedData['vulnerability_id']);
            if ($vulnerability) {
                $validatedData['project_id'] = $vulnerability->project_id;
            }
        } elseif (array_key_exists('vulnerability_id', $validatedData) && is_null($validatedData['vulnerability_id'])) {
            // If vulnerability_id is explicitly set to null, project_id might also need to be nullable or handled.
            // For now, if project_id wasn't in $validatedData, it won't be changed.
            // If it was, it will be updated to its provided value (which could be null if validation allows).
        }


        $task->update($validatedData);

        return redirect()->route('tasks.show', $task)->with('success', 'Tarea actualizada correctamente.');
    }

    /**
     * Remove the specified task from storage.
     *
     * @param  \App\Domain\Tasks\Models\Task  $task
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Task $task): \Illuminate\Http\RedirectResponse
    {
        $this->authorize('delete', $task);
        $task->delete();

        return redirect()->route('tasks.index')->with('success', 'Tarea eliminada correctamente.');
    }
}
