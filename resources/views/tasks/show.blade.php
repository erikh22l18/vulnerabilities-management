<x-app-layout>
    <div class="py-8 bg-gradient-to-br from-blue-50 via-white to-blue-100 min-h-screen">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-8">
                <h1 class="text-2xl font-semibold text-gray-800 mb-4">{{ $task->title }}</h1>
                <div class="mb-4">
                    <span class="font-semibold">Proyecto:</span> {{ $task->project->name ?? '-' }}
                </div>
                <div class="mb-4">
                    <span class="font-semibold">Vulnerabilidad:</span> {{ $vulnerability->title }}
                </div>
                <div class="mb-4">
                    <span class="font-semibold">Descripción:</span>
                    <div class="text-gray-700">{{ $task->description ?? '-' }}</div>
                </div>
                <div class="mb-4">
                    <span class="font-semibold">Prioridad:</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        @switch($task->priority)
                            @case('alta') bg-red-100 text-red-800 @break
                            @case('media') bg-yellow-100 text-yellow-800 @break
                            @default bg-green-100 text-green-800
                        @endswitch">
                        {{ ucfirst($task->priority) }}
                    </span>
                </div>
                <div class="mb-4">
                    <span class="font-semibold">Estado:</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        @switch($task->status)
                            @case('completada') bg-green-100 text-green-800 @break
                            @case('en_progreso') bg-blue-100 text-blue-800 @break
                            @case('cancelada') bg-red-100 text-red-800 @break
                            @default bg-yellow-100 text-yellow-800
                        @endswitch">
                        {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                    </span>
                </div>
                <div class="mb-4">
                    <span class="font-semibold">Asignado a:</span>
                    @if($task->assignee)
                        <span class="ml-2">{{ $task->assignee->name }}</span>
                    @else
                        <span class="ml-2 text-gray-400">Sin asignar</span>
                    @endif
                </div>
                <div class="mb-4">
                    <span class="font-semibold">Fecha límite:</span>
                    <span class="ml-2">{{ $task->due_date ? $task->due_date->format('d/m/Y') : '-' }}</span>
                </div>
                <div class="mb-4">
                    <span class="font-semibold">Creado por:</span>
                    <span class="ml-2">{{ $task->creator->name ?? '-' }}</span>
                </div>
                <div class="flex justify-between mt-8">
                    <a href="{{ route('vulnerabilities.tasks.index', $vulnerability) }}"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-2 rounded shadow transition">
                        ← Volver al listado
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>