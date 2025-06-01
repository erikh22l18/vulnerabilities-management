<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard') }}
            </h2>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                {{ now()->format('d/m/Y') }}
            </span>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-8">
                    <div class="flex items-center mb-6">
                        <svg class="w-10 h-10 text-blue-500 mr-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c1.657 0 3-1.343 3-3S13.657 2 12 2 9 3.343 9 5s1.343 3 3 3zm0 2c-2.21 0-4 1.79-4 4v5h8v-5c0-2.21-1.79-4-4-4z" />
                        </svg>
                        <div>
                            <h3 class="text-lg font-bold text-gray-700">¡Bienvenido/a, {{ Auth::user()->name }}!</h3>
                            <p class="text-gray-500 text-sm">Gestiona vulnerabilidades y usuarios desde este panel.</p>
                        </div>
                    </div>

                    {{-- Role-Specific Dashboard Section --}}
                    <div class="mt-8">
                        @if(isset($dashboard_type) && isset($service_data))
                            @if($dashboard_type === 'admin')
                                @include('dashboard.partials.admin-dashboard', ['data' => $service_data])
                            @elseif($dashboard_type === 'lider')
                                @include('dashboard.partials.lider-dashboard', ['data' => $service_data])
                            @elseif($dashboard_type === 'miembro')
                                @include('dashboard.partials.miembro-dashboard', ['data' => $service_data])
                            @elseif($dashboard_type === 'default')
                                {{-- Default content if user has no specific dashboard role but is authenticated --}}
                                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 md:p-8">
                                    <h2 class="text-2xl font-semibold text-gray-800 mb-6">Dashboard General</h2>
                                    <p>Bienvenido al panel general. Seleccione una opción del menú para comenzar.</p>
                                    {{-- You might include some very generic info here or leave it minimal --}}
                                </div>
                            @else
                                <p class="text-red-500">Error: Tipo de dashboard desconocido.</p>
                            @endif
                        @else
                            <p class="text-red-500">Error: No se pudo cargar la información del dashboard.</p> {{-- Fallback if dashboard_type or service_data isn't set --}}
                        @endif
                    </div>

                    {{-- Existing generic links and summary can remain below or be removed/integrated into role dashboards later --}}
                    {{-- For now, keeping them to avoid breaking existing view structure completely --}}
                    <div class="mt-8 border-t pt-8"> {{-- Added border and padding for separation --}}
                        <h2 class="text-xl font-bold text-gray-700 mb-4">Accesos Rápidos Generales</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <a href="{{ route('vulnerabilities.index') }}" class="p-6 bg-blue-50 rounded-lg shadow flex items-center hover:bg-blue-100 transition">
                                <svg class="w-8 h-8 text-blue-400 mr-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2a4 4 0 014-4h4m0 0V7a4 4 0 00-4-4H7a4 4 0 00-4 4v10a4 4 0 004 4h4" />
                            </svg>
                            <div>
                                <div class="font-semibold text-blue-700">Gestión de Vulnerabilidades</div>
                                <div class="text-sm text-blue-600">Revisa, edita y crea nuevas vulnerabilidades.</div>
                            </div>
                        </a>
                        @unlessrole('miembro')
                        <a href="{{ route('organizations.index') }}" class="p-6 bg-purple-50 rounded-lg shadow flex items-center hover:bg-purple-100 transition">
                            <svg class="w-8 h-8 text-purple-400 mr-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V7M16 3v4M8 3v4m-5 4h18" />
                            </svg>
                            <div>
                                <div class="font-semibold text-purple-700">Gestión de Organizaciones</div>
                                <div class="text-sm text-purple-600">Administra las organizaciones registradas.</div>
                            </div>
                        </a>
                        @endunlessrole
                        <a href="{{ route('projects.index') }}" class="p-6 bg-yellow-50 rounded-lg shadow flex items-center hover:bg-yellow-100 transition">
                            <svg class="w-8 h-8 text-yellow-400 mr-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                            <div>
                                <div class="font-semibold text-yellow-700">Gestión de Proyectos</div>
                                <div class="text-sm text-yellow-600">Administra los proyectos y su información.</div>
                            </div>
                        </a>
                        @unlessrole('miembro')
                        <a href="{{ route('admin.users.index') }}" class="p-6 bg-green-50 rounded-lg shadow flex items-center hover:bg-green-100 transition">
                            <svg class="w-8 h-8 text-green-400 mr-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6 0A4 4 0 0012 4a4 4 0 00-1 7.87" />
                            </svg>
                            <div>
                                <div class="font-semibold text-green-700">Gestión de Usuarios</div>
                                <div class="text-sm text-green-600">Administra los usuarios y sus roles.</div>
                            </div>
                        </a>
                        @endunlessrole
                    </div>

                    {{-- The x-welcome component might be part of a very generic dashboard, or removed if role dashboards are primary --}}
                    {{-- For now, let's comment it out if the role dashboards are meant to be the main content --}}
                    {{-- <x-welcome /> --}}

                    <div class="mt-10"> {{-- This was the original "Resumen de Estado de Vulnerabilidades" section --}}
                        {{-- This content might be duplicative if admin dashboard shows similar global stats --}}
                        {{-- Consider if this section is still needed globally or if its elements move into specific role dashboards --}}
                        {{-- For now, it will remain, potentially showing below the role-specific dashboard partial --}}
                        <h2 class="text-xl font-bold text-gray-700 mb-4">Resumen Global (Datos Anteriores)</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="bg-purple-50 p-6 rounded-lg shadow">
                                <h3 class="font-semibold text-purple-700 mb-2">Proyectos por Organización</h3>
                                <ul class="text-gray-700 text-sm">
                                    @foreach($orgs as $org)
                                    <li class="flex justify-between items-center border-b py-1">
                                        <div>
                                            <span>{{ $org->name }}</span>
                                            <a href="{{ route('organizations.projects.index', $org) }}"
                                                class="ml-2 inline-block px-2 py-0.5 bg-yellow-500 text-white text-xs rounded hover:bg-yellow-600 transition">
                                                Ver
                                            </a>
                                        </div>
                                        <span class="font-bold">{{ $org->projects_count }}</span>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="bg-blue-50 p-6 rounded-lg shadow">
                                <h3 class="font-semibold text-blue-700 mb-2">Vulnerabilidades por Proyecto</h3>
                                <ul class="text-gray-700 text-sm">
                                    @foreach($projects as $project)
                                    <li class="mb-2">
                                        <div class="flex justify-between items-center">
                                            <div>
                                                <span>{{ $project->name }}</span>
                                                <a href="{{ route('projects.vulnerabilities.index', $project) }}"
                                                    class="ml-2 inline-block px-2 py-0.5 bg-red-500 text-white text-xs rounded hover:bg-red-600 transition">
                                                    Ver
                                                </a>
                                            </div>
                                            <span>
                                                {{ $project->vulnerabilities_count }} total
                                                @if($project->vulnerabilities_count > 0)
                                                <span class="ml-2 text-xs text-green-700">
                                                    {{ $project->treatment_percentage }}% tratadas
                                                </span>
                                                @else
                                                <span class="ml-2 text-xs text-gray-400">Sin vulnerabilidades</span>
                                                @endif
                                            </span>
                                        </div>
                                        @if($project->vulnerabilities_count > 0)
                                        <div class="w-full bg-gray-200 rounded h-2 mt-1">
                                            <div class="bg-green-400 h-2 rounded" style="width: {{ $project->treatment_percentage }}%"></div>
                                        </div>
                                        @endif
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>