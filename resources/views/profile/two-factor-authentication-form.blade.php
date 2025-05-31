<x-action-section>
    <x-slot name="title">
        <span class="text-gray-800 font-bold">Autenticación en dos pasos</span>
    </x-slot>

    <x-slot name="description">
        <span class="text-gray-600">Añade seguridad adicional a tu cuenta usando autenticación en dos pasos.</span>
    </x-slot>

    <x-slot name="content">
        <h3 class="text-lg font-medium text-gray-900">
            @if ($this->enabled)
                @if ($showingConfirmation)
                    Finaliza la activación de la autenticación en dos pasos.
                @else
                    Has activado la autenticación en dos pasos.
                @endif
            @else
                No has activado la autenticación en dos pasos.
            @endif
        </h3>

        <div class="mt-3 max-w-xl text-sm text-gray-600">
            <p>
                Cuando la autenticación en dos pasos está activada, se te solicitará un token seguro y aleatorio durante el inicio de sesión. Puedes obtener este token desde la aplicación Google Authenticator de tu teléfono.
            </p>
        </div>

        @if ($this->enabled)
            @if ($showingQrCode)
                <div class="mt-4 max-w-xl text-sm text-gray-600">
                    <p class="font-semibold">
                        @if ($showingConfirmation)
                            Para finalizar la activación, escanea el siguiente código QR con tu aplicación de autenticación o ingresa la clave de configuración y proporciona el código OTP generado.
                        @else
                            La autenticación en dos pasos está activada. Escanea el siguiente código QR con tu aplicación de autenticación o ingresa la clave de configuración.
                        @endif
                    </p>
                </div>

                <div class="mt-4 p-3 inline-block bg-white border border-gray-200 rounded-lg shadow-sm">
                    {!! $this->user->twoFactorQrCodeSvg() !!}
                </div>

                <div class="mt-4 max-w-xl text-sm text-gray-600">
                    <p class="font-semibold">
                        Clave de configuración: <span class="text-blue-600 font-mono bg-blue-50 px-2 py-1 rounded">{{ decrypt($this->user->two_factor_secret) }}</span>
                    </p>
                </div>

                @if ($showingConfirmation)
                    <div class="mt-4">
                        <x-label for="code" value="Código" class="text-gray-700"/>

                        <x-input id="code" type="text" name="code" class="block mt-1 w-1/2 border-gray-300 rounded focus:ring focus:ring-blue-200" inputmode="numeric" autofocus autocomplete="one-time-code"
                            wire:model="code"
                            wire:keydown.enter="confirmTwoFactorAuthentication" />

                        <x-input-error for="code" class="mt-2" />
                    </div>
                @endif
            @endif

            @if ($showingRecoveryCodes)
                <div class="mt-4 max-w-xl text-sm text-gray-600">
                    <p class="font-semibold">
                        Guarda estos códigos de recuperación en un gestor de contraseñas seguro. Puedes usarlos para recuperar el acceso a tu cuenta si pierdes tu dispositivo de autenticación en dos pasos.
                    </p>
                </div>

                <div class="grid gap-1 max-w-xl mt-4 px-4 py-4 font-mono text-sm bg-blue-50 border border-blue-100 rounded-lg">
                    @foreach (json_decode(decrypt($this->user->two_factor_recovery_codes), true) as $code)
                        <div class="py-1">{{ $code }}</div>
                    @endforeach
                </div>
            @endif
        @endif

        <div class="mt-5 flex flex-wrap gap-2">
            @if (! $this->enabled)
                <x-confirms-password wire:then="enableTwoFactorAuthentication">
                    <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded shadow transition" wire:loading.attr="disabled">
                        Activar
                    </button>
                </x-confirms-password>
            @else
                @if ($showingRecoveryCodes)
                    <x-confirms-password wire:then="regenerateRecoveryCodes">
                        <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded shadow transition me-3" wire:loading.attr="disabled">
                            Regenerar códigos de recuperación
                        </button>
                    </x-confirms-password>
                @elseif ($showingConfirmation)
                    <x-confirms-password wire:then="confirmTwoFactorAuthentication">
                        <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded shadow transition me-3" wire:loading.attr="disabled">
                            Confirmar
                        </button>
                    </x-confirms-password>
                @else
                    <x-confirms-password wire:then="showRecoveryCodes">
                        <button type="button" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-2 rounded shadow transition me-3" wire:loading.attr="disabled">
                            Mostrar códigos de recuperación
                        </button>
                    </x-confirms-password>
                @endif

                @if ($showingConfirmation)
                    <x-confirms-password wire:then="disableTwoFactorAuthentication">
                        <button type="button" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-2 rounded shadow transition" wire:loading.attr="disabled">
                            Cancelar
                        </button>
                    </x-confirms-password>
                @else
                    <x-confirms-password wire:then="disableTwoFactorAuthentication">
                        <button type="button" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded shadow transition" wire:loading.attr="disabled">
                            Desactivar
                        </button>
                    </x-confirms-password>
                @endif
            @endif
        </div>
    </x-slot>
</x-action-section>