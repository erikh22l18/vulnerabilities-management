@props(['title' => __('Confirmar contraseña'), 'content' => __('Por tu seguridad, confirma tu contraseña para continuar.'), 'button' => __('Confirmar')])

@php
    $confirmableId = md5($attributes->wire('then'));
@endphp

<span
    {{ $attributes->wire('then') }}
    x-data
    x-ref="span"
    x-on:click="$wire.startConfirmingPassword('{{ $confirmableId }}')"
    x-on:password-confirmed.window="setTimeout(() => $event.detail.id === '{{ $confirmableId }}' && $refs.span.dispatchEvent(new CustomEvent('then', { bubbles: false })), 250);"
>
    {{ $slot }}
</span>

@once
<x-dialog-modal wire:model.live="confirmingPassword">
    <x-slot name="title">
        <span class="text-gray-800">{{ $title }}</span>
    </x-slot>

    <x-slot name="content">
        <p class="text-gray-600">{{ $content }}</p>

        <div class="mt-4" x-data="{}" x-on:confirming-password.window="setTimeout(() => $refs.confirmable_password.focus(), 250)">
            <x-input type="password" class="mt-1 block w-3/4 border-gray-300 rounded focus:ring focus:ring-blue-200" 
                    placeholder="{{ __('Contraseña') }}" autocomplete="current-password"
                    x-ref="confirmable_password"
                    wire:model="confirmablePassword"
                    wire:keydown.enter="confirmPassword" />

            <x-input-error for="confirmable_password" class="mt-2" />
        </div>
    </x-slot>

    <x-slot name="footer">
        <button type="button" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded shadow transition" 
                wire:click="stopConfirmingPassword" wire:loading.attr="disabled">
            {{ __('Cancelar') }}
        </button>

        <button type="button" class="ms-3 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow transition" 
                dusk="confirm-password-button" wire:click="confirmPassword" wire:loading.attr="disabled">
            {{ $button }}
        </button>
    </x-slot>
</x-dialog-modal>
@endonce