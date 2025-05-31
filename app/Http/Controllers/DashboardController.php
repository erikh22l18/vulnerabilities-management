<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Domain\Organizations\Models\Organization;
use App\Domain\Projects\Models\Project;



class DashboardController extends Controller
{
    public function index()
    {
        $orgs = Organization::withCount('projects')->get();

        $projects = Project::withCount([
            'vulnerabilities', // Total de vulnerabilidades
            'vulnerabilities as treated_vulnerabilities_count' => function ($query) {
                $query->whereIn('state', ['Resuelta', 'Cerrada']); // Estados considerados como tratamiento completado
            }
        ])->get();

        // Calcular porcentaje de tratamiento para cada proyecto
        foreach ($projects as $project) {
            if ($project->vulnerabilities_count > 0) {
                $project->treatment_percentage = round(($project->treated_vulnerabilities_count / $project->vulnerabilities_count) * 100, 2);
            } else {
                $project->treatment_percentage = 0; // O 'N/A' si se prefiere mostrar texto
            }
        }

        return view('dashboard', compact('orgs', 'projects'));
    }
}
