<x-app-layout>
    <div class="py-8 bg-gradient-to-br from-blue-50 via-white to-blue-100 min-h-screen">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-8">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-800">{{ $viewModel->title }}</h1>
                        @if($viewModel->subtitle)
                            <p class="text-gray-600 mt-1">{{ $viewModel->subtitle }}</p>
                        @endif
                    </div>

                    @if(isset($show_create_task_button) && $show_create_task_button)
                        @can('create', \App\Domain\Tasks\Models\Task::class) {{-- General permission check --}}
                            <a href="{{ route('tasks.create') }}" {{-- Use generic route, specific context chosen in create form --}}
                               class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 transition inline-flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Crear Tarea
                            </a>
                        @endcan
                    @endif
                </div>

                <div class="overflow-x-auto min-h-[400px]">
                    <!--
                        Table columns responsive design:
                        - 'Título', 'Estado', 'Prioridad', 'Acciones' are always visible.
                        - 'Vulnerabilidad' is hidden on screens smaller than 'lg'.
                        - 'Proyecto' is hidden on screens smaller than 'md'.
                        - 'Asignado A' is hidden on screens smaller than 'md'.
                        - 'Fecha Límite' is hidden on screens smaller than 'sm'.
                    -->
                    <table class="w-full bg-white shadow rounded">
                        <thead>
                            <tr class="bg-gray-100 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">
                                <th class="px-4 py-3">Título</th>
                                <th class="px-4 py-3 hidden lg:table-cell">Vulnerabilidad</th>
                                <th class="px-4 py-3 hidden md:table-cell">Proyecto</th>
                                <th class="px-4 py-3 hidden md:table-cell">Asignado A</th>
                                <th class="px-4 py-3">Estado</th>
                                <th class="px-4 py-3">Prioridad</th>
                                <th class="px-4 py-3 hidden sm:table-cell">Fecha Límite</th>
                                <th class="px-4 py-3">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700">
                            @forelse ($viewModel->tasks as $task)
                                <tr class="border-b hover:bg-gray-50 transition">
                                    <td class="px-4 py-3">{{ $task->title }}</td>
                                    <td class="px-4 py-3 hidden lg:table-cell">
                                        @if($task->vulnerability)
                                            <a href="{{ route('vulnerabilities.show', $task->vulnerability_id) }}" class="text-blue-600 hover:underline">
                                                {{ Str::limit($task->vulnerability->title, 30) }}
                                            </a>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 hidden md:table-cell">
                                        @if($task->project)
                                            <a href="{{ route('projects.show', $task->project_id) }}" class="text-blue-600 hover:underline">
                                                {{ Str::limit($task->project->name, 30) }}
                                            </a>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 hidden md:table-cell">
                                        @if($task->assignee)
                                            <div class="flex items-center">
                                                <img class="h-6 w-6 rounded-full object-cover mr-2"
                                                     src="{{ $task->assignee->profile_photo_url }}" 
                                                     alt="{{ $task->assignee->name }}">
                                                <span class="text-sm">{{ $task->assignee->name }}</span>
                                            </div>
                                        @else
                                            <span class="text-gray-400">Sin asignar</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @switch(strtolower($task->status))
                                                @case('completada') bg-green-100 text-green-800 @break
                                                @case('en progreso') bg-blue-100 text-blue-800 @break
                                                @case('pendiente') bg-yellow-100 text-yellow-800 @break
                                                @default bg-gray-100 text-gray-800
                                            @endswitch">
                                            {{ ucfirst($task->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @switch(strtolower($task->priority))
                                                @case('crítica') bg-red-100 text-red-800 @break
                                                @case('alta') bg-orange-100 text-orange-800 @break
                                                @case('media') bg-yellow-100 text-yellow-800 @break
                                                @case('baja') bg-green-100 text-green-800 @break
                                                @default bg-gray-100 text-gray-800
                                            @endswitch">
                                            {{ ucfirst($task->priority) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 hidden sm:table-cell">
                                        @if($task->due_date)
                                            <span class="text-sm {{ $task->due_date->isPast() && $task->status !== 'Completada' ? 'text-red-600 font-semibold' : 'text-gray-600' }}">
                                                {{ $task->due_date->format('d/m/Y') }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        <div class="flex items-center space-x-3">
                                            <a href="{{ route('tasks.show', $task) }}" class="text-blue-600 hover:text-blue-900">Ver</a>
                                            @can('update', $task)
                                                <a href="{{ route('tasks.edit', $task) }}" class="text-yellow-600 hover:text-yellow-900">Editar</a>
                                            @endcan
                                            @can('delete', $task)
                                                <form action="{{ route('tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que quieres eliminar esta tarea?');" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900">Eliminar</button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-6 text-center text-gray-500">
                                        No hay tareas disponibles.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $viewModel->tasks->links() }}
                    </div>
                </div>

                @if($viewModel->backRoute)
                    <div class="mt-6">
                        <a href="{{ $viewModel->backRoute }}" 
                           class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-2 rounded shadow transition">
                            ← Volver
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>