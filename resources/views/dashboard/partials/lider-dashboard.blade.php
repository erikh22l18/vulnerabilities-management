<div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 md:p-8">
    <h2 class="text-2xl font-semibold text-gray-800 mb-6">Dashboard de Líder de Proyecto</h2>

    {{-- Adjusted grid to better accommodate 5 items, aiming for a 2-column feel on medium screens and up --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Metric: Projects Led Count --}}
        <div class="bg-indigo-100 p-6 rounded-lg shadow">
            <h3 class="text-lg font-medium text-indigo-800">Proyectos Liderados</h3>
            <p class="text-3xl font-bold text-indigo-900 mt-2">
                {{ $data['projects_led_count'] ?? 'N/A' }}
            </p>
        </div>

        {{-- Metric: Critical Vulnerabilities in Leader's Projects --}}
        <div class="bg-red-100 p-6 rounded-lg shadow">
            <h3 class="text-lg font-medium text-red-800">Vulnerabilidades Críticas (Mis Proyectos)</h3>
            <p class="text-3xl font-bold text-red-900 mt-2">
                {{ $data['critical_vulnerabilities_in_projects_count'] ?? 'N/A' }}
            </p>
        </div>

        {{-- Metric: Accumulated Backlog --}}
        <div class="bg-orange-100 p-6 rounded-lg shadow"> {{-- Using orange for backlog --}}
            <h3 class="text-lg font-medium text-orange-800">Backlog Acumulado (Mis Proyectos)</h3>
            <p class="text-3xl font-bold text-orange-900 mt-2">
                {{ $data['accumulated_backlog_count'] ?? 'N/A' }}
            </p>
        </div>

        {{-- Metric: Open Treatment Gaps --}}
        <div class="bg-yellow-100 p-6 rounded-lg shadow">
            <h3 class="text-lg font-medium text-yellow-800">Brechas de Tratamiento Abiertas</h3>
            <p class="text-3xl font-bold text-yellow-900 mt-2">
                {{ $data['open_treatment_gaps_count'] ?? 'N/A' }}
            </p>
        </div>

        {{-- Metric: Monthly Closure Rate --}}
        <div class="bg-green-100 p-6 rounded-lg shadow">
            <h3 class="text-lg font-medium text-green-800">Tasa de Cierre Mensual (Vulnerabilidades)</h3>
            <p class="text-3xl font-bold text-green-900 mt-2">
                @if(isset($data['monthly_closure_rate']))
                    {{ number_format($data['monthly_closure_rate'] * 100, 2) }}%
                @else
                    N/A
                @endif
            </p>
        </div>

        {{-- Metric: Tasa de Remediación (Mis Proyectos Críticas/Altas) --}}
        <div class="bg-sky-100 p-6 rounded-lg shadow">
            <h3 class="text-lg font-medium text-sky-800">Tasa de Remediación (Mis Proyectos Críticas/Altas)</h3>
            <p class="text-3xl font-bold text-sky-900 mt-2">
                {{ isset($data['critical_high_remediation_rate_projects']) ? number_format($data['critical_high_remediation_rate_projects'], 1) . '%' : 'N/A' }}
            </p>
        </div>

        {{-- Metric: Remediación a Tiempo (Mis Proyectos %) --}}
        <div class="bg-teal-100 p-6 rounded-lg shadow">
            <h3 class="text-lg font-medium text-teal-800">Remediación a Tiempo (Mis Proyectos %)</h3>
            <p class="text-3xl font-bold text-teal-900 mt-2">
                {{ isset($data['on_time_remediation_percentage_projects']) ? number_format($data['on_time_remediation_percentage_projects'], 1) . '%' : 'N/A' }}
            </p>
        </div>
    </div>

    {{-- Further sections for lider dashboard --}}
    <div class="mt-8">
        <h3 class="text-xl font-semibold text-gray-700 mb-4">Mis Proyectos Recientes</h3>
        <p class="text-gray-600">Lista de proyectos o accesos directos...</p>
    </div>

    @if(!empty($data['lider_alerts']))
        <div class="mt-8 pt-6 border-t border-gray-200">
            <h3 class="text-xl font-semibold text-gray-700 mb-4">Alertas Importantes</h3>
            <div class="bg-gray-50 p-4 rounded-md shadow-inner space-y-3">
                @foreach($data['lider_alerts'] as $alert)
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
