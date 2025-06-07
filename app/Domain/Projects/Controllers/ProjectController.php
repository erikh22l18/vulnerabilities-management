<?php

namespace App\Domain\Projects\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request; // Will be replaced by FormRequests in store/update/updateStatus
use App\Domain\Projects\Models\Project;
use App\Domain\Organizations\Models\Organization;
use App\Domain\Projects\Requests\StoreProjectRequest;       // Added
use App\Domain\Projects\Requests\UpdateProjectRequest;       // Added
use App\Domain\Projects\Requests\UpdateProjectStatusRequest; // Added
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
    public function store(StoreProjectRequest $request): RedirectResponse
    {
        // Authorization and validation are handled by StoreProjectRequest
        $validatedData = $request->validated();

        // Create the project record.
        // Note: If 'created_by' or 'lider_id' needs to be set, it should be added here
        // e.g., $validatedData['created_by'] = Auth::id();
        $project = Project::create($validatedData);

        // Optionally, assign the creating user to the project here if needed.
        // $user = Auth::user();
        // $project->users()->attach($user->id, ['role' => 'lider']); // Example role assignment

        return redirect()->route('projects.index')->with('success', 'Proyecto creado correctamente.');
    }

    /**
     * Display the specified project.
     *
     * @param  \App\Domain\Projects\Models\Project  $project
     * @return \Illuminate\View\View
     */
    public function show(Project $project): View
    {
        $this->authorize('view', $project); // Assuming a ProjectPolicy exists with a 'view' method
        $project->load('organization', 'users', 'vulnerabilities'); // Eager load some common relations
        return view('projects.show', compact('project'));
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
    public function update(UpdateProjectRequest $request, Project $project): RedirectResponse
    {
        // Authorization and validation are handled by UpdateProjectRequest
        $validatedData = $request->validated();

        // Update the project record.
        $project->update($validatedData);

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

        $vulnerabilityStatsByStatus = $vulnerabilities->groupBy('state')->map->count();
        $vulnerabilityStatsBySeverity = $vulnerabilities->groupBy('severity_level')->map->count();

        // Generate PDF using barryvdh/laravel-dompdf.
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('projects.report.pdf', compact('project', 'vulnerabilities', 'vulnerabilityStatsByStatus', 'vulnerabilityStatsBySeverity'))
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
    public function updateStatus(UpdateProjectStatusRequest $request, Project $project): RedirectResponse
    {
        // Authorization and validation are handled by UpdateProjectStatusRequest
        $validatedData = $request->validated();

        // The UpdateProjectStatusRequest already prepares 'status' to be lowercase via prepareForValidation()
        // So, $validatedData['status'] will be 'active' or 'inactive'.

        $project->update(['status' => $validatedData['status']]);

        return redirect()->route('projects.index')->with('success', 'Estado del proyecto actualizado correctamente.');
    }
}
