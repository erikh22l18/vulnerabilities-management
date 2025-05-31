<?php

namespace App\Domain\Organizations\Controllers;

use App\Http\Controllers\Controller;
use \Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use App\Domain\Organizations\Models\Organization;
use Illuminate\Testing\Fluent\Concerns\Has;
use Illuminate\Support\Facades\Auth;

class OrganizationController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', Organization::class);
        $user = Auth::user();
        $query = Organization::withCount(['users', 'projects']);

        // Si es miembro, filtrar solo su organización
        if ($user->hasRole('miembro')) {
            $query->where('id', $user->organization_id);
        }

        $organizations = $query->orderBy('name')->paginate(8);

        return view('organizations.index', compact('organizations'));
    }

    public function create()
    {
        $this->authorize('create', Organization::class);
        return view('organizations.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Organization::class);
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'location'       => 'nullable|string|max:255',
            'business_model' => 'nullable|string|max:255',
        ]);

        $organization = Organization::create($validated);

        return redirect()->route('organizations.index')->with('success', 'Organización creada correctamente.');
    }

    public function edit(Organization $organization)
    {
        $this->authorize('update', $organization);
        return view('organizations.edit', compact('organization'));
    }

    public function update(Request $request, Organization $organization)
    {
        $this->authorize('update', $organization);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'business_model' => 'nullable|string|max:255',
        ]);

        $organization->update($validated);

        return redirect()->route('organizations.index')->with('success', 'Organización actualizada correctamente.');
    }

    public function destroy(Organization $organization)
    {
        $this->authorize('delete', $organization);
        $organization->delete();

        return redirect()->route('organizations.index')->with('success', 'Organización eliminada correctamente.');
    }
}
