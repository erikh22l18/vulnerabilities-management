<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detalles del Proyecto') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-2xl font-semibold text-gray-800">{{ $project->name }}</h1>
                        @can('update', $project)
                            <a href="{{ route('projects.edit', $project) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded shadow">
                                Editar Proyecto
                            </a>
                        @endcan
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Información General</h3>
                            <dl class="mt-2 space-y-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Identificador</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $project->identifier }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Organización</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $project->organization->name ?? 'N/A' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Estado</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ strtolower($project->status ?? 'default') === 'active' || strtolower($project->status ?? 'default') === 'activo' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ ucfirst($project->status ?? 'N/A') }}
                                        </span>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Creado</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $project->created_at->format('d/m/Y H:i') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Actualizado</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $project->updated_at->format('d/m/Y H:i') }}</dd>
                                </div>
                            </dl>
                        </div>
                        <div class="md:col-span-2">
                            <h3 class="text-lg font-medium text-gray-900">Objetivo General</h3>
                            <p class="mt-1 text-sm text-gray-900">{{ $project->general_objective ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="mt-8 border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Usuarios en el Proyecto</h3>
                        @if($project->users && $project->users->count() > 0)
                            <ul class="list-disc ml-5 space-y-1">
                                @foreach($project->users as $user)
                                    <li class="text-sm text-gray-700">{{ $user->name }} ({{ $user->email }}) - Rol: {{ $user->pivot->role ?? 'No especificado' }}</li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-sm text-gray-500">No hay usuarios asignados a este proyecto.</p>
                        @endif
                         <div class="mt-4">
                            <a href="{{ route('projects.users.index', $project) }}" class="text-sm text-blue-600 hover:underline">Gestionar Usuarios del Proyecto</a>
                        </div>
                    </div>

                    <div class="mt-8 border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Vulnerabilidades del Proyecto ({{ $project->vulnerabilities->count() }})</h3>
                        @if($project->vulnerabilities && $project->vulnerabilities->count() > 0)
                            <ul class="list-disc ml-5 space-y-1">
                                @foreach($project->vulnerabilities->take(10) as $vulnerability) {{-- Show first 10 for brevity --}}
                                    <li class="text-sm">
                                        <a href="{{ route('vulnerabilities.show', $vulnerability) }}" class="text-blue-600 hover:underline">{{ $vulnerability->title }}</a>
                                        <span class="text-xs px-1.5 py-0.5 rounded-full {{ strtolower($vulnerability->state) === 'resuelta' || strtolower($vulnerability->state) === 'cerrada' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">{{ $vulnerability->state }}</span>
                                    </li>
                                @endforeach
                                @if($project->vulnerabilities->count() > 10)
                                    <li class="text-sm text-gray-500">... y {{ $project->vulnerabilities->count() - 10 }} más.</li>
                                @endif
                            </ul>
                        @else
                            <p class="text-sm text-gray-500">No hay vulnerabilidades registradas para este proyecto.</p>
                        @endif
                        <div class="mt-4">
                             <a href="{{ route('projects.vulnerabilities.index', $project) }}" class="text-sm text-blue-600 hover:underline">Ver Todas las Vulnerabilidades del Proyecto</a>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t">
                        <a href="{{ route('projects.index') }}" class="text-indigo-600 hover:text-indigo-800 transition duration-150 ease-in-out">
                            &larr; Volver a la lista de proyectos
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
