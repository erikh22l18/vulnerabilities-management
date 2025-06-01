<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Crear Nueva Tarea para Vulnerabilidad') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 md:p-8">
                <h3 class="text-lg font-medium text-gray-900 mb-2">
                    Vulnerabilidad: <span class="font-normal">{{ $viewModel->vulnerability->title }}</span>
                </h3>
                <p class="text-sm text-gray-600 mb-4">
                    Proyecto: {{ $viewModel->vulnerability->project->name ?? 'N/A' }}
                </p>

                <x-validation-errors class="mb-4" />

                <form method="POST" action="{{ route('vulnerabilities.tasks.store', $viewModel->vulnerability->id) }}">
                    @csrf
                    <input type="hidden" name="vulnerability_id" value="{{ $viewModel->vulnerability->id }}">
                    {{-- Project ID can be implicitly derived in the controller from the vulnerability --}}


                    <!-- Title -->
                    <div class="mb-4">
                        <x-label for="title" value="Título de la Tarea" />
                        <x-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title')" required autofocus />
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <x-label for="description" value="Descripción" />
                        <textarea id="description" name="description" rows="4" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description') }}</textarea>
                    </div>

                    <!-- Assigned To -->
                    <div class="mb-4">
                        <x-label for="assigned_to" value="Asignar A (Usuario del Proyecto)" />
                        <select id="assigned_to" name="assigned_to" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">Sin asignar</option>
                            @foreach ($viewModel->users as $user) {{-- Assuming $viewModel->users are users of the project --}}
                                <option value="{{ $user->id }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Priority -->
                    <div class="mb-4">
                        <x-label for="priority" value="Prioridad" />
                        <select id="priority" name="priority" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="baja" {{ old('priority', 'media') == 'baja' ? 'selected' : '' }}>Baja</option>
                            <option value="media" {{ old('priority', 'media') == 'media' ? 'selected' : '' }}>Media</option>
                            <option value="alta" {{ old('priority', 'media') == 'alta' ? 'selected' : '' }}>Alta</option>
                            <option value="critica" {{ old('priority', 'media') == 'critica' ? 'selected' : '' }}>Crítica</option>
                        </select>
                    </div>

                    <!-- Due Date -->
                    <div class="mb-4">
                        <x-label for="due_date" value="Fecha Límite" />
                        <x-input id="due_date" class="block mt-1 w-full" type="date" name="due_date" :value="old('due_date')" />
                    </div>

                    {{-- Status defaults to 'Pendiente' in controller, so not explicitly set here --}}

                    <div class="flex items-center justify-end mt-6">
                        <a href="{{ route('vulnerabilities.tasks.index', $viewModel->vulnerability->id) }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">
                            Cancelar
                        </a>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow-md focus:outline-none focus:shadow-outline">
                            Crear Tarea
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
