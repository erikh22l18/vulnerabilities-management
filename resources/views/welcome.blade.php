<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistema de Gestión de Vulnerabilidades</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Styles -->
    <style>
        [x-cloak] { display: none !important; }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased text-gray-800 bg-gradient-to-br from-blue-50 via-white to-blue-100 min-h-screen">
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center">
                        <svg class="h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        <span class="ml-2 text-xl font-semibold">SecureVulnManager</span>
                    </div>
                </div>
                <div class="flex items-center">
                    @if (Route::has('login'))
                        <div class="space-x-4">
                            @auth
                                <a href="{{ url('/dashboard') }}" class="text-blue-600 hover:text-blue-800 font-medium">Dashboard</a>
                            @else
                                <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800 font-medium">Iniciar sesión</a>

                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 transition">Registrarse</a>
                                @endif
                            @endauth
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </header>

    <main>
        <!-- Hero Section -->
        <div class="py-12 md:py-20 px-4">
            <div class="max-w-6xl mx-auto">
                <div class="md:flex md:items-center">
                    <div class="md:w-1/2 md:pr-12">
                        <h1 class="text-4xl md:text-5xl font-bold text-gray-900 leading-tight">
                            Gestione sus vulnerabilidades de forma efectiva
                        </h1>
                        <p class="mt-4 text-xl text-gray-600">
                            Una plataforma centralizada para detectar, asignar y remediar vulnerabilidades de seguridad en su organización.
                        </p>
                        <div class="mt-8 flex flex-col sm:flex-row gap-4">
                            <a href="{{ route('login') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg shadow hover:bg-blue-700 transition text-center font-medium">
                                Comenzar ahora
                            </a>
                            <a href="#features" class="bg-white text-blue-600 border border-blue-600 px-6 py-3 rounded-lg hover:bg-blue-50 transition text-center font-medium">
                                Conocer más
                            </a>
                        </div>
                    </div>
                    <div class="md:w-1/2 mt-10 md:mt-0">
                        <img src="{{ asset('images/home/security.png') }}" alt="Seguridad" class="w-5/6 rounded-lg shadow-xl">
                    </div>
                </div>
            </div>
        </div>

        <!-- Features -->
        <div id="features" class="py-12 bg-white">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <h2 class="text-3xl font-bold text-gray-900">Características principales</h2>
                    <p class="mt-4 text-lg text-gray-600 max-w-2xl mx-auto">
                        Nuestra plataforma simplifica todo el ciclo de vida de la gestión de vulnerabilidades.
                    </p>
                </div>

                <div class="mt-12 grid md:grid-cols-3 gap-8">
                    <div class="bg-blue-50 p-6 rounded-lg">
                        <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                        <h3 class="mt-4 text-xl font-semibold text-gray-900">Detección y Registro</h3>
                        <p class="mt-2 text-gray-600">Registre vulnerabilidades con información detallada, prioridades y datos técnicos.</p>
                    </div>

                    <div class="bg-blue-50 p-6 rounded-lg">
                        <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <h3 class="mt-4 text-xl font-semibold text-gray-900">Asignación y Seguimiento</h3>
                        <p class="mt-2 text-gray-600">Asigne responsables, establezca plazos y realice seguimiento del progreso.</p>
                    </div>

                    <div class="bg-blue-50 p-6 rounded-lg">
                        <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <h3 class="mt-4 text-xl font-semibold text-gray-900">Reportes y Métricas</h3>
                        <p class="mt-2 text-gray-600">Obtenga informes detallados y visualice métricas para medir el progreso.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="py-12 bg-blue-600">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-3xl font-bold text-white">Comience a gestionar sus vulnerabilidades hoy</h2>
                <p class="mt-4 text-xl text-blue-100">
                    Mejore la seguridad de su organización con nuestra plataforma integral.
                </p>
                <div class="mt-8">
                    <a href="{{ route('login') }}" class="bg-white text-blue-600 px-6 py-3 rounded-lg shadow hover:bg-blue-50 transition font-medium">
                        Iniciar sesión
                    </a>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-gray-800 text-white py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="md:flex md:justify-between">
                <div>
                    <div class="flex items-center">
                        <svg class="h-6 w-6 text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        <span class="ml-2 text-lg font-semibold">SecureVulnManager</span>
                    </div>
                    <p class="mt-2 text-sm text-gray-400">
                        &copy; {{ date('Y') }} Todos los derechos reservados.
                    </p>
                </div>
                <div class="mt-8 md:mt-0">
                    <h3 class="text-sm font-semibold tracking-wider uppercase">Enlaces</h3>
                    <div class="mt-4 space-y-2">
                        <a href="#" class="text-gray-400 hover:text-white block">Acerca de</a>
                        <a href="#" class="text-gray-400 hover:text-white block">Características</a>
                        <a href="#" class="text-gray-400 hover:text-white block">Contacto</a>
                        <a href="#" class="text-gray-400 hover:text-white block">Política de privacidad</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>