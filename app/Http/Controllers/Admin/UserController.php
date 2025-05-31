<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role; // Assuming Role model exists
use App\Models\Organization; // Assuming Organization model exists
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!Gate::allows('viewAny', User::class)) {
            abort(403);
        }
        $users = User::with('organization', 'roles')->paginate(10);
        // Define columns to display, similar to VulnerabilityController
        $columns = [
            'id' => 'ID',
            'name' => 'Name',
            'email' => 'Email',
            // Add other relevant columns here
        ];
        return view('admin.users.index', compact('users', 'columns'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!Gate::allows('create', User::class)) {
            abort(403);
        }
        $organizations = Organization::all();
        $roles = Role::all();
        return view('admin.users.create', compact('organizations', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!Gate::allows('create', User::class)) {
            abort(403);
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'organization_id' => 'required|exists:organizations,id',
            'role' => 'required|exists:roles,name', // Assuming Role model and roles table exist
        ]);

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'organization_id' => $validatedData['organization_id'],
        ]);

        $user->assignRole($validatedData['role']);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        if (!Gate::allows('view', $user)) {
            abort(403);
        }
        $user->load('organization', 'roles');
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        if (!Gate::allows('update', $user)) {
            abort(403);
        }
        $organizations = Organization::all();
        $roles = Role::all();
        $user->load('roles'); // Ensure roles are loaded for the form
        return view('admin.users.edit', compact('user', 'organizations', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        if (!Gate::allows('update', $user)) {
            abort(403);
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|min:8|confirmed',
            'organization_id' => 'required|exists:organizations,id',
            'role' => 'required|exists:roles,name',
        ]);

        $userData = [
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'organization_id' => $validatedData['organization_id'],
        ];

        if (!empty($validatedData['password'])) {
            $userData['password'] = Hash::make($validatedData['password']);
        }

        $user->update($userData);
        $user->syncRoles([$validatedData['role']]);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if (!Gate::allows('delete', $user)) {
            abort(403);
        }
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }
}
