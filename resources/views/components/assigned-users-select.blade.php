@php
    // Agrupa usuarios por proyecto para facilitar el acceso
    $usersByProject = $projects->mapWithKeys(function($project) {
        return [$project->id => $project->users];
    });
@endphp

<div class="md:grid md:grid-cols-3 col-span-3 md:gap-10">
    <div>
        <label for="project_id" class="block font-medium text-gray-700 mb-1">Proyecto</label>
        <select name="project_id" id="project_id" class="w-full border border-gray-300 rounded px-2 py-1 focus:ring focus:ring-blue-200" required>
            <option value="">Seleccione un proyecto...</option>
            @foreach($projects as $project)
                <option value="{{ $project->id }}" {{ old('project_id', $selectedProject) == $project->id ? 'selected' : '' }}>
                    {{ $project->name }}
                </option>
            @endforeach
        </select>
        @error('project_id') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
    </div>

    <div>
        <label for="responsible_id" class="block font-medium text-gray-700 mb-1">Responsable</label>
        <select name="responsible_id" id="responsible_id" class="w-full border border-gray-300 rounded px-2 py-1 focus:ring focus:ring-blue-200" required>
            <option value="">Seleccione un responsable...</option>
            @foreach($projects as $project)
                @foreach($project->users as $user)
                    <option 
                        value="{{ $user->id }}" 
                        data-project="{{ $project->id }}"
                        {{ old('responsible_id', $responsibleId) == $user->id ? 'selected' : '' }}
                        class="project-user project-{{ $project->id }}" 
                        style="{{ old('project_id', $selectedProject) != $project->id ? 'display:none' : '' }}">
                        {{ $user->name }}
                    </option>
                @endforeach
            @endforeach
        </select>
        @error('responsible_id') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
    </div>

    <div>
    <label for="assigned_users" class="block font-medium text-gray-700 mb-1">Usuarios asignados</label>
    <input type="text" id="assigned_users_search" placeholder="Buscar usuarios..." class="w-full border border-gray-300 rounded px-2 py-1 mb-1 focus:ring focus:ring-blue-200">
    <select name="assigned_users[]" id="assigned_users" multiple class="w-full border border-gray-300 rounded px-2 py-1 focus:ring focus:ring-blue-200">
        @foreach($projects as $project)
            @foreach($project->users as $user)
                <option 
                    value="{{ $user->id }}" 
                    data-project="{{ $project->id }}"
                    {{ collect(old('assigned_users', $assignedUsers))->contains($user->id) ? 'selected' : '' }}
                    class="project-user project-{{ $project->id }}" 
                    style="{{ old('project_id', $selectedProject) != $project->id ? 'display:none' : '' }}">
                    {{ $user->name }}
                </option>
            @endforeach
        @endforeach
    </select>
    @error('assigned_users') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
</div>
</div>

<script>
    document.getElementById('project_id').addEventListener('change', function() {
        const projectId = this.value;
        
        // Ocultar todas las opciones de usuario
        document.querySelectorAll('.project-user').forEach(el => {
            el.style.display = 'none';
        });
        
        // Mostrar solo las opciones del proyecto seleccionado
        if (projectId) {
            document.querySelectorAll('.project-' + projectId).forEach(el => {
                el.style.display = '';
            });
        }
    });
</script>