<x-app-layout>
    <div class="py-8 bg-gradient-to-br from-blue-50 via-white to-blue-100 min-h-screen">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-8">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-800">{{ $viewModel->title }}</h1>
                        @if($viewModel->subtitle)
                        <p class="text-gray-600 mt-1">{{ $viewModel->subtitle }}</p>
                        @endif
                    </div>
                    <div class="flex gap-2">
                        @can('create', App\Domain\Vulnerabilities\Models\Vulnerability::class)
                        <a href="{{ $viewModel->createRoute }}" class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 transition inline-flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                            Nueva Vulnerabilidad
                        </a>
                        @endcan
                        @if($viewModel->can_import)
                        <a href="{{ route('vulnerabilities.charge') }}" class="bg-green-600 text-white px-4 py-2 rounded shadow hover:bg-green-700 transition inline-flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3" />
                            </svg>
                            Importar Excel
                        </a>
                        @endif
                    </div>
                </div>

                <div class="overflow-x-auto min-h-[400px]">
                    <table class="w-full bg-white shadow rounded">
                        <thead>
                            <tr class="bg-blue-100 text-left">
                                <th class="px-4 py-2">Título</th>
                                @if(!$viewModel->context || $viewModel->context !== 'project')
                                <th class="px-4 py-2">Proyecto</th>
                                @endif
                                <th class="px-4 py-2">Estado</th>
                                <th class="px-4 py-2">Prioridad</th>
                                @if(!$viewModel->context || $viewModel->context !== 'user')
                                <th class="px-4 py-2">Usuarios</th>
                                @endif
                                <th class="px-4 py-2">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($viewModel->vulnerabilities as $vulnerability)
                            <tr class="border-b hover:bg-blue-50 transition">
                                <td class="px-4 py-2">{{ $vulnerability->title }}</td>
                                @if(!$viewModel->context || $viewModel->context !== 'project')
                                <td class="px-4 py-2">{{ $vulnerability->project->name }}</td>
                                @endif
                                <td class="px-4 py-2">
                                    <x-vulnerability-status :status="$vulnerability->state" />
                                </td>
                                <td class="px-4 py-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @switch($vulnerability->priority)
                                                @case('Alta') bg-red-100 text-red-800 @break
                                                @case('Media') bg-yellow-100 text-yellow-800 @break
                                                @default bg-blue-100 text-blue-800
                                            @endswitch">
                                        {{ $vulnerability->priority }}
                                    </span>
                                </td>
                                @if(!$viewModel->context || $viewModel->context !== 'user')
                                <td class="px-4 py-2">
                                    <x-user-avatars :users="$vulnerability->assignedUsers" />
                                </td>
                                @endif
                                <td class="px-4 py-2">
                                    <div class="relative" x-data="{ open: false }">
                                        <button @click="open = !open" class="inline-flex items-center text-gray-700 hover:text-gray-900">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                                            </svg>
                                        </button>
                                        <div x-show="open"
                                            @click.away="open = false"
                                            class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50"
                                            x-transition:enter="transition ease-out duration-100"
                                            x-transition:enter-start="transform opacity-0 scale-95"
                                            x-transition:enter-end="transform opacity-100 scale-100"
                                            x-transition:leave="transition ease-in duration-75"
                                            x-transition:leave-start="transform opacity-100 scale-100"
                                            x-transition:leave-end="transform opacity-0 scale-95">

                                            <a href="{{ route('vulnerabilities.show', $vulnerability) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <div class="flex items-center">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    Ver Detalle
                                                </div>
                                            </a>

                                            <a href="{{ route('vulnerabilities.edit', $vulnerability) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <div class="flex items-center">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536M9 13l6.586-6.586a2 2 0 112.828 2.828L11.828 15.828a2 2 0 01-2.828 0L9 13zm-6 6h6v-2a2 2 0 012-2h2a2 2 0 012 2v2h6"></path>
                                                    </svg>
                                                    Editar
                                                </div>
                                            </a>

                                            <a href="{{ route('vulnerabilities.users.index', $vulnerability) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <div class="flex items-center">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                                    </svg>
                                                    Usuarios Asignados
                                                </div>
                                            </a>

                                            <!-- tareas -->
                                            <a href="{{ route('vulnerabilities.tasks.index', $vulnerability) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <div class="flex items-center">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m-3-3H9m6-4a2 2 0 11-4 0 2 2 0 014 0zM5.121 5.121A2.5 2.5 0 007.5 3h9a2.5 2.5 0 012.379 1.621l1.5 3A2.5 2.5 0 0117.5 10H7a2.5 2.5 0 01-1.879-.879l-1.5-3z" />
                                                    </svg>
                                                    Tareas
                                                </div>
                                            </a>

                                            <form action="{{ route('vulnerabilities.destroy', $vulnerability) }}" method="POST" class="block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100"
                                                    onclick="return confirm('¿Seguro que deseas eliminar esta vulnerabilidad?')">
                                                    <div class="flex items-center">
                                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M8 7V5a2 2 0 012-2h4a2 2 0 012 2v2"></path>
                                                        </svg>
                                                        Eliminar
                                                    </div>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                                    No se encontraron vulnerabilidades.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $viewModel->vulnerabilities->links() }}
                    </div>
                </div>
                @if($viewModel->backRoute)
                <div class="flex justify-between mt-6">
                    <a href="{{ $viewModel->backRoute }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-2 rounded shadow transition">← Volver</a>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>