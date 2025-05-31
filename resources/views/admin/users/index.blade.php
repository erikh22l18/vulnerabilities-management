@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 sm:px-8">
    <div class="py-8">
        <div>
            <h2 class="text-2xl font-semibold leading-tight">Usuarios</h2>
        </div>
        <div class="my-2 flex sm:flex-row flex-col">
            <div class="flex flex-row mb-1 sm:mb-0">
                {{-- Add search/filter options here if needed in the future --}}
            </div>
            <div class="block relative">
                <a href="{{ route('admin.users.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Crear Nuevo Usuario
                </a>
            </div>
        </div>
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif
        <div class="overflow-x-auto">
            <div class="inline-block min-w-full shadow rounded-lg overflow-hidden">
                <table class="min-w-full leading-normal">
                    <thead>
                        <tr>
                            @foreach ($columns as $column => $displayName)
                                <th
                                    class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    {{ $displayName }}
                                </th>
                            @endforeach
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        @forelse ($users as $user)
                            <tr>
                                @foreach ($columns as $column => $displayName)
                                    <td class="px-5 py-3 border-b border-gray-200 bg-white text-sm">
                                        <p class="text-gray-900 whitespace-no-wrap">
                                            @if ($key === 'organization')
                                                {{ $user->organization->name ?? 'N/A' }}
                                            @elseif ($key === 'roles')
                                                {{ $user->roles->pluck('name')->join(', ') }}
                                            @else
                                                {{ $user->$column }}
                                            @endif
                                        </p>
                                    </td>
                                @endforeach
                                <td class="px-5 py-3 border-b border-gray-200 bg-white text-sm">
                                    <a href="{{ route('admin.users.show', $user->id) }}" class="text-blue-600 hover:text-blue-900 mr-2">Ver</a>
                                    <a href="{{ route('admin.users.edit', $user->id) }}" class="text-yellow-600 hover:text-yellow-900 mr-2">Editar</a>
                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('¿Estás seguro de que deseas eliminar este usuario?')">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($columns) + 1 }}" class="text-center py-4">No hay usuarios registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="px-5 py-5 bg-white border-t flex flex-col xs:flex-row items-center xs:justify-between">
                    <span class="text-xs xs:text-sm text-gray-900">
                        Mostrando {{ $users->firstItem() }} a {{ $users->lastItem() }} de {{ $users->total() }} resultados
                    </span>
                    <div class="inline-flex mt-2 xs:mt-0">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>