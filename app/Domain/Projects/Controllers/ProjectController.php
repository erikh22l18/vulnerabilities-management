<?php

namespace App\Domain\Projects\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Domain\Projects\Models\Project;
use App\Domain\Organizations\Models\Organization;
use Illuminate\Support\Str; // For Str::slug
use App\Domain\Projects\ViewModels\ProjectIndexViewModel;
use Illuminate\View\View; // For type hinting views
use Illuminate\Http\RedirectResponse; // For type hinting redirects
use Illuminate\Http\Response as IlluminateResponse; // For PDF download response

/**
 * Controller for managing projects.
 * Handles CRUD operations for projects and generation of project-specific reports.
 *
 * @package App\Domain\Projects\Controllers
 */
class ProjectController extends Controller
{
    use AuthorizesRequests; // Laravel's trait for handling authorization

    /**
     * Display a listing of the projects.
     *
     * Users with 'miembro' role will only see projects they are associated with.
     * Others can see all projects, subject to policy.
     *
     * @return \Illuminate\View\View Returns the project index view with paginated projects.
     */
    public function index(): View
    {
        // Authorize if the user can view any projects.
        $this->authorize('viewAny', Project::class);

        $user = Auth::user();
        // Eager load relationships and count vulnerabilities for efficiency.
        $query = Project::with(['users', 'organization'])
            ->withCount('vulnerabilities');

        // Filter projects for users with the 'miembro' (member) role.
        if ($user->hasRole('miembro')) {
            $query->whereHas('users', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            });
        }

        // Order by creation date and paginate results.
        $projects = $query->orderBy('created_at', 'desc')
            ->paginate(6); // Paginate with 6 items per page.

        // Prepare data for the view using a ViewModel.
        $viewModel = new ProjectIndexViewModel(
            title: 'Proyectos',
            projects: $projects,
            can_create: $user->can('create', Project::class), // Check if user can create new projects
            createRoute: route('projects.create') // Route for creating new projects
        );

        return view('projects.index', compact('viewModel'));
    }

    /**
     * Show the form for creating a new project.
     *
     * @return \Illuminate\View\View Returns the project creation form view.
     * @throws \Illuminate\Auth\Access\AuthorizationException If the user is not authorized to create projects.
     */
    public function create(): View
    {
        // Authorize if the user can create projects.
        $this->authorize('create', Project::class);
        // Fetch all organizations for the dropdown in the form.
        $organizations = Organization::all();
        return view('projects.create', compact('organizations'));
    }

    /**
     * Store a newly created project in storage.
     *
     * @param \Illuminate\Http\Request $request The HTTP request containing project data.
     * @return \Illuminate\Http\RedirectResponse Redirects to the project index with a success message.
     * @throws \Illuminate\Auth\Access\AuthorizationException If the user is not authorized to create projects.
     * @throws \Illuminate\Validation\ValidationException If request validation fails.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Project::class);

        // Validate the incoming request data.
        $validated = $request->validate([
            'identifier'      => 'required|string|max:100|unique:projects,identifier', // Ensure identifier is unique
            'name'            => 'required|string|max:255',
            'general_objective' => 'required|string|max:500',
            'organization_id' => 'required|exists:organizations,id', // Ensure organization exists
        ]);

        // Create the project record.
        $project = Project::create($validated);

        // Optionally, assign the creating user to the project here if needed.
        // $user = Auth::user();
        // $project->users()->attach($user->id, ['role' => 'lider']); // Example role assignment

        return redirect()->route('projects.index')->with('success', 'Proyecto creado correctamente.');
    }

    /**
     * Show the form for editing the specified project.
     *
     * @param \App\Domain\Projects\Models\Project $project The project model instance.
     * @return \Illuminate\View\View Returns the project edit form view.
     * @throws \Illuminate\Auth\Access\AuthorizationException If the user is not authorized to update the project.
     */
    public function edit(Project $project): View
    {
        $this->authorize('update', $project);
        // Fetch all organizations for the dropdown in the form.
        $organizations = Organization::all();
        return view('projects.edit', compact('project', 'organizations'));
    }

    /**
     * Update the specified project in storage.
     *
     * @param \Illuminate\Http\Request $request The HTTP request containing updated project data.
     * @param \App\Domain\Projects\Models\Project $project The project model instance.
     * @return \Illuminate\Http\RedirectResponse Redirects to the project index with a success message.
     * @throws \Illuminate\Auth\Access\AuthorizationException If the user is not authorized to update the project.
     * @throws \Illuminate\Validation\ValidationException If request validation fails.
     */
    public function update(Request $request, Project $project): RedirectResponse
    {
        $this->authorize('update', $project);

        // Validate the incoming request data.
        $validated = $request->validate([
            'identifier'      => 'required|string|max:100|unique:projects,identifier,' . $project->id, // Ensure identifier is unique, ignoring current project
            'name'            => 'required|string|max:255',
            'general_objective' => 'required|string|max:500',
            'organization_id' => 'required|exists:organizations,id', // Ensure organization exists
        ]);

        // Update the project record.
        $project->update($validated);

        return redirect()->route('projects.index')->with('success', 'Proyecto actualizado correctamente.');
    }

    /**
     * Remove the specified project from storage.
     *
     * @param \App\Domain\Projects\Models\Project $project The project model instance.
     * @return \Illuminate\Http\RedirectResponse Redirects to the project index with a success message.
     * @throws \Illuminate\Auth\Access\AuthorizationException If the user is not authorized to delete the project.
     */
    public function destroy(Project $project): RedirectResponse
    {
        $this->authorize('delete', $project);
        // Consider implications: what happens to associated vulnerabilities, tasks, etc.?
        // Soft deletes might be an option, or explicit cleanup logic.
        $project->delete();
        return redirect()->route('projects.index')->with('success', 'Proyecto eliminado correctamente.');
    }

    /**
     * Generate a PDF report for the specified project, including its vulnerabilities.
     *
     * @param \App\Domain\Projects\Models\Project $project The project model instance.
     * @return \Illuminate\Http\Response Returns a PDF download response.
     * @throws \Illuminate\Auth\Access\AuthorizationException If the user is not authorized to view the project's PDF report.
     */
    public function generateProjectReportPDF(Project $project): IlluminateResponse
    {
        // Authorize if the user can view the PDF report for this project.
        // This might be the same as 'view' or a more specific permission like 'viewPdfReport'.
        $this->authorize('viewPdfReport', $project);

        // Eager load necessary relationships for the report.
        $project->load([
            'organization', 
            'vulnerabilities', // Load all vulnerabilities related to this project
            'vulnerabilities.type', // For each vulnerability, load its type
            'vulnerabilities.category', // For each vulnerability, load its category
            'vulnerabilities.creator', // For each vulnerability, load the user who created it
            'vulnerabilities.assignedUsers' // For each vulnerability, load assigned users
        ]);

        // Sort vulnerabilities for consistent reporting.
        // Example: Sort by state (e.g., 'Detectada' first), then by severity (e.g., 'Crítica' first).
        $vulnerabilities = $project->vulnerabilities->sortBy(function ($vulnerability) {
            $stateOrder = array_flip(Project::VULNERABILITY_STATE_ORDER_FOR_REPORTING ?? ['Detectada', 'Asignada', 'En tratamiento', 'Resuelta', 'Cerrada']);
            $severityOrder = array_flip(Project::VULNERABILITY_SEVERITY_ORDER_FOR_REPORTING ?? ['Crítica', 'Alta', 'Media', 'Baja', 'Informativa']);
            
            return sprintf('%02d-%02d-%s',
                           $stateOrder[$vulnerability->state] ?? 99, 
                           $severityOrder[$vulnerability->severity_level] ?? 99,
                           $vulnerability->id // Fallback sort by ID for stability
                        );
        });

        // Generate PDF using barryvdh/laravel-dompdf.
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('projects.report.pdf', compact('project', 'vulnerabilities'))
            ->setPaper('A4', 'landscape'); // Set paper size to A4 landscape

        // Download the PDF with a dynamic, descriptive name.
        return $pdf->download('proyecto_' . Str::slug($project->name) . '_vulnerabilidades_' . time() . '.pdf');
    }

    /**
     * Update the status of the specified project.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Domain\Projects\Models\Project  $project
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function updateStatus(Request $request, Project $project): RedirectResponse
    {
        $this->authorize('update', $project); // Or a more specific 'updateStatus' permission

        $validated = $request->validate([
            'status' => 'required|string|in:active,inactive,activo,inactivo', // Add 'activo' and 'inactivo' if those are used
        ]);

        // Normalize status to 'active' or 'inactive' if 'activo'/'inactivo' are used
        $statusToUpdate = strtolower($validated['status']);
        if ($statusToUpdate === 'activo') {
            $statusToUpdate = 'active';
        } elseif ($statusToUpdate === 'inactivo') {
            $statusToUpdate = 'inactive';
        }


        $project->update(['status' => $statusToUpdate]);

        return redirect()->route('projects.index')->with('success', 'Estado del proyecto actualizado correctamente.');
    }
}
