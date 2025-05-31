<?php

namespace App\Domain\Projects\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Domain\Projects\Models\Project; // Ya estaba, pero para confirmar
use App\Domain\Organizations\Models\Organization;
use Illuminate\Support\Str; // Añadido para Str::slug
use App\Domain\Projects\ViewModels\ProjectIndexViewModel;

class ProjectController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', Project::class);

        $user = Auth::user();
        $query = Project::with(['users', 'organization'])
            ->withCount('vulnerabilities');

        if ($user->hasRole('miembro')) {
            $query->whereHas('users', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            });
        }

        $projects = $query->orderBy('created_at', 'desc')
            ->paginate(6);

        $viewModel = new ProjectIndexViewModel(
            title: 'Proyectos',
            projects: $projects,
            can_create: $user->can('crear proyectos'),
            createRoute: route('projects.create')
        );

        return view('projects.index', compact('viewModel'));
    }

    public function create()
    {
        $this->authorize('create', Project::class);
        $organizations = Organization::all();
        return view('projects.create', compact('organizations'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Project::class);
        $validated = $request->validate([
            'identifier'      => 'required|string|max:100',
            'name'            => 'required|string|max:255',
            'general_objective' => 'required|string|max:500',
            'organization_id' => 'required|exists:organizations,id',
        ]);

        $project = Project::create($validated);

        return redirect()->route('projects.index')->with('success', 'Proyecto creado correctamente.');
    }

    public function edit(Project $project)
    {
        $this->authorize('update', $project);
        $organizations = Organization::all();
        return view('projects.edit', compact('project', 'organizations'));
    }

    public function update(Request $request, Project $project)
    {
        $this->authorize('update', $project);
        $validated = $request->validate([
            'identifier'      => 'required|string|max:100',
            'name'            => 'required|string|max:255',
            'general_objective' => 'required|string|max:500',
            'organization_id' => 'required|exists:organizations,id',
        ]);

        $project->update($validated);

        return redirect()->route('projects.index')->with('success', 'Proyecto actualizado correctamente.');
    }

    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);
        $project->delete();
        return redirect()->route('projects.index')->with('success', 'Proyecto eliminado correctamente.');
    }

    public function generateProjectReportPDF(Project $project)
    {
        $this->authorize('viewPdfReport', $project); // o una política específica para informes

        // Cargar el proyecto con sus vulnerabilidades y las relaciones necesarias
        $project->load([
            'organization', 
            'vulnerabilities', // Carga todas las vulnerabilidades del proyecto
            'vulnerabilities.type', // Carga el tipo de cada vulnerabilidad
            'vulnerabilities.category', // Carga la categoría de cada vulnerabilidad
            'vulnerabilities.creator', // Carga el creador de cada vulnerabilidad
            'vulnerabilities.assignedUsers' // Carga los usuarios asignados a cada vulnerabilidad
        ]);

        // Ordenar vulnerabilidades, por ejemplo, por estado o severidad
        // Para ordenar por múltiples criterios o de forma más compleja, se podría usar una colección y su método sortBy
        $vulnerabilities = $project->vulnerabilities->sortBy(function ($vulnerability) {
            // Ejemplo de ordenamiento: Estado (Detectada primero), luego Severidad (Crítica primero)
            $stateOrder = ['Detectada' => 1, 'Asignada' => 2, 'En tratamiento' => 3, 'Resuelta' => 4, 'Cerrada' => 5];
            $severityOrder = ['Crítica' => 1, 'Alta' => 2, 'Media' => 3, 'Baja' => 4, 'Informativa' => 5]; // Asumiendo 'Informativa'
            
            return sprintf('%s-%s-%s', 
                           $stateOrder[$vulnerability->state] ?? 99, 
                           $severityOrder[$vulnerability->severity_level] ?? 99,
                           $vulnerability->id // Como último criterio para mantener un orden estable
                        );
        });


        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('projects.report.pdf', compact('project', 'vulnerabilities'))
            ->setPaper('A4', 'landscape');

        return $pdf->download('proyecto_' . Str::slug($project->name) . '_vulnerabilidades_' . time() . '.pdf');
    }
}
