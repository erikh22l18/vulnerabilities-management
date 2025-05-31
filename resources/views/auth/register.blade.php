<x-guest-layout>
    <div class="min-h-screen flex flex-col bg-gradient-to-br from-blue-50 via-white to-blue-100 sm:justify-center items-center pt-6 sm:pt-0">
        <div class="w-full sm:max-w-md px-6 py-8 bg-white shadow-xl rounded-lg">
            <div class="mb-6 flex justify-center">
                <svg class="h-12 w-12 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
            
            <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Crear cuenta</h2>

            <x-validation-errors class="mb-4" />

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div>
                    <x-label for="name" value="Nombre" class="text-gray-700" />
                    <x-input id="name" class="block mt-1 w-full border-gray-300 rounded focus:ring focus:ring-blue-200" 
                        type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                </div>

                <div class="mt-4">
                    <x-label for="email" value="Correo electrónico" class="text-gray-700" />
                    <x-input id="email" class="block mt-1 w-full border-gray-300 rounded focus:ring focus:ring-blue-200" 
                        type="email" name="email" :value="old('email')" required autocomplete="username" />
                </div>

                <div class="mt-4">
                    <x-label for="password" value="Contraseña" class="text-gray-700" />
                    <x-input id="password" class="block mt-1 w-full border-gray-300 rounded focus:ring focus:ring-blue-200" 
                        type="password" name="password" required autocomplete="new-password" />
                </div>

                <div class="mt-4">
                    <x-label for="password_confirmation" value="Confirmar contraseña" class="text-gray-700" />
                    <x-input id="password_confirmation" class="block mt-1 w-full border-gray-300 rounded focus:ring focus:ring-blue-200" 
                        type="password" name="password_confirmation" required autocomplete="new-password" />
                </div>

                @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                    <div class="mt-4">
                        <x-label for="terms">
                            <div class="flex items-center">
                                <x-checkbox name="terms" id="terms" class="text-blue-600" required />

                                <div class="ms-2 text-sm text-gray-600">
                                    {!! __('Acepto los :terms_of_service y la :privacy_policy', [
                                            'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="text-blue-600 hover:text-blue-800">Términos de Servicio</a>',
                                            'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="text-blue-600 hover:text-blue-800">Política de Privacidad</a>',
                                    ]) !!}
                                </div>
                            </div>
                        </x-label>
                    </div>
                @endif

                <div class="flex items-center justify-end mt-4">
                    <a class="text-sm text-blue-600 hover:text-blue-800" href="{{ route('login') }}">
                        ¿Ya tienes cuenta?
                    </a>

                    <button type="submit" class="ms-4 px-4 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-700 transition">
                        Registrarse
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>