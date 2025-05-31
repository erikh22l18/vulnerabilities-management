<?php

namespace App\Domain\Users\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Domain\Organizations\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserOrganizationController extends Controller
{
    use AuthorizesRequests;

    public function edit(User $user)
    {
        $this->authorize('update', $user);
        $organizations = Organization::all();
        return view('users.organization', compact('user', 'organizations'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);
        $validated = $request->validate([
            'organization_id' => 'nullable|exists:organizations,id'
        ]);

        $user->update($validated);

        return redirect()->route('users.index')
            ->with('success', 'Organización actualizada exitosamente.');
    }
}