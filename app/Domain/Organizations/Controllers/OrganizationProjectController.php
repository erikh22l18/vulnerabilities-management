<?php

namespace App\Domain\Organizations\Controllers;

use App\Domain\Organizations\Models\Organization;
use App\Domain\Projects\ViewModels\ProjectIndexViewModel;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class OrganizationProjectController extends Controller
{
    public function index(Organization $organization)
    {
        // $this->authorize('view', $organization);

        $projects = $organization->projects()
            ->with(['users', 'organization'])
            ->withCount('vulnerabilities')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $viewModel = new ProjectIndexViewModel(
            title: 'Proyectos de la Organización',
            subtitle: $organization->name,
            context: 'organization',
            projects: $projects,
            can_create: Auth::user()->can('crear proyectos'),
            createRoute: route('organizations.projects.create', $organization),
            backRoute: route('organizations.index')
        );

        return view('projects.index', compact('viewModel'));
    }
}