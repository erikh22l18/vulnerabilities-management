<x-app-layout>
    <div class="py-8 bg-gradient-to-br from-blue-50 via-white to-blue-100 min-h-screen">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-8">
                {{-- Header Section --}}
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-800">{{ $viewModel->title }}</h1>
                        @if($viewModel->subtitle)
                            <p class="text-gray-600 mt-1">{{ $viewModel->subtitle }}</p>
                        @endif
                    </div>

                    <div class="flex gap-2">
                        @if($viewModel->can_create)
                            <a href="{{ $viewModel->createRoute }}" 
                               class="bg-blue-600 text-white p-2 md:px-4 md:py-2 rounded shadow hover:bg-blue-700 transition inline-flex items-center"
                               aria-label="Nuevo Proyecto">
                                <svg class="w-5 h-5 md:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                <span class="hidden md:inline">Nuevo Proyecto</span>
                            </a>
                        @endif
                    </div>
                </div>

                <div class="mb-4">
                    <input type="text" id="projectTableSearchInput" class="mt-1 block w-full md:w-1/3 px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Buscar proyectos...">
                </div>

                {{-- Table Section --}}
                <div class="overflow-x-auto min-h-[400px]">
                    <!--
                        Table columns responsive design:
                        - 'Identificador', 'Nombre', 'Acciones' are always visible.
                        - 'Organización' is hidden on screens smaller than 'md'.
                        - 'Estado' is hidden on screens smaller than 'md'.
                        - 'Usuarios' is hidden on screens smaller than 'sm'.
                        - 'Vulnerabilidades' is hidden on screens smaller than 'sm'.
                    -->
                    <table id="projectsTable" class="w-full bg-white shadow rounded">
                        <thead>
                            <tr class="bg-blue-100 text-left">
                                <th class="px-4 py-2 text-sm">Identificador</th>
                                <th class="px-4 py-2 text-sm">Nombre</th>
                                @if(!$viewModel->context || $viewModel->context !== 'organization')
                                    <th class="px-4 py-2 hidden md:table-cell text-sm">Organización</th>
                                @endif
                                <th class="px-4 py-2 hidden md:table-cell text-sm">Estado</th>
                                <th class="px-4 py-2 hidden sm:table-cell text-sm">Usuarios</th>
                                <th class="px-4 py-2 hidden sm:table-cell text-sm">Vulnerabilidades</th>
                                <th class="px-4 py-2 text-sm"><span class="sr-only">Acciones</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($viewModel->projects as $project)
                                <tr class="border-b hover:bg-blue-50 transition">
                                    <td class="px-4 py-2 text-sm">{{ $project->identifier }}</td>
                                    <td class="px-4 py-2 text-sm">{{ $project->name }}</td>
                                    @if(!$viewModel->context || $viewModel->context !== 'organization')
                                        <td class="px-4 py-2 hidden md:table-cell text-sm">{{ $project->organization->name }}</td>
                                    @endif
                                    <td class="px-4 py-2 hidden md:table-cell text-sm">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            {{ strtolower($project->status) === 'active' ? 'bg-green-100 text-green-800' : (strtolower($project->status) === 'activo' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800') }}">
                                            {{ ucfirst($project->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 hidden sm:table-cell text-sm">
                                        <x-user-avatars :users="$project->users" />
                                    </td>
                                    <td class="px-4 py-2 hidden sm:table-cell text-sm">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $project->vulnerabilities_count }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 text-sm">
                                        <div class="relative" x-data="{ open: false }">
                                            <button @click="open = !open" class="text-gray-400 hover:text-gray-600">
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                                </svg>
                                            </button>

                                            <div x-show="open" @click.away="open = false"
                                                 class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                                                
                                                @can('verVulnerabilidades', $project)
                                                <a href="{{ route('projects.vulnerabilities.index', $project) }}" 
                                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                    Ver Vulnerabilidades
                                                </a>
                                                @endcan
                                                <a href="{{ route('projects.users.index', $project) }}" 
                                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                    Gestionar Usuarios
                                                </a>
                                                @can('viewPdfReport', $project)
                                                <a href="{{ route('projects.report.pdf', $project) }}" 
                                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                                   target="_blank">  {{-- target="_blank" para abrir en nueva pestaña --}}
                                                    Descargar Informe (PDF)
                                                </a>
                                                @endcan
                                                @can('update', $project)
                                                    <div class="border-t border-gray-100"></div>
                                                    @if(strtolower($project->status) === 'active' || strtolower($project->status) === 'activo')
                                                        <form action="{{ route('projects.updateStatus', $project) }}" method="POST" class="block w-full">
                                                            @csrf
                                                            @method('PUT')
                                                            <input type="hidden" name="status" value="inactive">
                                                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-yellow-700 hover:bg-gray-100" title="Marcar como Inactivo">
                                                                Inactivar Proyecto
                                                            </button>
                                                        </form>
                                                    @else
                                                        <form action="{{ route('projects.updateStatus', $project) }}" method="POST" class="block w-full">
                                                            @csrf
                                                            @method('PUT')
                                                            <input type="hidden" name="status" value="active">
                                                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-green-700 hover:bg-gray-100" title="Marcar como Activo">
                                                                Activar Proyecto
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endcan
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-6 text-center text-gray-500 text-sm">
                                        No se encontraron proyectos.
                                    </td>
                                </tr>
                            @endforelse
                            <tr id="noProjectSearchResultsRow" style="display: none;">
                                <td colspan="7" class="px-4 py-6 text-center text-gray-500 text-sm">
                                    No se encontraron proyectos para su búsqueda.
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $viewModel->projects->links() }}
                    </div>
                </div>

                {{-- Back Button --}}
                @if($viewModel->backRoute)
                    <div class="mt-6">
                        <a href="{{ $viewModel->backRoute }}" 
                           class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-2 rounded shadow transition">
                            ← Volver
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('projectTableSearchInput');
    const table = document.getElementById('projectsTable');

    if (!table || !searchInput) {
        // console.error('Search input or table not found for projects');
        return;
    }
    const tableBody = table.querySelector('tbody');
    if (!tableBody) return;

    const dataRows = Array.from(tableBody.getElementsByTagName('tr')).filter(row => row.id !== 'noProjectSearchResultsRow');
    const noSearchResultsRow = document.getElementById('noProjectSearchResultsRow');

    // Check if the original @empty message is showing. For projects, it has colspan="7".
    const initialEmptyMessageTr = Array.from(tableBody.getElementsByTagName('tr')).find(
        tr => tr.cells.length === 1 && tr.cells[0].getAttribute('colspan') === '7' && tr.id !== 'noProjectSearchResultsRow'
    );

    if (initialEmptyMessageTr && dataRows.length === 0) {
        searchInput.disabled = true; // Disable search if table is initially empty
        return;
    }

    if (!noSearchResultsRow) {
        // console.error('noProjectSearchResultsRow not found');
        return;
    }

    searchInput.addEventListener('keyup', function () {
        const searchTerm = searchInput.value.toLowerCase().trim();
        let visibleRows = 0;
        const headerCells = table.querySelectorAll('thead th');

        let organizationColumnIndex = -1;
        headerCells.forEach((th, index) => {
            if (th.textContent.trim().toLowerCase() === 'organización') {
                organizationColumnIndex = index;
            }
        });

        dataRows.forEach(row => {
            // Ensure it's a data row before processing
            if (row.id === 'noProjectSearchResultsRow') return;

            const identifierText = row.cells[0] ? row.cells[0].textContent.toLowerCase() : '';
            const nameText = row.cells[1] ? row.cells[1].textContent.toLowerCase() : '';
            let organizationText = '';

            if (organizationColumnIndex !== -1 && row.cells[organizationColumnIndex]) {
                organizationText = row.cells[organizationColumnIndex].textContent.toLowerCase();
            }

            const rowTextToSearch = identifierText + ' ' + nameText + ' ' + organizationText;

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
</x-app-layout>