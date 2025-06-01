<?php

namespace App\Domain\Organizations\Controllers;

use App\Http\Controllers\Controller;
use \Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use App\Domain\Organizations\Models\Organization;
// use Illuminate\Testing\Fluent\Concerns\Has; // This import seems unused.
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View; // For type hinting views
use Illuminate\Http\RedirectResponse; // For type hinting redirects

/**
 * Controller for managing organizations.
 * Handles CRUD operations for organizations.
 *
 * @package App\Domain\Organizations\Controllers
 */
class OrganizationController extends Controller
{
    use AuthorizesRequests; // Laravel's trait for handling authorization

    /**
     * Display a listing of the organizations.
     *
     * Users with 'miembro' role will only see their own organization.
     * Others can see all organizations, subject to policy.
     *
     * @return \Illuminate\View\View Returns the organization index view with paginated organizations.
     */
    public function index(): View
    {
        // Authorize if the user can view any organizations.
        $this->authorize('viewAny', Organization::class);
        $user = Auth::user();
        // Eager load counts of users and projects for efficiency.
        $query = Organization::withCount(['users', 'projects']);

        // If the user is a 'miembro' (member), filter to show only their organization.
        if ($user->hasRole('miembro') && $user->organization_id) {
            $query->where('id', $user->organization_id);
        }

        // Order by name and paginate results.
        $organizations = $query->orderBy('name')->paginate(8); // Paginate with 8 items per page.

        return view('organizations.index', compact('organizations'));
    }

    /**
     * Show the form for creating a new organization.
     *
     * @return \Illuminate\View\View Returns the organization creation form view.
     * @throws \Illuminate\Auth\Access\AuthorizationException If the user is not authorized to create organizations.
     */
    public function create(): View
    {
        // Authorize if the user can create organizations.
        $this->authorize('create', Organization::class);
        return view('organizations.create');
    }

    /**
     * Store a newly created organization in storage.
     *
     * @param \Illuminate\Http\Request $request The HTTP request containing organization data.
     * @return \Illuminate\Http\RedirectResponse Redirects to the organization index with a success message.
     * @throws \Illuminate\Auth\Access\AuthorizationException If the user is not authorized to create organizations.
     * @throws \Illuminate\Validation\ValidationException If request validation fails.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Organization::class);

        // Validate the incoming request data.
        $validated = $request->validate([
            'name'           => 'required|string|max:255|unique:organizations,name', // Name should be unique.
            'location'       => 'nullable|string|max:255',
            'business_model' => 'nullable|string|max:255',
        ]);

        // Create the organization record.
        $organization = Organization::create($validated);

        return redirect()->route('organizations.index')->with('success', 'Organización creada correctamente.');
    }

    /**
     * Show the form for editing the specified organization.
     *
     * @param \App\Domain\Organizations\Models\Organization $organization The organization model instance.
     * @return \Illuminate\View\View Returns the organization edit form view.
     * @throws \Illuminate\Auth\Access\AuthorizationException If the user is not authorized to update the organization.
     */
    public function edit(Organization $organization): View
    {
        $this->authorize('update', $organization);
        return view('organizations.edit', compact('organization'));
    }

    /**
     * Update the specified organization in storage.
     *
     * @param \Illuminate\Http\Request $request The HTTP request containing updated organization data.
     * @param \App\Domain\Organizations\Models\Organization $organization The organization model instance.
     * @return \Illuminate\Http\RedirectResponse Redirects to the organization index with a success message.
     * @throws \Illuminate\Auth\Access\AuthorizationException If the user is not authorized to update the organization.
     * @throws \Illuminate\Validation\ValidationException If request validation fails.
     */
    public function update(Request $request, Organization $organization): RedirectResponse
    {
        $this->authorize('update', $organization);

        // Validate the incoming request data.
        $validated = $request->validate([
            // Name should be unique, ignoring the current organization's name.
            'name' => 'required|string|max:255|unique:organizations,name,' . $organization->id,
            'location' => 'nullable|string|max:255',
            'business_model' => 'nullable|string|max:255',
        ]);

        // Update the organization record.
        $organization->update($validated);

        return redirect()->route('organizations.index')->with('success', 'Organización actualizada correctamente.');
    }

    /**
     * Remove the specified organization from storage.
     *
     * @param \App\Domain\Organizations\Models\Organization $organization The organization model instance.
     * @return \Illuminate\Http\RedirectResponse Redirects to the organization index with a success message.
     * @throws \Illuminate\Auth\Access\AuthorizationException If the user is not authorized to delete the organization.
     */
    public function destroy(Organization $organization): RedirectResponse
    {
        $this->authorize('delete', $organization);
        // Consider implications: what happens to associated users, projects?
        // Soft deletes or cascading constraints might be needed depending on requirements.
        $organization->delete();

        return redirect()->route('organizations.index')->with('success', 'Organización eliminada correctamente.');
    }
}
