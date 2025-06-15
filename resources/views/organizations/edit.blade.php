<x-app-layout>
    <div class="py-8 bg-gradient-to-br from-blue-50 via-white to-blue-100 min-h-screen">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-8">
                <h1 class="text-2xl font-semibold text-gray-800 mb-6">Editar Organización</h1>
                <form action="{{ route('organizations.update', $organization) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')
                    <div>
                        <label for="name" class="block font-medium text-gray-700 mb-1 text-sm">Nombre</label>
                        <input type="text" name="name" id="name" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200" value="{{ old('name', $organization->name) }}" required>
                        @error('name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="location" class="block font-medium text-gray-700 mb-1 text-sm">Ubicación</label>
                        <input type="text" name="location" id="location" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200" value="{{ old('location', $organization->location) }}">
                        @error('location') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="business_model" class="block font-medium text-gray-700 mb-1 text-sm">Modelo de negocio</label>
                        <input type="text" name="business_model" id="business_model" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200" value="{{ old('business_model', $organization->business_model) }}">
                        @error('business_model') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex justify-between mt-6">
                        <a href="{{ route('organizations.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-2 rounded shadow transition">← Volver al listado</a>
                        <button type="submit" class="bg-blue-700 hover:bg-blue-800 text-white px-6 py-2 rounded shadow transition">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>