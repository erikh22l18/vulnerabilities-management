<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detalles del Usuario: ') . $user->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                    <div class="mb-6">
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2">
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">ID de Usuario</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $user->id }}</dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Nombre</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $user->name }}</dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Correo Electrónico</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $user->email }}</dd>
                            </div>
                             <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Identificación</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $user->identification ?? 'N/A' }}</dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Área</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $user->area ?? 'N/A' }}</dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Organización</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $user->organization->name ?? 'N/A' }}</dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Rol(es)</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $user->roles->isNotEmpty() ? $user->roles->pluck('name')->join(', ') : 'N/A' }}</dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Fecha de Creación</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $user->created_at ? $user->created_at->format('d/m/Y H:i:s') : 'N/A' }}</dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Última Actualización</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $user->updated_at ? $user->updated_at->format('d/m/Y H:i:s') : 'N/A' }}</dd>
                            </div>
                            @if ($user->email_verified_at)
                                <div class="sm:col-span-1">
                                    <dt class="text-sm font-medium text-gray-500">Email Verificado</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $user->email_verified_at->format('d/m/Y H:i:s') }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>

                    <div class="flex items-center justify-start mt-8 pt-5 border-t border-gray-200">
                        <a href="{{ route('admin.users.index') }}" class="text-indigo-600 hover:text-indigo-900 transition duration-150 ease-in-out">
                            &larr; Volver al listado de usuarios
                        </a>
                        @can('update', $user)
                            <a href="{{ route('admin.users.edit', $user) }}" class="ml-4 bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded shadow focus:outline-none focus:shadow-outline">
                                Editar Usuario
                            </a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
