@extends('layouts.admin')

@section('content')
    <div class="container">
        <h1>User Details</h1>

        <table class="table">
            <tbody>
                <tr>
                    <th>ID</th>
                    <td>{{ $user->id }}</td>
                </tr>
                <tr>
                    <th>Name</th>
                    <td>{{ $user->name }}</td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>{{ $user->email }}</td>
                </tr>
                <tr>
                    <th>Role</th>
                    <td>{{ $user->roles->isNotEmpty() ? $user->roles->first()->name : 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Organización</th>
                    <td>{{ $user->organization ? $user->organization->name : 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Created At</th>
                    <td>{{ $user->created_at }}</td>
                </tr>
                <tr>
                    <th>Updated At</th>
                    <td>{{ $user->updated_at }}</td>
                </tr>
            </tbody>
        </table>

        <a href="{{ route('admin.users.index') }}" class="btn btn-primary">Back to List</a>
    </div>
@endsection
