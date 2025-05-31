<x-app-layout>
    <div class="py-8 bg-gradient-to-br from-blue-50 via-white to-blue-100 min-h-screen">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-8">
                <div class="mb-6">
                    <h1 class="text-2xl font-semibold text-gray-800">Gestionar Roles y Permisos</h1>
                    <p class="text-gray-600 mt-1">Usuario: {{ $user->name }}</p>
                </div>

                <form action="{{ route('users.roles.update', $user) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <h2 class="text-lg font-medium text-gray-900 mb-2">Roles</h2>
                        <div class="space-y-2">
                            @foreach($roles as $role)
                                <label class="flex items-center">
                                    <input type="checkbox" 
                                           name="roles[]" 
                                           value="{{ $role->id }}"
                                           {{ $user->hasRole($role) ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2">{{ $role->name }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('roles')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <h2 class="text-lg font-medium text-gray-900 mb-2">Permisos Actuales</h2>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            @forelse($permissions as $permission)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mr-2 mb-2">
                                    {{ $permission->name }}
                                </span>
                            @empty
                                <p class="text-gray-500">No tiene permisos asignados.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="flex justify-end gap-2">
                        <a href="{{ route('users.index') }}" 
                           class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded shadow transition">
                            Cancelar
                        </a>
                        <button type="submit" 
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow transition">
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>