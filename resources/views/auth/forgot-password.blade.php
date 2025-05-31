<x-guest-layout>
    <div class="min-h-screen flex flex-col bg-gradient-to-br from-blue-50 via-white to-blue-100 sm:justify-center items-center pt-6 sm:pt-0">
        <div class="w-full sm:max-w-md px-6 py-8 bg-white shadow-xl rounded-lg">
            <div class="mb-6 flex justify-center">
                <svg class="h-12 w-12 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
            
            <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Recuperar contraseña</h2>

            <div class="mb-4 text-sm text-gray-600">
                ¿Olvidaste tu contraseña? No hay problema. Simplemente indícanos tu dirección de correo electrónico y te enviaremos un enlace para restablecer la contraseña.
            </div>

            @session('status')
                <div class="mb-4 font-medium text-sm text-blue-600">
                    {{ $value }}
                </div>
            @endsession

            <x-validation-errors class="mb-4" />

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="block">
                    <x-label for="email" value="Correo electrónico" class="text-gray-700" />
                    <x-input id="email" class="block mt-1 w-full border-gray-300 rounded focus:ring focus:ring-blue-200" 
                        type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                </div>

                <div class="flex items-center justify-end mt-4">
                    <a class="text-sm text-blue-600 hover:text-blue-800 mr-3" href="{{ route('login') }}">
                        Volver al inicio de sesión
                    </a>
                    
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-700 transition">
                        Enviar enlace
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>