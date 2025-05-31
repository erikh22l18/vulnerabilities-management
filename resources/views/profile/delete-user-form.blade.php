<x-action-section>
    <x-slot name="title">
        {{ __('Eliminar cuenta') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Elimina permanentemente tu cuenta.') }}
    </x-slot>

    <x-slot name="content">
        <div class="max-w-xl text-sm text-gray-600">
            {{ __('Una vez que elimines tu cuenta, todos sus recursos y datos se eliminarán permanentemente. Antes de eliminar tu cuenta, descarga cualquier información que desees conservar.') }}
        </div>

        <div class="mt-5">
            <button type="button"
                class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded shadow transition"
                wire:click="confirmUserDeletion" wire:loading.attr="disabled">
                Eliminar cuenta
            </button>
        </div>

        <!-- Modal de confirmación para eliminar usuario -->
        <x-dialog-modal wire:model.live="confirmingUserDeletion">
            <x-slot name="title">
                {{ __('Eliminar cuenta') }}
            </x-slot>

            <x-slot name="content">
                {{ __('¿Estás seguro de que deseas eliminar tu cuenta? Una vez eliminada, todos sus recursos y datos se eliminarán permanentemente. Por favor, ingresa tu contraseña para confirmar que deseas eliminar tu cuenta de forma permanente.') }}

                <div class="mt-4" x-data="{}" x-on:confirming-delete-user.window="setTimeout(() => $refs.password.focus(), 250)">
                    <input type="password" class="mt-1 block w-3/4 border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200"
                        autocomplete="current-password"
                        placeholder="Contraseña"
                        x-ref="password"
                        wire:model="password"
                        wire:keydown.enter="deleteUser" />

                    <x-input-error for="password" class="mt-2" />
                </div>
            </x-slot>

            <x-slot name="footer">
                <button type="button"
                    class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-2 rounded shadow transition"
                    wire:click="$toggle('confirmingUserDeletion')" wire:loading.attr="disabled">
                    Cancelar
                </button>
                <button type="button"
                    class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded shadow transition ms-3"
                    wire:click="deleteUser" wire:loading.attr="disabled">
                    Eliminar cuenta
                </button>
            </x-slot>
        </x-dialog-modal>
    </x-slot>
</x-action-section>