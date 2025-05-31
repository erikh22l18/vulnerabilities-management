<x-app-layout>
    <div class="py-8 bg-gradient-to-br from-blue-50 via-white to-blue-100 min-h-screen">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-8">
                <h1 class="text-2xl font-semibold text-gray-800 mb-6">Registrar Proyecto</h1>
                <form action="{{ route('projects.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label for="identifier" class="block font-medium text-gray-700 mb-1">Identificación del proyecto</label>
                        <input type="text" name="identifier" id="identifier" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200" value="{{ old('identifier') }}" required>
                        @error('identifier') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="name" class="block font-medium text-gray-700 mb-1">Nombre</label>
                        <input type="text" name="name" id="name" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200" value="{{ old('name') }}" required>
                        @error('name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="general_objective" class="block font-medium text-gray-700 mb-1">Objetivo general</label>
                        <textarea name="general_objective" id="general_objective" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200" required>{{ old('general_objective') }}</textarea>
                        @error('general_objective') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="organization_id" class="block font-medium text-gray-700 mb-1">Organización</label>
                        <select name="organization_id" id="organization_id" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200" required>
                            <option value="">Seleccione una organización</option>
                            @foreach ($organizations as $org)
                                <option value="{{ $org->id }}" {{ old('organization_id') == $org->id ? 'selected' : '' }}>
                                    {{ $org->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('organization_id') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex justify-between mt-6">
                        <a href="{{ route('projects.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-2 rounded shadow transition">← Volver al listado</a>
                        <button type="submit" class="bg-blue-700 hover:bg-blue-800 text-white px-6 py-2 rounded shadow transition">Registrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>