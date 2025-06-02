<div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 md:p-8">
    <h2 class="text-2xl font-semibold text-gray-800 mb-6">Dashboard de Administrador</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {{-- Metric: Total Projects --}}
        <div class="bg-blue-100 p-6 rounded-lg shadow">
            <h3 class="text-lg font-medium text-blue-800">Total de Proyectos</h3>
            <p class="text-3xl font-bold text-blue-900 mt-2">
                {{ $data['total_projects'] ?? 'N/A' }}
            </p>
        </div>

        {{-- Metric: Total Users --}}
        <div class="bg-green-100 p-6 rounded-lg shadow">
            <h3 class="text-lg font-medium text-green-800">Total de Usuarios</h3>
            <p class="text-3xl font-bold text-green-900 mt-2">
                {{ $data['total_users'] ?? 'N/A' }}
            </p>
        </div>

        {{-- Metric: Critical Open Vulnerabilities --}}
        <div class="bg-red-100 p-6 rounded-lg shadow">
            <h3 class="text-lg font-medium text-red-800">Vulnerabilidades Críticas Abiertas</h3>
            <p class="text-3xl font-bold text-red-900 mt-2">
                {{ $data['critical_open_vulnerabilities_count'] ?? 'N/A' }}
            </p>
        </div>

        {{-- Metric: Overdue Vulnerabilities --}}
        <div class="bg-orange-100 p-6 rounded-lg shadow"> {{-- Or use bg-yellow-100 if orange is not defined/desired --}}
            <h3 class="text-lg font-medium text-orange-800">Vulnerabilidades Vencidas</h3>
            <p class="text-3xl font-bold text-orange-900 mt-2">
                {{ $data['overdue_vulnerabilities_count'] ?? 'N/A' }}
            </p>
        </div>

        {{-- Metric: SLA Compliance --}}
        <div class="bg-indigo-100 p-6 rounded-lg shadow">
            <h3 class="text-lg font-medium text-indigo-800">Cumplimiento SLA</h3>
            <p class="text-3xl font-bold text-indigo-900 mt-2">
                @if(isset($data['sla_compliance_percentage']) && $data['sla_compliance_percentage'] == -1)
                    N/A <span class="text-sm font-normal">(Placeholder)</span>
                @else
                    {{ $data['sla_compliance_percentage'] ?? 'N/A' }}%
                @endif
            </p>
        </div>

        {{-- Metric: Inactive Users --}}
        <div class="bg-gray-100 p-6 rounded-lg shadow">
            <h3 class="text-lg font-medium text-gray-800">Usuarios Inactivos</h3>
            <p class="text-3xl font-bold text-gray-900 mt-2">
                @if(isset($data['inactive_users_count']) && $data['inactive_users_count'] == -1)
                    N/A <span class="text-sm font-normal">(Placeholder)</span>
                @else
                    {{ $data['inactive_users_count'] ?? 'N/A' }}
                @endif
            </p>
        </div>
    </div>

    {{-- Section: Average Vulnerability Resolution Time by Organization --}}
    <div class="mt-8 pt-6 border-t border-gray-200">
        <h3 class="text-xl font-semibold text-gray-700 mb-4">Tiempo Promedio de Resolución de Vulnerabilidades por Organización</h3>
        <div id="avg-resolution-time-orgs-container">
            <p class="text-gray-600">Cargando datos de tiempo de resolución...</p>
        </div>
    </div>

    {{-- Further sections for admin dashboard --}}
    <div class="mt-8 pt-6 border-t border-gray-200">
        <h3 class="text-xl font-semibold text-gray-700 mb-4">Gestión Rápida</h3>
        {{-- Links to user management, system settings etc. --}}
        <p class="text-gray-600">Enlaces a herramientas administrativas...</p>
    </div>

    @if(!empty($data['global_alerts']))
        <div class="mt-8 pt-6 border-t border-gray-200">
            <h3 class="text-xl font-semibold text-gray-700 mb-4">Alertas Globales</h3>
            <div class="bg-gray-50 p-4 rounded-md shadow-inner space-y-3">
                @foreach($data['global_alerts'] as $alert)
                    <div class="p-3 bg-white rounded-md shadow-sm border border-gray-200">
                        <p class="text-sm text-gray-700">{{ $alert['message'] }}</p>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ \Carbon\Carbon::parse($alert['created_at'])->diffForHumans() }}
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('avg-resolution-time-orgs-container');

    if (container) {
        // Set initial loading message with Tailwind classes for consistency, if desired
        // container.innerHTML = '<p class="text-gray-600">Cargando datos de tiempo de resolución...</p>';

        fetch('{{ route('dashboard.admin.avgResolutionTimeOrgs') }}')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.statusText + ' (status: ' + response.status + ')');
                }
                return response.json();
            })
            .then(data => {
                container.innerHTML = ''; // Clear loading message

                if (data && Object.keys(data).length > 0) {
                    const wrapperDiv = document.createElement('div');
                    wrapperDiv.className = 'bg-white shadow overflow-hidden sm:rounded-md';

                    const ul = document.createElement('ul');
                    ul.setAttribute('role', 'list');
                    ul.className = 'divide-y divide-gray-200';

                    let hasActualData = false;

                    for (const orgName in data) {
                        hasActualData = true; // Assume any key means data to display
                        const li = document.createElement('li');
                        li.className = 'px-6 py-4';

                        const itemDiv = document.createElement('div');
                        itemDiv.className = 'flex items-center justify-between';

                        const nameP = document.createElement('p');
                        nameP.className = 'text-sm font-medium text-gray-900 truncate';
                        nameP.textContent = orgName;

                        const timeP = document.createElement('p');
                        timeP.className = 'text-sm text-gray-500';

                        let timeText = 'N/A';
                        // Logic from service: 0 might mean actual 0 days avg, or no resolved vulnerabilities.
                        // The service sends 0 for "no resolved vulnerabilities" or actual 0 days average.
                        // It might send null or 'N/A' string for other cases, which this handles.
                        if (data[orgName] !== null && data[orgName] !== undefined) {
                             if (typeof data[orgName] === 'number') {
                                timeText = data[orgName] + ' días';
                            } else if (typeof data[orgName] === 'string' && data[orgName].toLowerCase() === 'n/a') {
                                timeText = 'N/A';
                            } else {
                                timeText = data[orgName]; // Use as is if it's a pre-formatted string like "0 días" or "N/A"
                            }
                        }
                        timeP.textContent = timeText;

                        itemDiv.appendChild(nameP);
                        itemDiv.appendChild(timeP);
                        li.appendChild(itemDiv);
                        ul.appendChild(li);
                    }

                    if (hasActualData) {
                        wrapperDiv.appendChild(ul);
                        container.appendChild(wrapperDiv);
                    } else {
                        // This case should ideally be caught by Object.keys(data).length > 0
                        // but as a fallback if data is {} or contains only null/undefined values
                        // depending on how backend guarantees non-empty data.
                        container.innerHTML = '<p class="text-gray-600">No hay datos de tiempo de resolución disponibles para las organizaciones.</p>';
                    }

                } else {
                    container.innerHTML = '<p class="text-gray-600">No hay datos de tiempo de resolución disponibles para las organizaciones.</p>';
                }
            })
            .catch(error => {
                console.error('Error fetching average resolution times:', error);
                container.innerHTML = '<p class="text-red-500">Error al cargar los datos de tiempo de resolución. Por favor, intente recargar la página.</p>';
            });
    }
});
</script>
