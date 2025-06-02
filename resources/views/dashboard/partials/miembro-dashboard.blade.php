<div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 md:p-8">
    <h2 class="text-2xl font-semibold text-gray-800 mb-6">Dashboard de Miembro de Proyecto</h2>

    {{-- Adjusted grid for a 2x2 layout on medium screens and up --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Metric: My Assigned Active Tasks --}}
        <div class="bg-purple-100 p-6 rounded-lg shadow">
            <h3 class="text-lg font-medium text-purple-800">Mis Tareas Activas Asignadas</h3>
            <p class="text-3xl font-bold text-purple-900 mt-2">
                {{ $data['my_active_assigned_tasks_count'] ?? 'N/A' }}
            </p>
        </div>

        {{-- Metric: Individual Productivity (Last Month) --}}
        <div class="bg-blue-100 p-6 rounded-lg shadow">
            <h3 class="text-lg font-medium text-blue-800">Productividad Individual (Últimos 30 días)</h3>
            <p class="text-3xl font-bold text-blue-900 mt-2">
                {{ $data['individual_productivity_last_month'] ?? 'N/A' }}
                <span class="text-sm font-normal text-blue-700">Tareas/Vulnerabilidades Cerradas</span>
            </p>
        </div>

        {{-- Metric: Resolution Time Compliance --}}
        <div class="bg-green-100 p-6 rounded-lg shadow">
            <h3 class="text-lg font-medium text-green-800">Cumplimiento de Plazos de Resolución</h3>
            <p class="text-3xl font-bold text-green-900 mt-2">
                @if(isset($data['resolution_time_compliance_percentage']))
                    {{ number_format($data['resolution_time_compliance_percentage'], 2) }}%
                @else
                    N/A
                @endif
            </p>
        </div>

        {{-- Metric: Assigned vs. Closed Ratio --}}
        <div class="bg-yellow-100 p-6 rounded-lg shadow">
            <h3 class="text-lg font-medium text-yellow-800">Ratio Asignadas vs. Cerradas (Global)</h3>
            <p class="text-3xl font-bold text-yellow-900 mt-2">
                @if(isset($data['assigned_vs_closed_ratio']))
                    {{ number_format($data['assigned_vs_closed_ratio'] * 100, 0) }}% {{-- Or display as a ratio like X:Y --}}
                    {{-- Example for ratio X:Y could be more complex if values are not pre-calculated for this display --}}
                @else
                    N/A
                @endif
            </p>
        </div>
    </div>

    {{-- Further sections for miembro dashboard --}}
    <div class="mt-8">
        <h3 class="text-xl font-semibold text-gray-700 mb-4">Accesos Rápidos</h3>
        <p class="text-gray-600">Enlaces a mis tareas, vulnerabilidades reportadas por mí, etc...</p>
    </div>

    @if(!empty($data['personal_alerts']))
        <div class="mt-8 pt-6 border-t border-gray-200">
            <h3 class="text-xl font-semibold text-gray-700 mb-4">Mis Alertas</h3>
            <div class="bg-gray-50 p-4 rounded-md shadow-inner space-y-3">
                @foreach($data['personal_alerts'] as $alert)
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
