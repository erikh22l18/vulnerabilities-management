<?php

namespace App\Domain\Organizations\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\Organizations\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request; // Will be replaced by FormRequests
use Illuminate\Support\Facades\Hash; // Import Hash facade
use App\Domain\Organizations\Requests\StoreOrganizationUserRequest; // Added
use App\Domain\Organizations\Requests\UpdateOrganizationUserRequest; // Added
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // For authorization

/**
 * Controller for managing users within a specific organization.
 * Handles listing, adding (new or existing), editing, and removing users from an organization.
 *
 * @package App\Domain\Organizations\Controllers
 */
class OrganizationUserController extends Controller
{
    use AuthorizesRequests; // Use authorization trait

    /**
     * Display a listing of users belonging to the specified organization.
     *
     * @param \App\Domain\Organizations\Models\Organization $organization The organization instance.
     * @return \Illuminate\View\View Returns the view listing users of the organization.
     * @throws \Illuminate\Auth\Access\AuthorizationException If the user is not authorized to view users of this organization.
     */
    public function index(Organization $organization): View
    {
        // Example authorization: Ensure the user can view the organization itself.
        $this->authorize('view', $organization);
        $users = $organization->users()->paginate(10); // Paginate the users list.
        return view('organizations.users.index', compact('organization', 'users'));
    }

    /**
     * Show the form for adding users to the specified organization.
     * This includes options to create a new user or add existing unassigned users.
     *
     * @param \App\Domain\Organizations\Models\Organization $organization The organization instance.
     * @return \Illuminate\View\View Returns the form view.
     * @throws \Illuminate\Auth\Access\AuthorizationException If the user is not authorized to manage users for this organization.
     */
    public function create(Organization $organization): View
    {
        // Example authorization: Ensure the user can update the organization (implies managing its users).
        $this->authorize('update', $organization);
        // Fetch users who do not currently belong to any organization.
        $availableUsers = User::whereNull('organization_id')->orderBy('name')->get();
        return view('organizations.users.create', compact('organization', 'availableUsers'));
    }

    /**
     * Store new or existing users and associate them with the specified organization.
     *
     * @param \Illuminate\Http\Request $request The HTTP request.
     * @param \App\Domain\Organizations\Models\Organization $organization The organization instance.
     * @return \Illuminate\Http\RedirectResponse Redirects to the organization's user list with a success message.
     * @throws \Illuminate\Auth\Access\AuthorizationException If the user is not authorized.
     * @throws \Illuminate\Validation\ValidationException If request validation fails.
     */
    public function store(StoreOrganizationUserRequest $request, Organization $organization): RedirectResponse
    {
        // Authorization and conditional validation are handled by StoreOrganizationUserRequest
        $validatedData = $request->validated();

        // Check if the request was to create a new user (based on which rules were applied/passed)
        if (isset($validatedData['name']) && isset($validatedData['email']) && isset($validatedData['password'])) {
            // Logic for creating a new user
            User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
                'organization_id' => $organization->id
                // 'area' could be $validatedData['area'] if added to form & request rules
            ]);
            $message = 'Usuario nuevo creado y agregado a la organización exitosamente.';
        } elseif (isset($validatedData['user_ids'])) {
            // Logic for adding existing users
            // The FormRequest does not currently filter these user_ids to ensure they are not already in an org,
            // but the controller's original logic did. This might need to be added back or handled by DB constraints/policy.
            // For now, we'll replicate the controller's intent to only update users not already in an org.
            User::whereIn('id', $validatedData['user_ids'])
                ->whereNull('organization_id') // Only assign if not already in an organization
                ->update(['organization_id' => $organization->id]);
            $message = 'Usuarios existentes agregados a la organización exitosamente.';
        } else {
            // Should not be reached if FormRequest rules are correctly triggered by input
            return redirect()->back()->with('error', 'Solicitud inválida.');
        }

        return redirect()->route('organizations.users.index', $organization)
            ->with('success', $message);
    }

    /**
     * Show the form for editing a user's details within an organization context.
     * (Currently, this might be similar to general user edit, but could be specific).
     *
     * @param \App\Domain\Organizations\Models\Organization $organization The organization instance.
     * @param \App\Models\User $user The user instance to edit.
     * @return \Illuminate\View\View Returns the edit form view.
     * @throws \Illuminate\Auth\Access\AuthorizationException If the user is not authorized.
     */
    public function edit(Organization $organization, User $user): View
    {
        // Authorize if the user can update other users, potentially restricted to their own org.
        $this->authorize('update', $user);
        // Ensure the user actually belongs to this organization before editing in this context.
        if ($user->organization_id !== $organization->id) {
            abort(404, 'Usuario no encontrado en esta organización.');
        }
        return view('organizations.users.edit', compact('organization', 'user'));
    }

    /**
     * Update the specified user's details.
     *
     * @param \Illuminate\Http\Request $request The HTTP request.
     * @param \App\Domain\Organizations\Models\Organization $organization The organization instance (for context).
     * @param \App\Models\User $user The user instance to update.
     * @return \Illuminate\Http\RedirectResponse Redirects to the organization's user list.
     * @throws \Illuminate\Auth\Access\AuthorizationException If the user is not authorized.
     * @throws \Illuminate\Validation\ValidationException If request validation fails.
     */
    public function update(UpdateOrganizationUserRequest $request, Organization $organization, User $user): RedirectResponse
    {
        // Authorization is handled by UpdateOrganizationUserRequest (checks if current user can update $user)
        // Additional check: Ensure the $user being updated actually belongs to the $organization from the route.
        if ($user->organization_id !== $organization->id) {
            abort(403, 'Este usuario no pertenece a la organización especificada.');
        }

        $validatedData = $request->validated();

        // Password handling: If password fields are added to UpdateOrganizationUserRequest and are present in $validatedData
        // if (!empty($validatedData['password'])) {
        //     $validatedData['password'] = Hash::make($validatedData['password']);
        // } else {
        //     unset($validatedData['password']); // Ensure password isn't updated if not provided
        // }

        $user->update($validatedData);

        return redirect()->route('organizations.users.index', $organization)
            ->with('success', 'Usuario actualizado exitosamente.');
    }

    /**
     * Remove (dissociate) a user from the specified organization.
     * This action sets the user's organization_id to null, rather than deleting the user.
     *
     * @param \App\Domain\Organizations\Models\Organization $organization The organization instance.
     * @param \App\Models\User $user The user to remove from the organization.
     * @return \Illuminate\Http\RedirectResponse Redirects to the organization's user list.
     * @throws \Illuminate\Auth\Access\AuthorizationException If the user is not authorized.
     */
    public function destroy(Organization $organization, User $user): RedirectResponse
    {
        // Authorize if the current user can remove users from this organization.
        $this->authorize('update', $organization);
        if ($user->organization_id !== $organization->id) {
            abort(404); // Or redirect with error if user not in this org
        }

        // Dissociate the user from the organization by setting organization_id to null.
        $user->update(['organization_id' => null]);

        return redirect()->route('organizations.users.index', $organization)
            ->with('success', 'Usuario removido de la organización exitosamente.');
    }
}
