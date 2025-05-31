<x-app-layout>
    <div class="py-8 bg-gradient-to-br from-blue-50 via-white to-blue-100 min-h-screen">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-8">
                <div class="flex items-center justify-between mb-6">
                    <h1 class="text-2xl font-semibold text-gray-800">Gestionar Usuarios - {{ $organization->name }}</h1>
                </div>

                <!-- Pestañas de navegación -->
                <div class="border-b border-gray-200 mb-6">
                    <nav class="-mb-px flex space-x-6">
                        <button type="button" 
                                onclick="showTab('add-existing')"
                                class="tab-button border-b-2 border-blue-500 py-2 px-1 text-sm font-medium text-blue-600">
                            Agregar Existentes
                        </button>
                        <button type="button" 
                                onclick="showTab('create-new')"
                                class="tab-button border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                            Crear Nuevo Usuario
                        </button>
                    </nav>
                </div>

                <!-- Tab: Agregar usuarios existentes -->
                <div id="add-existing" class="tab-content">
                    @if($availableUsers->isEmpty())
                        <div class="text-gray-500 text-center py-4">
                            No hay usuarios disponibles para agregar.
                        </div>
                    @else
                        <form action="{{ route('organizations.users.store', $organization) }}" method="POST" class="space-y-6">
                            @csrf
                            <div class="space-y-4">
                                @foreach($availableUsers as $user)
                                    <div class="flex items-center p-4 border rounded hover:bg-gray-50">
                                        <input type="checkbox" 
                                               name="user_ids[]" 
                                               value="{{ $user->id }}" 
                                               id="user_{{ $user->id }}"
                                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        <label for="user_{{ $user->id }}" class="ml-3 flex-1">
                                            <div class="font-medium text-gray-700">{{ $user->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                        </label>
                                    </div>
                                @endforeach
                            </div>

                            @error('user_ids')
                                <div class="text-red-600 text-sm">{{ $message }}</div>
                            @enderror

                            <div class="flex justify-end gap-2">
                                <button type="submit" 
                                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow transition">
                                    Agregar Seleccionados
                                </button>
                            </div>
                        </form>
                    @endif
                </div>

                <!-- Tab: Crear nuevo usuario -->
                <div id="create-new" class="tab-content hidden">
                    <form action="{{ route('organizations.users.store', $organization) }}" method="POST" class="space-y-6">
                        @csrf
                        <input type="hidden" name="create_new" value="true">
                        
                        <div>
                            <label for="name" class="block font-medium text-gray-700 mb-1">Nombre</label>
                            <input type="text" name="name" id="name" 
                                   class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200"
                                   value="{{ old('name') }}" required>
                            @error('name')
                                <span class="text-red-600 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block font-medium text-gray-700 mb-1">Correo electrónico</label>
                            <input type="email" name="email" id="email" 
                                   class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200"
                                   value="{{ old('email') }}" required>
                            @error('email')
                                <span class="text-red-600 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label for="password" class="block font-medium text-gray-700 mb-1">Contraseña</label>
                            <input type="password" name="password" id="password" 
                                   class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200"
                                   required>
                            @error('password')
                                <span class="text-red-600 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block font-medium text-gray-700 mb-1">Confirmar contraseña</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" 
                                   class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200"
                                   required>
                        </div>

                        <div class="flex justify-end gap-2">
                            <button type="submit" 
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow transition">
                                Crear Usuario
                            </button>
                        </div>
                    </form>
                </div>

                <div class="mt-6">
                    <a href="{{ route('organizations.users.index', $organization) }}" 
                       class="inline-block bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded shadow transition">
                        Volver
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showTab(tabId) {
            // Ocultar todos los contenidos
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });
            
            // Mostrar el contenido seleccionado
            document.getElementById(tabId).classList.remove('hidden');
            
            // Actualizar estilos de los botones
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('border-blue-500', 'text-blue-600');
                button.classList.add('border-transparent', 'text-gray-500');
            });
            
            // Activar el botón seleccionado
            const activeButton = document.querySelector(`[onclick="showTab('${tabId}')"]`);
            activeButton.classList.remove('border-transparent', 'text-gray-500');
            activeButton.classList.add('border-blue-500', 'text-blue-600');
        }
    </script>
</x-app-layout>