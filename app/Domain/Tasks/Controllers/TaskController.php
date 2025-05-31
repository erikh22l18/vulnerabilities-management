<?php

namespace App\Domain\Tasks\Controllers;

use \Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Domain\Tasks\Models\Task;
use App\Domain\Tasks\ViewModels\TaskIndexViewModel;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

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
}
