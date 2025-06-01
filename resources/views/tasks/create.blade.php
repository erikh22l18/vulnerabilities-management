<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Crear Nueva Tarea') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 md:p-8">
                <x-validation-errors class="mb-4" />

                <form method="POST" action="{{ route('tasks.store') }}">
                    @csrf

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

                    <!-- Vulnerability -->
                    <div class="mb-4">
                        <x-label for="vulnerability_id" value="Vulnerabilidad Asociada" />
                        <select id="vulnerability_id" name="vulnerability_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="">Seleccione una vulnerabilidad</option>
                            @foreach ($vulnerabilities as $vulnerability)
                                <option value="{{ $vulnerability->id }}" data-project-id="{{ $vulnerability->project_id }}" {{ old('vulnerability_id') == $vulnerability->id ? 'selected' : '' }}>
                                    {{ Str::limit($vulnerability->title, 70) }} (Proyecto: {{ $vulnerability->project->name ?? 'N/A' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Project (auto-filled by JS, or manual if no JS / no vulnerability selected) -->
                    <div class="mb-4">
                        <x-label for="project_id" value="Proyecto Asociado" />
                        <select id="project_id" name="project_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="">Seleccione un proyecto</option>
                            @foreach ($projects as $project)
                                <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                    {{ $project->name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-sm text-gray-500 mt-1">El proyecto se seleccionará automáticamente al elegir una vulnerabilidad. Puede cambiarlo manually si es necesario.</p>
                    </div>


                    <!-- Assigned To -->
                    <div class="mb-4">
                        <x-label for="assigned_to" value="Asignar A (Usuario)" />
                        <select id="assigned_to" name="assigned_to" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">Sin asignar</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Due Date -->
                    <div class="mb-4">
                        <x-label for="due_date" value="Fecha Límite" />
                        <x-input id="due_date" class="block mt-1 w-full" type="date" name="due_date" :value="old('due_date')" />
                    </div>

                    <!-- Priority -->
                    <div class="mb-4">
                        <x-label for="priority" value="Prioridad" />
                        <select id="priority" name="priority" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="Baja" {{ old('priority') == 'Baja' ? 'selected' : '' }}>Baja</option>
                            <option value="Media" {{ old('priority', 'Media') == 'Media' ? 'selected' : '' }}>Media</option>
                            <option value="Alta" {{ old('priority') == 'Alta' ? 'selected' : '' }}>Alta</option>
                            <option value="Crítica" {{ old('priority') == 'Crítica' ? 'selected' : '' }}>Crítica</option>
                        </select>
                    </div>

                    <!-- Status -->
                    <div class="mb-4">
                        <x-label for="status" value="Estado" />
                        <select id="status" name="status" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="pendiente" {{ old('status', 'pendiente') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                            <option value="en_progreso" {{ old('status') == 'en_progreso' ? 'selected' : '' }}>En Progreso</option>
                            <option value="completada" {{ old('status') == 'completada' ? 'selected' : '' }}>Completada</option>
                            {{-- 'cancelada' is a valid state in DB, but usually not set at creation from this form --}}
                        </select>
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        <a href="{{ url()->previous(route('tasks.index')) }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">
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
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const vulnerabilitySelect = document.getElementById('vulnerability_id');
            const projectSelect = document.getElementById('project_id');

            if (vulnerabilitySelect && projectSelect) {
                vulnerabilitySelect.addEventListener('change', function () {
                    const selectedOption = this.options[this.selectedIndex];
                    const projectId = selectedOption.dataset.projectId;
                    if (projectId) {
                        projectSelect.value = projectId;
                    } else if (this.value === "") {
                        // If "Seleccione una vulnerabilidad" is chosen, reset project selection
                        projectSelect.value = "";
                    }
                    // If no specific project ID on the option, do not change project automatically
                    // This allows manual project selection if a vulnerability isn't directly tied to one in the <select>
                });
                // Trigger change on load if a vulnerability is already selected (e.g. due to validation error retaining old input)
                if (vulnerabilitySelect.value) {
                    vulnerabilitySelect.dispatchEvent(new Event('change'));
                }
            }
        });
    </script>
    @endpush
</x-app-layout>
