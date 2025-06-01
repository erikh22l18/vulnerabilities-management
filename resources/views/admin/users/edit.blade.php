<x-app-layout>
    <div class="py-8 bg-gradient-to-br from-blue-50 via-white to-blue-100 min-h-screen">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-8">
                <h1 class="text-2xl font-semibold text-gray-800 mb-6">Editar Usuario</h1>
                
                <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="name" class="block font-medium text-gray-700 mb-1">Nombres completos</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" 
                               class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200">
                        @error('name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="identification" class="block font-medium text-gray-700 mb-1">Identificación</label>
                        <input type="text" id="identification" name="identification" value="{{ old('identification', $user->identification) }}" 
                               class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200">
                        @error('identification') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="email" class="block font-medium text-gray-700 mb-1">Correo electrónico</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" 
                               class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200">
                        @error('email') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="area" class="block font-medium text-gray-700 mb-1">Área</label>
                        <input type="text" id="area" name="area" value="{{ old('area', $user->area) }}" 
                               class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200">
                        @error('area') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="organization_id" class="block font-medium text-gray-700 mb-1">Organización</label>
                        <select name="organization_id" class="w-full border border-gray-300 rounded px-3 py-2">
                            @foreach ($organizations as $org)
                            <option value="{{ $org->id }}" 
                                {{ old('organization_id', $user->organization_id) == $org->id ? 'selected' : '' }}>
                                {{ $org->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('organization_id') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="role" class="block font-medium text-gray-700 mb-1">Rol</label>
                        <select id="role" name="role" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200">
                            <option value="">Seleccione un rol</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}" 
                                    {{ old('role', $currentRoleName ?? '') == $role->name ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('role') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="border-t border-gray-200 mt-6 pt-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Cambiar contraseña (opcional)</h2>
                        <div>
                            <label for="password" class="block font-medium text-gray-700 mb-1">Nueva contraseña</label>
                            <input type="password" id="password" name="password" 
                                   class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200">
                            @error('password') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="mt-4">
                            <label for="password_confirmation" class="block font-medium text-gray-700 mb-1">Confirmar nueva contraseña</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" 
                                   class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200">
                            @error('password_confirmation') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="flex justify-between mt-6">
                        <a href="{{ route('admin.users.index') }}"
                           class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-2 rounded shadow transition">
                            ← Volver al listado
                        </a>
                        <button type="submit" 
                                class="bg-blue-700 hover:bg-blue-800 text-white px-6 py-2 rounded shadow transition">
                            Guardar cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>