<div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 md:p-8">
    <h2 class="text-2xl font-semibold text-gray-800 mb-6">Dashboard de Líder de Proyecto</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {{-- Metric: Projects Led Count --}}
        <div class="bg-indigo-100 p-6 rounded-lg shadow">
            <h3 class="text-lg font-medium text-indigo-800">Proyectos Liderados</h3>
            <p class="text-3xl font-bold text-indigo-900 mt-2">
                {{ $data['projects_led_count'] ?? 'N/A' }}
            </p>
        </div>

        {{-- Add more lider-specific widgets/metrics here --}}
        <div class="bg-teal-100 p-6 rounded-lg shadow">
            <h3 class="text-lg font-medium text-teal-800">Vulnerabilidades Críticas (Mis Proyectos)</h3>
            <p class="text-3xl font-bold text-teal-900 mt-2">
                Próximamente
            </p>
        </div>

        <div class="bg-pink-100 p-6 rounded-lg shadow">
            <h3 class="text-lg font-medium text-pink-800">Tareas Pendientes (Mis Proyectos)</h3>
            <p class="text-3xl font-bold text-pink-900 mt-2">
                Próximamente
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
