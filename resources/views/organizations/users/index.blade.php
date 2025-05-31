<x-app-layout>
    <div class="py-8 bg-gradient-to-br from-blue-50 via-white to-blue-100 min-h-screen">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-8">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-800">Usuarios de {{ $organization->name }}</h1>
                        <p class="text-gray-600 mt-1">Gestiona los usuarios que pertenecen a esta organización</p>
                    </div>
                    <a href="{{ route('organizations.users.create', $organization) }}"
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
                                <td class="px-4 py-2">{{ $user->role }}</td>
                                <td class="px-4 py-2">
                                    <div class="flex items-center space-x-2">
                                        <form action="{{ route('organizations.users.destroy', [$organization, $user]) }}"
                                            method="POST"
                                            class="inline ml-2">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="text-red-600 hover:underline"
                                                onclick="return confirm('¿Confirmas quitar este usuario de la organización?')">
                                                Quitar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-4 py-6 text-center text-gray-400">
                                    No hay usuarios registrados en esta organización.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $users->links() }}
                    </div>
                </div>
                <div class="flex justify-between mt-6">
                    <a href="{{ route('organizations.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-2 rounded shadow transition">← Volver al listado</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>