<x-app-layout>
    <div class="py-8 bg-gradient-to-br from-blue-50 via-white to-blue-100 min-h-screen">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-8">
                <h1 class="text-2xl font-semibold text-gray-800 mb-6">Editar Vulnerabilidad</h1>
                <form action="{{ route('vulnerabilities.update', $vulnerability) }}" method="POST" enctype="multipart/form-data"
                    class="space-y-2 grid grid-cols-2 md:grid-cols-3 gap-x-10 gap-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label for="title" class="block font-medium text-gray-700 mb-1 text-sm">Título o nombre</label>
                        <input type="text" name="title" id="title" class="w-full border border-gray-300 rounded px-2 py-1 focus:ring focus:ring-blue-200" value="{{ old('title', $vulnerability->title) }}" required>
                        @error('title') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="detection_date" class="block font-medium text-gray-700 mb-1 text-sm">Fecha de detección</label>
                        <input type="date" name="detection_date" id="detection_date" class="w-full border border-gray-300 rounded px-2 py-1 focus:ring focus:ring-blue-200" value="{{ old('detection_date', $vulnerability->detection_date ? \Illuminate\Support\Carbon::parse($vulnerability->detection_date)->format('Y-m-d') : '') }}">
                        @error('detection_date') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="project_id" class="block font-medium text-gray-700 mb-1 text-sm">Proyecto asociado</label>
                        <select name="project_id" id="project_id" class="w-full border border-gray-300 rounded px-2 py-1 focus:ring focus:ring-blue-200" required>
                            <option value="">Seleccione un proyecto</option>
                            @foreach ($projects as $project)
                                <option value="{{ $project->id }}" {{ old('project_id', $vulnerability->project_id) == $project->id ? 'selected' : '' }}>{{ $project->name }}</option>
                            @endforeach
                        </select>
                        @error('project_id') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-span-3">
                        <label for="description" class="block font-medium text-gray-700 mb-1 text-sm">Descripción detallada</label>
                        <textarea name="description" id="description" class="w-full border border-gray-300 rounded px-2 py-1 focus:ring focus:ring-blue-200" rows="3">{{ old('description', $vulnerability->description) }}</textarea>
                        @error('description') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="component" class="block font-medium text-gray-700 mb-1 text-sm">Componente o módulo afectado</label>
                        <input type="text" name="component" id="component" class="w-full border border-gray-300 rounded px-2 py-1 focus:ring focus:ring-blue-200" value="{{ old('component', $vulnerability->component) }}">
                        @error('component') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="type_id" class="block font-medium text-gray-700 mb-1 text-sm">Tipo de vulnerabilidad</label>
                        <select name="type_id" id="type_id" class="w-full border border-gray-300 rounded px-2 py-1 focus:ring focus:ring-blue-200" required>
                            <option value="">Seleccione un tipo...</option>
                            @foreach($types as $type)
                                <option value="{{ $type->id }}" {{ old('type_id', $vulnerability->type_id) == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('type_id') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="category_id" class="block font-medium text-gray-700 mb-1 text-sm">Categoría de Vulnerabilidad</label>
                        <select name="category_id" id="category_id" class="w-full border border-gray-300 rounded px-2 py-1 focus:ring focus:ring-blue-200" required>
                            <option value="">Seleccione una categoría...</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $vulnerability->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="owasp_classification" class="block font-medium text-gray-700 mb-1 text-sm">Clasificación OWASP</label>
                        <input type="text" name="owasp_classification" id="owasp_classification" class="w-full border border-gray-300 rounded px-2 py-1 focus:ring focus:ring-blue-200" value="{{ old('owasp_classification', $vulnerability->owasp_classification) }}">
                        @error('owasp_classification') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="cvss_vector" class="block font-medium text-gray-700 mb-1 text-sm">Vector CVSS</label>
                        <input type="text" name="cvss_vector" id="cvss_vector" class="w-full border border-gray-300 rounded px-2 py-1 focus:ring focus:ring-blue-200" value="{{ old('cvss_vector', $vulnerability->cvss_vector) }}">
                        @error('cvss_vector') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="cvss_score" class="block font-medium text-gray-700 mb-1 text-sm">Puntaje CVSS</label>
                        <input type="number" step="0.1" name="cvss_score" id="cvss_score" class="w-full border border-gray-300 rounded px-2 py-1 focus:ring focus:ring-blue-200" value="{{ old('cvss_score', $vulnerability->cvss_score) }}">
                        @error('cvss_score') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="severity" class="block font-medium text-gray-700 mb-1 text-sm">Nivel de severidad</label>
                        <select name="severity" id="severity" class="w-full border border-gray-300 rounded px-2 py-1 focus:ring focus:ring-blue-200">
                            <option value="">Seleccione</option>
                            <option value="Baja" {{ old('severity', $vulnerability->severity_level) == 'Baja' ? 'selected' : '' }}>Baja</option>
                            <option value="Media" {{ old('severity', $vulnerability->severity_level) == 'Media' ? 'selected' : '' }}>Media</option>
                            <option value="Alta" {{ old('severity', $vulnerability->severity_level) == 'Alta' ? 'selected' : '' }}>Alta</option>
                            <option value="Crítica" {{ old('severity', $vulnerability->severity_level) == 'Crítica' ? 'selected' : '' }}>Crítica</option>
                        </select>
                        @error('severity') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="likelihood" class="block font-medium text-gray-700 mb-1 text-sm">Probabilidad de explotación</label>
                        <input type="number" step="0.01" name="likelihood" id="likelihood" class="w-full border border-gray-300 rounded px-2 py-1 focus:ring focus:ring-blue-200" value="{{ old('likelihood', $vulnerability->exploit_probability) }}">
                        @error('likelihood') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="impact" class="block font-medium text-gray-700 mb-1 text-sm">Impacto estimado</label>
                        <select name="impact" id="impact" class="w-full border border-gray-300 rounded px-2 py-1 focus:ring focus:ring-blue-200" required>
                            <option value="">Seleccione</option>
                            <option value="Alto" {{ old('impact', $vulnerability->estimated_impact) == 'Alto' ? 'selected' : '' }}>Alto</option>
                            <option value="Bajo" {{ old('impact', $vulnerability->estimated_impact) == 'Bajo' ? 'selected' : '' }}>Bajo</option>
                        </select>
                        @error('impact') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="status" class="block font-medium text-gray-700 mb-1 text-sm">Estado actual</label>
                        <select name="status" id="status" class="w-full border border-gray-300 rounded px-2 py-1 focus:ring focus:ring-blue-200">
                            <option value="">Seleccione</option>
                            <option value="Detectada" {{ old('status', $vulnerability->state) == 'Detectada' ? 'selected' : '' }}>Detectada</option>
                            <option value="En análisis" {{ old('status', $vulnerability->state) == 'En análisis' ? 'selected' : '' }}>En análisis</option>
                            <option value="En tratamiento" {{ old('status', $vulnerability->state) == 'En tratamiento' ? 'selected' : '' }}>En tratamiento</option>
                            <option value="Resuelta" {{ old('status', $vulnerability->state) == 'Resuelta' ? 'selected' : '' }}>Resuelta</option>
                            <option value="Cerrada" {{ old('status', $vulnerability->state) == 'Cerrada' ? 'selected' : '' }}>Cerrada</option>
                        </select>
                        @error('status') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="assigned_user_id" class="block font-medium text-gray-700 mb-1 text-sm">Usuario asignado/responsable</label>
                        <select name="assigned_user_id" id="assigned_user_id" class="w-full border border-gray-300 rounded px-2 py-1 focus:ring focus:ring-blue-200">
                            <option value="">Seleccione un usuario</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" {{ old('assigned_user_id', $vulnerability->assigned_user_id) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                        @error('assigned_user_id') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="due_date" class="block font-medium text-gray-700 mb-1 text-sm">Fecha límite o estimada de resolución</label>
                        <input type="date" name="due_date" id="due_date" class="w-full border border-gray-300 rounded px-2 py-1 focus:ring focus:ring-blue-200" value="{{ old('due_date', $vulnerability->resolution_deadline ? \Illuminate\Support\Carbon::parse($vulnerability->resolution_deadline)->format('Y-m-d') : '') }}">
                        @error('due_date') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="priority" class="block font-medium text-gray-700 mb-1 text-sm">Prioridad</label>
                        <select name="priority" id="priority" class="w-full border border-gray-300 rounded px-2 py-1 focus:ring focus:ring-blue-200">
                            <option value="">Seleccione</option>
                            <option value="Baja" {{ old('priority', $vulnerability->priority) == 'Baja' ? 'selected' : '' }}>Baja</option>
                            <option value="Media" {{ old('priority', $vulnerability->priority) == 'Media' ? 'selected' : '' }}>Media</option>
                            <option value="Alta" {{ old('priority', $vulnerability->priority) == 'Alta' ? 'selected' : '' }}>Alta</option>
                            <option value="Crítica" {{ old('priority', $vulnerability->priority) == 'Crítica' ? 'selected' : '' }}>Crítica</option>
                        </select>
                        @error('priority') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="source" class="block font-medium text-gray-700 mb-1 text-sm">Fuente de detección</label>
                        <input type="text" name="source" id="source" class="w-full border border-gray-300 rounded px-2 py-1 focus:ring focus:ring-blue-200" value="{{ old('source', $vulnerability->detection_source) }}">
                        @error('source') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-span-2">
                        <label for="documentation_url" class="block font-medium text-gray-700 mb-1 text-sm">Enlace a documentación relacionada (URL carpeta compartida)</label>
                        <input type="url" name="documentation_url" id="documentation_url" class="w-full border border-gray-300 rounded px-2 py-1 focus:ring focus:ring-blue-200" value="{{ old('documentation_url', $vulnerability->documentation_url) }}">
                        @error('documentation_url') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-span-3">
                        <label for="attachments" class="block font-medium text-gray-700 mb-1 text-sm">Archivos adjuntos (opcional)</label>
                        <input type="file" name="attachments[]" id="attachments" class="w-full border border-gray-300 rounded px-2 py-1 focus:ring focus:ring-blue-200" multiple>
                        @error('attachments') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        @if(isset($vulnerability->attachments) && is_array($vulnerability->attachments) && count($vulnerability->attachments))
                            <div class="mt-2 text-sm text-gray-600">
                                <span class="font-semibold">Archivos actuales:</span>
                                <ul class="list-disc ml-5">
                                    @foreach($vulnerability->attachments as $file)
                                        <li>
                                            <a href="{{ asset('storage/' . $file) }}" target="_blank" class="text-blue-600 underline">{{ basename($file) }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                    <div class="col-span-3">
                        <label for="observations" class="block font-medium text-gray-700 mb-1 text-sm">Observaciones y consideraciones</label>
                        <textarea name="observations" id="observations" class="w-full border border-gray-300 rounded px-2 py-1 focus:ring focus:ring-blue-200" rows="2">{{ old('observations', $vulnerability->observations) }}</textarea>
                        @error('observations') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex col-span-3 justify-end">
                        <a href="{{ route('vulnerabilities.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-2 rounded shadow transition">← Volver al listado</a>
                        <button type="submit" class="bg-blue-700 hover:bg-blue-800 text-white px-6 py-2 rounded shadow transition ml-2">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>