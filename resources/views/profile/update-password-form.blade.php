<x-form-section submit="updatePassword">
    <x-slot name="title">
        <span class="text-gray-800 font-bold">Actualizar contraseña</span>
    </x-slot>

    <x-slot name="description">
        <span class="text-gray-600">Asegúrate de usar una contraseña larga y aleatoria para mantener tu cuenta segura.</span>
    </x-slot>

    <x-slot name="form">
        <div class="col-span-6 sm:col-span-4">
            <x-label for="current_password" value="Contraseña actual" class="text-gray-700" />
            <x-input id="current_password" type="password" class="mt-1 block w-full border-gray-300 rounded focus:ring focus:ring-blue-200" 
                    wire:model="state.current_password" autocomplete="current-password" />
            <x-input-error for="current_password" class="mt-2" />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-label for="password" value="Nueva contraseña" class="text-gray-700" />
            <x-input id="password" type="password" class="mt-1 block w-full border-gray-300 rounded focus:ring focus:ring-blue-200" 
                    wire:model="state.password" autocomplete="new-password" />
            <x-input-error for="password" class="mt-2" />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-label for="password_confirmation" value="Confirmar nueva contraseña" class="text-gray-700" />
            <x-input id="password_confirmation" type="password" class="mt-1 block w-full border-gray-300 rounded focus:ring focus:ring-blue-200" 
                    wire:model="state.password_confirmation" autocomplete="new-password" />
            <x-input-error for="password_confirmation" class="mt-2" />
        </div>
    </x-slot>

    <x-slot name="actions">
        <x-action-message class="me-3 text-blue-600" on="saved">
            Guardado.
        </x-action-message>

        <x-button class="bg-blue-600 hover:bg-blue-700 border-blue-600">
            Guardar
        </x-button>
    </x-slot>
</x-form-section>