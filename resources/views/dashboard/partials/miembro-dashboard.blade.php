<div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 md:p-8">
    <h2 class="text-2xl font-semibold text-gray-800 mb-6">Dashboard de Miembro de Proyecto</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {{-- Metric: My Assigned Active Tasks --}}
        <div class="bg-purple-100 p-6 rounded-lg shadow">
            <h3 class="text-lg font-medium text-purple-800">Mis Tareas Activas Asignadas</h3>
            <p class="text-3xl font-bold text-purple-900 mt-2">
                {{ $data['my_active_assigned_tasks_count'] ?? 'N/A' }}
            </p>
        </div>

        {{-- Add more miembro-specific widgets/metrics here --}}
        <div class="bg-lime-100 p-6 rounded-lg shadow">
            <h3 class="text-lg font-medium text-lime-800">Vulnerabilidades Asignadas</h3>
            <p class="text-3xl font-bold text-lime-900 mt-2">
                Próximamente
            </p>
        </div>

        <div class="bg-cyan-100 p-6 rounded-lg shadow">
            <h3 class="text-lg font-medium text-cyan-800">Proyectos en los que Participo</h3>
            <p class="text-3xl font-bold text-cyan-900 mt-2">
                Próximamente
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
