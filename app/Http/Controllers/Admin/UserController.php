<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Domain\Organizations\Models\Organization; // Corrected namespace
use Spatie\Permission\Models\Role; // Corrected namespace for Spatie Role model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * Controller for managing users in the admin panel.
 * Handles CRUD operations for users, including role and organization assignment.
 *
 * @package App\Http\Controllers\Admin
 */
class UserController extends Controller
{
    /**
     * Display a listing of the users.
     * Authorization is checked using the UserPolicy.
     *
     * @return \Illuminate\View\View Returns the user index view with paginated users and column definitions.
     */
    public function index(): View
    {
        // Authorize if the current user can view any users.
        if (!Gate::allows('viewAny', User::class)) {
            abort(403, 'Acción no autorizada.');
        }
        // Eager load organization and roles for each user. Paginate results.
        $users = User::with('organization', 'roles')->paginate(10);

        // Define columns for the user list table in the view.
        $columns = [
            'id' => 'ID',
            'name' => 'Nombre',
            'email' => 'Correo Electrónico',
            'organization.name' => 'Organización', // Example of accessing related model data
            'roles.0.name' => 'Rol', // Example: display first role name
        ];
        return view('admin.users.index', compact('users', 'columns'));
    }

    /**
     * Show the form for creating a new user.
     * Authorization is checked using the UserPolicy.
     *
     * @return \Illuminate\View\View Returns the user creation form view with organizations and roles for selection.
     */
    public function create(): View
    {
        if (!Gate::allows('create', User::class)) {
            abort(403, 'Acción no autorizada.');
        }
        // Provide all organizations and roles to the view for dropdowns.
        $organizations = Organization::all();
        $roles = Role::all(); // Assuming Spatie's Role model
        return view('admin.users.create', compact('organizations', 'roles'));
    }

    /**
     * Store a newly created user in storage.
     * Assigns the specified role to the user.
     * Authorization is checked using the UserPolicy.
     *
     * @param \Illuminate\Http\Request $request The HTTP request containing user data.
     * @return \Illuminate\Http\RedirectResponse Redirects to the user index with a success message.
     * @throws \Illuminate\Validation\ValidationException If request validation fails.
     */
    public function store(Request $request): RedirectResponse
    {
        if (!Gate::allows('create', User::class)) {
            abort(403, 'Acción no autorizada.');
        }

        // Validate incoming data.
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed', // Password must be confirmed
            'organization_id' => 'required|exists:organizations,id', // Ensure organization exists
            'role' => 'required|string|exists:roles,name', // Ensure role exists by name
        ]);

        // Create the user.
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']), // Hash the password
            'organization_id' => $validatedData['organization_id'],
        ]);

        // Assign the selected role to the user.
        $user->assignRole($validatedData['role']);

        return redirect()->route('admin.users.index')->with('success', 'Usuario creado correctamente.');
    }

    /**
     * Display the specified user.
     * Authorization is checked using the UserPolicy.
     *
     * @param \App\Models\User $user The user model instance.
     * @return \Illuminate\View\View Returns the user detail view.
     */
    public function show(User $user): View
    {
        if (!Gate::allows('view', $user)) {
            abort(403, 'Acción no autorizada.');
        }
        // Eager load organization and roles for display.
        $user->load('organization', 'roles');
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     * Authorization is checked using the UserPolicy.
     *
     * @param \App\Models\User $user The user model instance.
     * @return \Illuminate\View\View Returns the user edit form view with organizations and roles for selection.
     */
    public function edit(User $user): View
    {
        if (!Gate::allows('update', $user)) {
            abort(403, 'Acción no autorizada.');
        }
        $organizations = Organization::all();
        $roles = Role::all();
        $user->load('roles'); // Ensure roles are loaded to pre-select in the form.
        return view('admin.users.edit', compact('user', 'organizations', 'roles'));
    }

    /**
     * Update the specified user in storage.
     * If a password is provided, it will be updated. Roles are synced.
     * Authorization is checked using the UserPolicy.
     *
     * @param \Illuminate\Http\Request $request The HTTP request containing updated user data.
     * @param \App\Models\User $user The user model instance.
     * @return \Illuminate\Http\RedirectResponse Redirects to the user index with a success message.
     * @throws \Illuminate\Validation\ValidationException If request validation fails.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        if (!Gate::allows('update', $user)) {
            abort(403, 'Acción no autorizada.');
        }

        // Validate incoming data. Email uniqueness is checked, ignoring the current user.
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed', // Password is optional
            'organization_id' => 'required|exists:organizations,id',
            'role' => 'required|string|exists:roles,name',
        ]);

        // Prepare data for update.
        $userData = [
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'organization_id' => $validatedData['organization_id'],
        ];

        // If a new password is provided, hash and include it for update.
        if (!empty($validatedData['password'])) {
            $userData['password'] = Hash::make($validatedData['password']);
        }

        $user->update($userData);
        // Sync roles ensures the user only has the specified role.
        $user->syncRoles([$validatedData['role']]);

        return redirect()->route('admin.users.index')->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * Remove the specified user from storage.
     * Authorization is checked using the UserPolicy.
     *
     * @param \App\Models\User $user The user model instance.
     * @return \Illuminate\Http\RedirectResponse Redirects to the user index with a success message.
     */
    public function destroy(User $user): RedirectResponse
    {
        if (!Gate::allows('delete', $user)) {
            abort(403, 'Acción no autorizada.');
        }
        // Consider what happens to user's created content (soft delete, reassign, etc.).
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Usuario eliminado correctamente.');
    }
}
