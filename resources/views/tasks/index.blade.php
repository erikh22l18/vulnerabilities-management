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

                    @if($viewModel->can_create)
                        <a href="{{ $viewModel->createRoute }}" 
                           class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 transition inline-flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Nueva Tarea
                        </a>
                    @endif
                </div>

                <div class="overflow-x-auto min-h-[400px]">
                    <table class="w-full bg-white shadow rounded">
                        <thead>
                            <tr class="bg-blue-100 text-left">
                                <th class="px-4 py-2">Título</th>
                                @if(!$viewModel->context || $viewModel->context !== 'project')
                                    <th class="px-4 py-2">Proyecto</th>
                                @endif
                                @if(!$viewModel->context || $viewModel->context !== 'vulnerability')
                                    <th class="px-4 py-2">Vulnerabilidad</th>
                                @endif
                                <th class="px-4 py-2">Estado</th>
                                <th class="px-4 py-2">Prioridad</th>
                                <th class="px-4 py-2">Asignado a</th>
                                <th class="px-4 py-2">Fecha límite</th>
                                <th class="px-4 py-2">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($viewModel->tasks as $task)
                                <tr class="border-b hover:bg-blue-50 transition">
                                    <td class="px-4 py-2">{{ $task->title }}</td>
                                    @if(!$viewModel->context || $viewModel->context !== 'project')
                                        <td class="px-4 py-2">{{ $task->project->name }}</td>
                                    @endif
                                    @if(!$viewModel->context || $viewModel->context !== 'vulnerability')
                                        <td class="px-4 py-2">{{ $task->vulnerability->title }}</td>
                                    @endif
                                    <td class="px-4 py-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @switch($task->status)
                                                @case('completada')
                                                    bg-green-100 text-green-800
                                                    @break
                                                @case('en_progreso')
                                                    bg-blue-100 text-blue-800
                                                    @break
                                                @case('cancelada')
                                                    bg-red-100 text-red-800
                                                    @break
                                                @default
                                                    bg-yellow-100 text-yellow-800
                                            @endswitch">
                                            {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @switch($task->priority)
                                                @case('alta')
                                                    bg-red-100 text-red-800
                                                    @break
                                                @case('media')
                                                    bg-yellow-100 text-yellow-800
                                                    @break
                                                @default
                                                    bg-green-100 text-green-800
                                            @endswitch">
                                            {{ ucfirst($task->priority) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2">
                                        @if($task->assignee)
                                            <div class="flex items-center">
                                                <img class="h-6 w-6 rounded-full mr-2" 
                                                     src="{{ $task->assignee->profile_photo_url }}" 
                                                     alt="{{ $task->assignee->name }}">
                                                <span class="text-sm text-gray-600">{{ $task->assignee->name }}</span>
                                            </div>
                                        @else
                                            <span class="text-gray-400">Sin asignar</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2">
                                        @if($task->due_date)
                                            <span class="text-sm {{ $task->due_date->isPast() ? 'text-red-600' : 'text-gray-600' }}">
                                                {{ $task->due_date->format('d/m/Y') }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('tasks.show', [ $task]) }}" 
                                               class="text-blue-600 hover:text-blue-900">Ver</a>
                                            @if(Auth::user()->can('editar tareas'))
                                                <a href="{{ route('tasks.edit', $task) }}" 
                                                   class="text-gray-600 hover:text-gray-900">Editar</a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-6 text-center text-gray-500">
                                        No hay tareas registradas.
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