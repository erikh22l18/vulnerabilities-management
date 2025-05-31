<x-guest-layout>
    <div class="min-h-screen flex flex-col bg-gradient-to-br from-blue-50 via-white to-blue-100 sm:justify-center items-center pt-6 sm:pt-0">
        <div class="w-full sm:max-w-md px-6 py-8 bg-white shadow-xl rounded-lg">
            <div class="mb-6 flex justify-center">
                <svg class="h-12 w-12 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
            
            <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Verificar correo electrónico</h2>

            <div class="mb-4 text-sm text-gray-600">
                Antes de continuar, ¿podrías verificar tu dirección de correo electrónico haciendo clic en el enlace que acabamos de enviarte? Si no recibiste el correo, con gusto te enviaremos otro.
            </div>

            @if (session('status') == 'verification-link-sent')
                <div class="mb-4 font-medium text-sm text-blue-600">
                    Se ha enviado un nuevo enlace de verificación a la dirección de correo electrónico que proporcionaste en la configuración de tu perfil.
                </div>
            @endif

            <div class="mt-4 flex items-center justify-between">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf

                    <div>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-700 transition">
                            Reenviar email de verificación
                        </button>
                    </div>
                </form>

                <div class="flex space-x-3">
                    <a
                        href="{{ route('profile.show') }}"
                        class="text-sm text-blue-600 hover:text-blue-800"
                    >
                        Editar perfil</a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <button type="submit" class="text-sm text-blue-600 hover:text-blue-800">
                            Cerrar sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>