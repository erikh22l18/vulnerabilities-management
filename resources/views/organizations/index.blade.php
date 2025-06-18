<x-app-layout>
    <div class="py-8 bg-gradient-to-br from-blue-50 via-white to-blue-100 min-h-screen">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-8">
                <div class="flex items-center justify-between mb-6">
                    <h1 class="text-2xl font-semibold text-gray-800">Organizaciones</h1>
                    <a href="{{ route('organizations.create') }}" 
                       class="bg-blue-600 text-white p-2 md:px-4 md:py-2 rounded shadow hover:bg-blue-700 transition inline-flex items-center"
                       aria-label="Nueva Organización">
                        <svg class="w-5 h-5 md:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        <span class="hidden md:inline">+ Nueva Organización</span>
                    </a>
                </div>
                <div class="mb-4">
                    <input type="text" id="organizationTableSearchInput" class="mt-1 block w-full md:w-1/3 px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Buscar organizaciones...">
                </div>
                <div class="overflow-x-auto min-h-[400px]">
                    <!--
                        Table columns responsive design:
                        - 'Nombre' and 'Acciones' are always visible.
                        - 'Dirección' is hidden on screens smaller than 'lg'.
                        - 'Usuarios' is hidden on screens smaller than 'md'.
                        - 'Proyectos' is hidden on screens smaller than 'sm'.
                    -->
                    <table id="organizationsTable" class="w-full bg-white shadow rounded">
                        <thead>
                            <tr class="bg-blue-100 text-left">
                                <th class="px-4 py-2 text-sm">Nombre</th>
                                <th class="px-4 py-2 hidden lg:table-cell text-sm">Dirección</th>
                                <th class="px-4 py-2 hidden md:table-cell text-sm">Usuarios</th>
                                <th class="px-4 py-2 hidden sm:table-cell text-sm">Proyectos</th>
                                <th class="px-4 py-2 text-sm"><span class="sr-only">Acciones</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($organizations as $org)
                                {{-- Full HTML for an organization row as previously defined in the file --}}
                                <tr class="border-b hover:bg-blue-50 transition">
                                    <td class="px-4 py-2 font-medium text-gray-700 text-sm">{{ $org->name }}</td>
                                    <td class="px-4 py-2 text-gray-600 hidden lg:table-cell text-sm">{{ $org->location ?? '-' }}</td>
                                    <td class="px-4 py-2 hidden md:table-cell text-sm">
                                        <x-user-avatars :users="$org->users" />
                                    </td>
                                    <td class="px-4 py-2 hidden sm:table-cell text-sm">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            {{ $org->projects_count }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 text-sm">
                                        {{-- Full actions dropdown HTML as previously defined --}}
                                        <div class="relative" x-data="{ open: false }">
                                            <button @click="open = !open" class="inline-flex items-center text-gray-700 hover:text-gray-900">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path></svg>
                                            </button>
                                            <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95">
                                                <a href="{{ route('organizations.edit', $org) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"><div class="flex items-center"><svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536M9 13l6.586-6.586a2 2 0 112.828 2.828L11.828 15.828a2 2 0 01-2.828 0L9 13zm-6 6h6v-2a2 2 0 012-2h2a2 2 0 012 2v2h6"></path></svg>Editar</div></a>
                                                <a href="{{ route('organizations.users.index', $org) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"><div class="flex items-center"><svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>Usuarios</div></a>
                                                <a href="{{ route('organizations.projects.index', $org) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"><div class="flex items-center"><svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m-3-3H9m0 0V8m0 4h3m1-4h3m-3 0V4m0 4h3m-3 0H9m0 0V4m0 4h3"></path></svg>Proyectos</div></a>
                                                <form action="{{ route('organizations.destroy', $org) }}" method="POST" class="block">@csrf @method('DELETE')<button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100" onclick="return confirm('¿Seguro que deseas eliminar esta organización?')"><div class="flex items-center"><svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M8 7V5a2 2 0 012-2h4a2 2 0 012 2v2"></path></svg>Eliminar</div></button></form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-6 text-center text-gray-400 text-sm">
                                        No hay organizaciones registradas. {{-- Original empty message --}}
                                    </td>
                                </tr>
                            @endforelse
                            {{-- JS-controlled "no search results" row --}}
                            <tr id="noOrganizationSearchResultsRow" style="display: none;">
                                <td colspan="5" class="px-4 py-6 text-center text-gray-500 text-sm">
                                    No se encontraron organizaciones para su búsqueda.
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $organizations->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

@verbatim
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('organizationTableSearchInput');
    const table = document.getElementById('organizationsTable');

    if (!table || !searchInput) {
        // console.error('Search input or table not found for organizations');
        return;
    }
    const tableBody = table.querySelector('tbody');
    if (!tableBody) return;

    const dataRows = Array.from(tableBody.getElementsByTagName('tr')).filter(row => row.id !== 'noOrganizationSearchResultsRow');
    const noSearchResultsRow = document.getElementById('noOrganizationSearchResultsRow');

    const initialEmptyMessageTr = Array.from(tableBody.getElementsByTagName('tr')).find(
        tr => tr.cells.length === 1 && tr.cells[0].getAttribute('colspan') === '5' && tr.id !== 'noOrganizationSearchResultsRow'
    );

    if (initialEmptyMessageTr && dataRows.length === 0) {
        searchInput.disabled = true;
        return;
    }

    if (!noSearchResultsRow) {
        // console.error('noOrganizationSearchResultsRow not found');
        return;
    }

    searchInput.addEventListener('keyup', function () {
        const searchTerm = searchInput.value.toLowerCase().trim();
        let visibleRows = 0;
        const headerCells = table.querySelectorAll('thead th');

        let addressColumnIndex = -1;
        headerCells.forEach((th, index) => {
            // Using toLowerCase for 'dirección' to be safe
            if (th.textContent.trim().toLowerCase() === 'dirección') {
                addressColumnIndex = index;
            }
        });

        dataRows.forEach(row => {
            // Ensure it's a data row before processing
            if (row.id === 'noOrganizationSearchResultsRow') return;

            const nameText = row.cells[0] ? row.cells[0].textContent.toLowerCase() : '';
            let addressText = '';

            if (addressColumnIndex !== -1 && row.cells[addressColumnIndex]) {
                addressText = row.cells[addressColumnIndex].textContent.toLowerCase();
            }

            const rowTextToSearch = nameText + ' ' + addressText;

            if (rowTextToSearch.includes(searchTerm)) {
                row.style.display = '';
                visibleRows++;
            } else {
                row.style.display = 'none';
            }
        });

        noSearchResultsRow.style.display = visibleRows === 0 ? '' : 'none';
    });
});
</script>
@endverbatim
</x-app-layout>