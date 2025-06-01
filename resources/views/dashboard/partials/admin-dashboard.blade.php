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

        {{-- Add more admin-specific widgets/metrics here --}}
        <div class="bg-yellow-100 p-6 rounded-lg shadow">
            <h3 class="text-lg font-medium text-yellow-800">Otra Métrica Admin</h3>
            <p class="text-3xl font-bold text-yellow-900 mt-2">
                Próximamente
            </p>
        </div>
    </div>

    {{-- Further sections for admin dashboard --}}
    <div class="mt-8">
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
