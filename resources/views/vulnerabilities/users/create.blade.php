<x-app-layout>
    <div class="py-8 bg-gradient-to-br from-blue-50 via-white to-blue-100 min-h-screen">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-8">
                <div class="flex items-center justify-between mb-6">
                    <h1 class="text-2xl font-semibold text-gray-800">Agregar Usuarios a la Vulnerabilidad - {{ $vulnerability->title }}</h1>
                </div>

                @if($availableUsers->isEmpty())
                    <div class="text-gray-500 text-center py-4">
                        No hay usuarios disponibles para agregar.
                    </div>
                @else
                    <form action="{{ route('vulnerabilities.users.store', $vulnerability) }}" method="POST" class="space-y-6">
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
                                        <div class="text-sm text-gray-400">{{ $user->organization->name }}</div>
                                    </label>
                                </div>
                            @endforeach
                        </div>

                        @error('user_ids')
                            <div class="text-red-600 text-sm">{{ $message }}</div>
                        @enderror

                        <div class="flex justify-end gap-2">
                            <a href="{{ route('vulnerabilities.users.index', $vulnerability) }}" 
                               class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded shadow transition">
                                Cancelar
                            </a>
                            <button type="submit" 
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow transition">
                                Agregar Seleccionados
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>