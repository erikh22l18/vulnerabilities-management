<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Domain\Organizations\Models\Organization;
use App\Domain\Projects\Models\Project;
use Illuminate\View\View; // For type hinting views

/**
 * Controller for handling the main dashboard display.
 * Aggregates data from various parts of the application to provide an overview.
 *
 * @package App\Http\Controllers
 */
class DashboardController extends Controller
{
    /**
     * Display the main dashboard.
     *
     * Fetches organizations with their project counts, and projects with vulnerability statistics
     * (total vulnerabilities, treated vulnerabilities, and treatment percentage).
     * This data is then passed to the dashboard view.
     *
     * @return \Illuminate\View\View Returns the dashboard view with aggregated data.
     */
    public function index(): View
    {
        // Fetch all organizations and count their associated projects.
        $orgs = Organization::withCount('projects')->get();

        // Fetch all projects and count total vulnerabilities and "treated" vulnerabilities.
        // "Treated" vulnerabilities are those in 'Resuelta' (Resolved) or 'Cerrada' (Closed) states.
        $projects = Project::withCount([
            'vulnerabilities', // Counts all vulnerabilities for each project
            'vulnerabilities as treated_vulnerabilities_count' => function ($query) {
                // Counts vulnerabilities that are considered treated
                $query->whereIn('state', ['Resuelta', 'Cerrada']);
            }
        ])->get();

        // Calculate the treatment percentage for each project.
        foreach ($projects as $project) {
            if ($project->vulnerabilities_count > 0) {
                // Calculate percentage: (treated / total) * 100, rounded to 2 decimal places.
                $project->treatment_percentage = round(($project->treated_vulnerabilities_count / $project->vulnerabilities_count) * 100, 2);
            } else {
                // If a project has no vulnerabilities, its treatment percentage is 0.
                // Alternatively, this could be set to 'N/A' or null depending on display preference.
                $project->treatment_percentage = 0;
            }
        }

        // Pass the aggregated data to the 'dashboard' view.
        return view('dashboard', compact('orgs', 'projects'));
    }
}
