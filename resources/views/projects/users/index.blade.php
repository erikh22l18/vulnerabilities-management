<x-app-layout>
    <div class="py-8 bg-gradient-to-br from-blue-50 via-white to-blue-100 min-h-screen">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-8">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-800">Usuarios del Proyecto: {{ $project->name }}</h1>
                        <p class="text-gray-600 mt-1">Gestiona los usuarios que pertenecen a este proyecto</p>
                    </div>
                    <a href="{{ route('projects.users.create', $project) }}"
                        class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 transition">
                        + Agregar Usuario
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full bg-white shadow rounded">
                        <thead>
                            <tr class="bg-blue-100 text-left">
                                <th class="px-4 py-2">Nombre</th>
                                <th class="px-4 py-2">Email</th>
                                <th class="px-4 py-2">Rol</th>
                                <th class="px-4 py-2">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                            <tr class="border-b hover:bg-blue-50 transition">
                                <td class="px-4 py-2">{{ $user->name }}</td>
                                <td class="px-4 py-2">{{ $user->email }}</td>
                                <td class="px-4 py-2">{{ $user->pivot->role ?? 'Miembro' }}</td>
                                <td class="px-4 py-2">
                                    <form action="{{ route('projects.users.destroy', [$project, $user]) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-red-600 hover:text-red-800 font-medium"
                                            onclick="return confirm('¿Estás seguro que deseas eliminar este usuario del proyecto?')">
                                            <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Quitar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-4 py-6 text-center text-gray-400">No hay usuarios asignados a este proyecto.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="flex justify-between mt-6">
                        <a href="{{ route('projects.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-2 rounded shadow transition">← Volver al listado</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>