<x-app-layout>
    <div class="py-8 bg-gradient-to-br from-blue-50 via-white to-blue-100 min-h-screen">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-8">
                <h1 class="text-2xl font-semibold text-gray-800 mb-6">Nueva Vulnerabilidad</h1>
                <form action="{{ $viewModel->storeRoute }}" method="POST" enctype="multipart/form-data"
                    class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-x-10 gap-y-4">
                    @csrf
                    @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded mb-4">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <div>
                        <label for="title" class="block font-medium text-gray-700 mb-1 text-sm">Nombre</label>
                        <input type="text" name="title" id="title" class="w-full border border-gray-300 rounded px-2 py-1 focus:ring focus:ring-blue-200" value="{{ old('title') }}" required>
                        @error('title') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="detection_date" class="block font-medium text-gray-700 mb-1 text-sm">Fecha de detección</label>
                        <input type="date" name="detection_date" id="detection_date" class="w-full border border-gray-300 rounded px-2 py-1 focus:ring focus:ring-blue-200" value="{{ old('detection_date') }}">
                        @error('detection_date') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    {{-- El campo para vulnerability_type_id ha sido eliminado. El campo type_id más abajo se conservará y será el único para Tipo de Vulnerabilidad --}}
                    <div class="col-span-3">
                        <label for="description" class="block font-medium text-gray-700 mb-1 text-sm">Descripción detallada</label>
                        <textarea name="description" id="description" class="w-full border border-gray-300 rounded px-2 py-1 focus:ring focus:ring-blue-200" rows="3">{{ old('description') }}</textarea>
                        @error('description') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    @if($viewModel->users && $viewModel->users->count())
                    <!-- <div>
                        <label for="responsible_id" class="block font-medium text-gray-700 mb-1">Responsable</label>
                        <select name="responsible_id" id="responsible_id" class="w-full border border-gray-300 rounded px-2 py-1 focus:ring focus:ring-blue-200" required>
                            <option value="">Seleccione un responsable...</option>
                            @foreach($viewModel->users as $user)
                            <option value="{{ $user->id }}" {{ old('responsible_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('responsible_id') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <x-combobox
                        name="assigned_users"
                        :label="'Usuarios asignados'"
                        :options="$viewModel->users->map(fn($u) => ['id' => $u->id, 'name' => $u->name])->values()"
                        :selected="old('assigned_users', [])"
                        placeholder="Seleccione usuarios..." /> -->
                    <x-assigned-users-select
                        :projects="$viewModel->projects"
                        :selected-project="old('project_id', $viewModel->project?->id)"
                        :assigned-users="old('assigned_users', [])"
                        :responsible-id="old('responsible_id')"
                        class="col-span-1 sm:col-span-2 md:col-span-3" />
                    @endif
                    <div>
                        <label for="component" class="block font-medium text-gray-700 mb-1 text-sm">Componente o módulo afectado</label>
                        <input type="text" name="component" id="component" class="w-full border border-gray-300 rounded px-2 py-1 focus:ring focus:ring-blue-200" value="{{ old('component') }}">
                        @error('component') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    @if($viewModel->projects && $viewModel->projects->count())

                    @endif


                    <div>
                        <label for="owasp_classification" class="block font-medium text-gray-700 mb-1 text-sm">Clasificación OWASP</label>
                        <input type="text" name="owasp_classification" id="owasp_classification" class="w-full border border-gray-300 rounded px-2 py-1 focus:ring focus:ring-blue-200" value="{{ old('owasp_classification') }}">
                        @error('owasp_classification') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="cvss_vector" class="block font-medium text-gray-700 mb-1 text-sm">Vector CVSS</label>
                        <input type="text" name="cvss_vector" id="cvss_vector" class="w-full border border-gray-300 rounded px-2 py-1 focus:ring focus:ring-blue-200" value="{{ old('cvss_vector') }}">
                        @error('cvss_vector') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="cvss_score" class="block font-medium text-gray-700 mb-1 text-sm">Puntaje CVSS</label>
                        <input type="number" step="0.1" name="cvss_score" id="cvss_score" class="w-full border border-gray-300 rounded px-2 py-1 focus:ring focus:ring-blue-200" value="{{ old('cvss_score') }}">
                        @error('cvss_score') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="severity" class="block font-medium text-gray-700 mb-1 text-sm">Nivel de severidad</label>
                        <select name="severity" id="severity" class="w-full border border-gray-300 rounded px-2 py-1 focus:ring focus:ring-blue-200">
                            <option value="">Seleccione</option>
                            <option value="Baja" {{ old('severity') == 'Baja' ? 'selected' : '' }}>Baja</option>
                            <option value="Media" {{ old('severity') == 'Media' ? 'selected' : '' }}>Media</option>
                            <option value="Alta" {{ old('severity') == 'Alta' ? 'selected' : '' }}>Alta</option>
                            <option value="Crítica" {{ old('severity') == 'Crítica' ? 'selected' : '' }}>Crítica</option>
                        </select>
                        @error('severity') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="likelihood" class="block font-medium text-gray-700 mb-1 text-sm">Probabilidad de explotación</label>
                        <input type="number" step="0.01" name="likelihood" id="likelihood"
                            class="w-full border border-gray-300 rounded px-2 py-1 focus:ring focus:ring-blue-200"
                            value="{{ old('likelihood') }}">
                        @error('likelihood') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="impact" class="block font-medium text-gray-700 mb-1 text-sm">Impacto estimado</label>
                        <select name="impact" id="impact" class="w-full border border-gray-300 rounded px-2 py-1 focus:ring focus:ring-blue-200" required>
                            <option value="">Seleccione</option>
                            <option value="Alto" {{ old('impact') == 'Alto' ? 'selected' : '' }}>Alto</option>
                            <option value="Bajo" {{ old('impact') == 'Bajo' ? 'selected' : '' }}>Bajo</option>
                        </select>
                        @error('impact') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    @if($viewModel->categories && $viewModel->categories->count())
                    <div>
                        <label for="category_id" class="block font-medium text-gray-700 mb-1 text-sm">Categoría de Vulnerabilidad</label>
                        <select name="category_id" id="category_id" class="w-full border border-gray-300 rounded px-2 py-1 focus:ring focus:ring-blue-200" required>
                            <option value="">Seleccione una categoría...</option>
                            @foreach($viewModel->categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('category_id') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    @endif
                    @if($viewModel->types && $viewModel->types->count())
                    <div>
                        <label for="type_id" class="block font-medium text-gray-700 mb-1 text-sm">Tipo de vulnerabilidad</label>
                        <select name="type_id" id="type_id" class="w-full border border-gray-300 rounded px-2 py-1 focus:ring focus:ring-blue-200" required>
                            <option value="">Seleccione un tipo...</option>
                            @foreach($viewModel->types as $type)
                            <option value="{{ $type->id }}" {{ old('type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('type_id') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    @endif
                    <div>
                        <label for="due_date" class="block font-medium text-gray-700 mb-1 text-sm">Fecha límite o estimada de resolución</label>
                        <input type="date" name="due_date" id="due_date" class="w-full border border-gray-300 rounded px-2 py-1 focus:ring focus:ring-blue-200" value="{{ old('due_date') }}">
                        @error('due_date') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="priority" class="block font-medium text-gray-700 mb-1 text-sm">Prioridad</label>
                        <select name="priority" id="priority" class="w-full border border-gray-300 rounded px-2 py-1 focus:ring focus:ring-blue-200">
                            <option value="">Seleccione</option>
                            <option value="Baja" {{ old('priority') == 'Baja' ? 'selected' : '' }}>Baja</option>
                            <option value="Media" {{ old('priority') == 'Media' ? 'selected' : '' }}>Media</option>
                            <option value="Alta" {{ old('priority') == 'Alta' ? 'selected' : '' }}>Alta</option>
                            <option value="Crítica" {{ old('priority') == 'Crítica' ? 'selected' : '' }}>Crítica</option>
                        </select>
                        @error('priority') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="source" class="block font-medium text-gray-700 mb-1 text-sm">Fuente de detección</label>
                        <input type="text" name="source" id="source" class="w-full border border-gray-300 rounded px-2 py-1 focus:ring focus:ring-blue-200" value="{{ old('source') }}">
                        @error('source') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-span-2">
                        <label for="documentation_url" class="block font-medium text-gray-700 mb-1 text-sm">Enlace a documentación relacionada (URL carpeta compartida)</label>
                        <input type="url" name="documentation_url" id="documentation_url" class="w-full border border-gray-300 rounded px-2 py-1 focus:ring focus:ring-blue-200" value="{{ old('documentation_url') }}">
                        @error('documentation_url') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-span-3">
                        <label for="attachments" class="block font-medium text-gray-700 mb-1 text-sm">Archivos adjuntos (opcional)</label>
                        <input type="file" name="attachments[]" id="attachments" class="w-full border border-gray-300 rounded px-2 py-1 focus:ring focus:ring-blue-200" multiple>
                        @error('attachments') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    {{-- El siguiente div que contiene type_id se mantiene, el de vulnerability_type_id se elimina --}}
                    <div class="col-span-3">
                        <label for="observations" class="block font-medium text-gray-700 mb-1 text-sm">Observaciones y consideraciones</label>
                        <textarea name="observations" id="observations" class="w-full border border-gray-300 rounded px-2 py-1 focus:ring focus:ring-blue-200" rows="2">{{ old('observations') }}</textarea>
                        @error('observations') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex flex-col sm:flex-row sm:justify-between col-span-1 sm:col-span-2 md:col-span-3 space-y-4 sm:space-y-0 mt-4">
                        <a href="{{ $viewModel->backRoute }}" class="w-full sm:w-auto text-center sm:text-left bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-2 rounded shadow transition">← Volver al listado</a>
                        <button type="submit" class="w-full sm:w-auto bg-blue-700 hover:bg-blue-800 text-white px-6 py-2 rounded shadow transition">Registrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>