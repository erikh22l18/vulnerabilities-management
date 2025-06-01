<?php

namespace App\Domain\Users\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Domain\Organizations\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * Controller for managing the organization assignment of a user.
 *
 * @package App\Domain\Users\Controllers
 */
class UserOrganizationController extends Controller
{
    use AuthorizesRequests; // Laravel's trait for handling authorization

    /**
     * Show the form for editing the organization assignment of a specific user.
     * Displays all available organizations for selection.
     *
     * @param \App\Models\User $user The user whose organization is to be edited.
     * @return \Illuminate\View\View Returns the organization assignment form view.
     * @throws \Illuminate\Auth\Access\AuthorizationException If the current user is not authorized to update the target user.
     */
    public function edit(User $user): View
    {
        // Authorize if the current user can update the target user's details (which includes org assignment).
        $this->authorize('update', $user);

        $organizations = Organization::orderBy('name')->get(); // Get all organizations, ordered by name.
        return view('users.organization', compact('user', 'organizations'));
    }

    /**
     * Update the organization assignment for a specific user.
     *
     * @param \Illuminate\Http\Request $request The HTTP request containing the new organization ID.
     * @param \App\Models\User $user The user whose organization is to be updated.
     * @return \Illuminate\Http\RedirectResponse Redirects to the user index (or a relevant page) with a success message.
     * @throws \Illuminate\Auth\Access\AuthorizationException If the current user is not authorized to update the target user.
     * @throws \Illuminate\Validation\ValidationException If request validation fails.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        // Validate that 'organization_id' is a valid ID from the 'organizations' table, or null.
        $validated = $request->validate([
            'organization_id' => 'nullable|exists:organizations,id' // Allows unassigning by passing null
        ]);

        // Update the user's organization_id.
        $user->update($validated);

        // Consider redirecting to a more specific user management page.
        return redirect()->route('admin.users.index') // Assuming this is an admin function
            ->with('success', 'Organización del usuario actualizada exitosamente.');
        // Alternative redirects:
        // return redirect()->route('users.show', $user)->with('success', 'Organización actualizada exitosamente.');
        // return redirect()->back()->with('success', 'Organización actualizada exitosamente.');
    }
}