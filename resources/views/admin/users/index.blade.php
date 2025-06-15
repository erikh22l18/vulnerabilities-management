<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestión de Usuarios') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-2xl font-semibold text-gray-800">
                            Lista de Usuarios del Sistema
                        </h1>
                        @can('create', App\Models\User::class) {{-- Assuming UserPolicy for create --}}
                            <a href="{{ route('admin.users.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold p-2 md:px-4 md:py-2 rounded shadow transition inline-flex items-center" aria-label="Crear Nuevo Usuario">
                                <svg class="w-5 h-5 md:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                <span class="hidden md:inline">Crear Nuevo Usuario</span>
                            </a>
                        @endcan
                    </div>

                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <!--
                            Table columns responsive design:
                            - 'Name' and 'Acciones' are always visible.
                            - 'Organización' or 'Organization Name' is hidden on screens smaller than 'lg'.
                            - 'Rol' or 'Role' is hidden on screens smaller than 'md'.
                            - 'Email' is hidden on screens smaller than 'sm'.
                            - Padding for all th/td changed from px-5 py-3 to px-3 py-3 sm:px-5 sm:py-3.
                        -->
                        <div class="inline-block min-w-full shadow rounded-lg overflow-hidden">
                            <table class="min-w-full leading-normal">
                                <thead>
                                    <tr>
                                        @foreach ($columns as $column => $displayName)
                                            <th class="px-3 py-3 sm:px-5 sm:py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider
                                                {{ $displayName === 'Organización' || $displayName === 'Organization Name' ? 'hidden lg:table-cell' : '' }}
                                                {{ $displayName === 'Rol' || $displayName === 'Role' ? 'hidden md:table-cell' : '' }}
                                                {{ $displayName === 'Email' ? 'hidden sm:table-cell' : '' }}
                                            ">
                                                {{ $displayName }}
                                            </th>
                                        @endforeach
                                        <th class="px-3 py-3 sm:px-5 sm:py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider"><span class="sr-only">Acciones</span></th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-700">
                                    @forelse ($users as $user)
                                        <tr>
                                            @foreach ($columns as $column => $displayName)
                                                <td class="px-3 py-3 sm:px-5 sm:py-3 border-b border-gray-200 bg-white text-sm
                                                    {{ $displayName === 'Organización' || $displayName === 'Organization Name' ? 'hidden lg:table-cell' : '' }}
                                                    {{ $displayName === 'Rol' || $displayName === 'Role' ? 'hidden md:table-cell' : '' }}
                                                    {{ $displayName === 'Email' ? 'hidden sm:table-cell' : '' }}
                                                ">
                                                    <p class="text-gray-900 whitespace-no-wrap">
                                                        @if ($column === 'organization.name')
                                                            {{ $user->organization->name ?? 'N/A' }}
                                                        @elseif ($column === 'roles.0.name')
                                                            {{ $user->roles->pluck('name')->join(', ') }}
                                                        @else
                                                            {{ data_get($user, $column, 'N/A') }} {{-- Use data_get for potentially nested direct properties --}}
                                                        @endif
                                                    </p>
                                                </td>
                                            @endforeach
                                            <td class="px-3 py-3 sm:px-5 sm:py-3 border-b border-gray-200 bg-white text-sm">
                                                @can('view', $user)
                                                    <a href="{{ route('admin.users.show', $user->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Ver</a>
                                                @endcan
                                                @can('update', $user)
                                                    <a href="{{ route('admin.users.edit', $user->id) }}" class="text-yellow-600 hover:text-yellow-900 mr-3">Editar</a>
                                                @endcan
                                                @can('delete', $user)
                                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline-block">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('¿Estás seguro de que deseas eliminar este usuario?')">Eliminar</button>
                                                    </form>
                                                @endcan
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ count($columns) + 1 }}" class="text-center py-4 text-gray-500">No hay usuarios registrados.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>

                            @if ($users->hasPages())
                            <div class="px-5 py-5 bg-white border-t flex flex-col xs:flex-row items-center xs:justify-between">
                                
                                <div class="inline-flex mt-2 xs:mt-0">
                                    {{ $users->links() }}
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>