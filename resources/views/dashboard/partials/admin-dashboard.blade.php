<div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 md:p-8">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-800">Dashboard de Administrador</h2>
        <button id="customize-admin-dashboard-btn" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Personalizar Dashboard
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6"> {{-- Changed lg:grid-cols-3 to lg:grid-cols-4 --}}
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

        {{-- Metric: Tasa de Remediación (Críticas/Altas) --}}
        <div class="bg-sky-100 p-6 rounded-lg shadow">
            <h3 class="text-lg font-medium text-sky-800">Tasa de Remediación (Críticas/Altas)</h3>
            <p class="text-3xl font-bold text-sky-900 mt-2">
                {{ isset($data['critical_high_remediation_rate']) ? number_format($data['critical_high_remediation_rate'], 1) . '%' : 'N/A' }}
            </p>
        </div>

        {{-- Metric: Remediación a Tiempo (%) --}}
        <div class="bg-teal-100 p-6 rounded-lg shadow">
            <h3 class="text-lg font-medium text-teal-800">Remediación a Tiempo (%)</h3>
            <p class="text-3xl font-bold text-teal-900 mt-2">
                {{ isset($data['on_time_remediation_percentage']) ? number_format($data['on_time_remediation_percentage'], 1) . '%' : 'N/A' }}
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

{{-- Modal for Dashboard Customization --}}
<div id="admin-dashboard-customize-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center hidden">
    <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-lg mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-semibold text-gray-800">Personalizar Dashboard de Administrador</h3>
            <button id="admin-customize-modal-close-btn" class="text-gray-600 hover:text-gray-900 text-2xl">&times;</button>
        </div>

        <div class="space-y-4">
            {{-- Conceptual Widget Toggles --}}
            <div>
                <input type="checkbox" id="widget_toggle_admin_metrics_total_projects" name="admin_metrics_total_projects" checked class="form-checkbox h-5 w-5 text-blue-600">
                <label for="widget_toggle_admin_metrics_total_projects" class="ml-2 text-gray-700">Mostrar Total de Proyectos</label>
            </div>
            <div>
                <input type="checkbox" id="widget_toggle_admin_metrics_total_users" name="admin_metrics_total_users" checked class="form-checkbox h-5 w-5 text-blue-600">
                <label for="widget_toggle_admin_metrics_total_users" class="ml-2 text-gray-700">Mostrar Total de Usuarios</label>
            </div>
            <div>
                <input type="checkbox" id="widget_toggle_admin_metrics_critical_vulnerabilities" name="admin_metrics_critical_vulnerabilities" checked class="form-checkbox h-5 w-5 text-blue-600">
                <label for="widget_toggle_admin_metrics_critical_vulnerabilities" class="ml-2 text-gray-700">Mostrar Vulnerabilidades Críticas Abiertas</label>
            </div>
            <div>
                <input type="checkbox" id="widget_toggle_admin_metrics_overdue_vulnerabilities" name="admin_metrics_overdue_vulnerabilities" checked class="form-checkbox h-5 w-5 text-blue-600">
                <label for="widget_toggle_admin_metrics_overdue_vulnerabilities" class="ml-2 text-gray-700">Mostrar Vulnerabilidades Vencidas</label>
            </div>
            <div>
                <input type="checkbox" id="widget_toggle_admin_metrics_sla_compliance" name="admin_metrics_sla_compliance" checked class="form-checkbox h-5 w-5 text-blue-600">
                <label for="widget_toggle_admin_metrics_sla_compliance" class="ml-2 text-gray-700">Mostrar Cumplimiento SLA</label>
            </div>
            <div>
                <input type="checkbox" id="widget_toggle_admin_metrics_inactive_users" name="admin_metrics_inactive_users" checked class="form-checkbox h-5 w-5 text-blue-600">
                <label for="widget_toggle_admin_metrics_inactive_users" class="ml-2 text-gray-700">Mostrar Usuarios Inactivos</label>
            </div>
            <div>
                <input type="checkbox" id="widget_toggle_admin_section_avg_resolution_time" name="admin_section_avg_resolution_time" checked class="form-checkbox h-5 w-5 text-blue-600">
                <label for="widget_toggle_admin_section_avg_resolution_time" class="ml-2 text-gray-700">Mostrar Sección: Tiempo Promedio de Resolución</label>
            </div>
            <div>
                <input type="checkbox" id="widget_toggle_admin_section_quick_management" name="admin_section_quick_management" checked class="form-checkbox h-5 w-5 text-blue-600">
                <label for="widget_toggle_admin_section_quick_management" class="ml-2 text-gray-700">Mostrar Sección: Gestión Rápida</label>
            </div>
            <div>
                <input type="checkbox" id="widget_toggle_admin_section_global_alerts" name="admin_section_global_alerts" checked class="form-checkbox h-5 w-5 text-blue-600">
                <label for="widget_toggle_admin_section_global_alerts" class="ml-2 text-gray-700">Mostrar Sección: Alertas Globales</label>
            </div>
        </div>

        <div class="mt-8 flex justify-end space-x-3">
            <button id="admin-customize-modal-cancel-btn" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                Cancelar
            </button>
            <button type="button" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                Guardar Cambios (No funcional)
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Script for Async Loading of Avg Resolution Time
    const avgResTimeContainer = document.getElementById('avg-resolution-time-orgs-container');
    if (avgResTimeContainer) {
        // ... (existing async loading script remains here, unchanged by this diff) ...
        // For brevity, the existing script content is not repeated in this diff block
        // but it should be assumed to be present if it was in the original search block.
        // The following search block will target the end of it to append new JS.
    }

    // Script for Modal Toggle
    const customizeBtn = document.getElementById('customize-admin-dashboard-btn');
    const customizeModal = document.getElementById('admin-dashboard-customize-modal');
    const closeModalBtn = document.getElementById('admin-customize-modal-close-btn');
    const cancelModalBtn = document.getElementById('admin-customize-modal-cancel-btn'); // Added cancel button

    if (customizeBtn && customizeModal && closeModalBtn && cancelModalBtn) {
        customizeBtn.addEventListener('click', () => {
            customizeModal.classList.remove('hidden');
        });
        closeModalBtn.addEventListener('click', () => {
            customizeModal.classList.add('hidden');
        });
        cancelModalBtn.addEventListener('click', () => { // Also hide modal on cancel
            customizeModal.classList.add('hidden');
        });
        // Optional: Close modal if user clicks outside of it
        customizeModal.addEventListener('click', function(event) {
            if (event.target === customizeModal) {
                customizeModal.classList.add('hidden');
            }
        });
    }
});

// The original script for avg-resolution-time-orgs-container continues below if it was there.
// This diff only shows the addition of the modal and its JS.
// The tool should handle appending this correctly.
// For the sake of this specific diff, let's assume the previous script block for avg-resolution-time ends here.
// And the new script for modal is added.

// If the original script for avg-resolution-time-orgs-container was inside the DOMContentLoaded listener,
// then the modal JS should also be inside that same listener, or a new one.
// The following shows placing the modal JS inside the existing DOMContentLoaded listener.

// (Content of avg-resolution-time-orgs-container script)
// ...
// (End of avg-resolution-time-orgs-container script)

// This is just a placeholder to ensure the diff tool understands where to append the modal JS
// if the original script was more complex.
// The actual merge should be:
// document.addEventListener('DOMContentLoaded', function () {
//   // ... existing avg-resolution-time script ...
//
//   // ... new modal toggle script ...
// });

// For this tool, I'll assume the previous script block ended before this new one.
// Or that this new script content for modal is added *within* the existing DOMContentLoaded.
// The diff provided for the <script> tag should append the modal JS logic
// within the existing DOMContentLoaded listener.

// Let's refine the script part of the diff to make it clearer.
// The following search block should be the start of the existing script.
// Then the replace block will show the *combined* script.

</script>
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
                avgResTimeContainer.innerHTML = '<p class="text-red-500">Error al cargar los datos de tiempo de resolución. Por favor, intente recargar la página.</p>';
            });
    } // End of if (avgResTimeContainer)

    // Script for Modal Toggle
    const customizeBtn = document.getElementById('customize-admin-dashboard-btn');
    const customizeModal = document.getElementById('admin-dashboard-customize-modal');
    const closeModalBtn = document.getElementById('admin-customize-modal-close-btn');
    const cancelModalBtn = document.getElementById('admin-customize-modal-cancel-btn');

    if (customizeBtn && customizeModal && closeModalBtn && cancelModalBtn) {
        customizeBtn.addEventListener('click', () => {
            customizeModal.classList.remove('hidden');
        });
        closeModalBtn.addEventListener('click', () => {
            customizeModal.classList.add('hidden');
        });
        cancelModalBtn.addEventListener('click', () => {
            customizeModal.classList.add('hidden');
        });
        // Optional: Close modal if user clicks outside of it
        customizeModal.addEventListener('click', function(event) {
            // Check if the click is directly on the modal background (event.target is the modal itself)
            // and not on a child element.
            if (event.target === customizeModal) {
                customizeModal.classList.add('hidden');
            }
        });
    }
});
</script>
