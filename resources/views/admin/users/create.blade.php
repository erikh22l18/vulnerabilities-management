<x-app-layout>
    <div class="py-8 bg-gradient-to-br from-blue-50 via-white to-blue-100 min-h-screen">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-8">
                <h1 class="text-2xl font-semibold text-gray-800 mb-6">Registrar Usuario</h1>
                <form method="POST" action="{{ route('users.store') }}" class="space-y-6">
                    @csrf
                    <div>
                        <label for="name" class="block font-medium text-gray-700 mb-1">Nombres completos</label>
                        <input type="text" id="name" wire:model.defer="name" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200">
                        @error('name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="identification" class="block font-medium text-gray-700 mb-1">Identificación</label>
                        <input type="text" id="identification" wire:model.defer="identification" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200">
                        @error('identification') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="email" class="block font-medium text-gray-700 mb-1">Correo electrónico</label>
                        <input type="email" id="email" wire:model.defer="email" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200">
                        @error('email') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="area" class="block font-medium text-gray-700 mb-1">Área</label>
                        <input type="text" id="area" wire:model.defer="area" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200">
                        @error('area') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="organization_id" class="block font-medium text-gray-700 mb-1">Organización</label>
                        <select name="organization_id" class="w-full border border-gray-300 rounded px-3 py-2">
                            @foreach ($organizations as $org)
                            <option value="{{ $org->id }}" {{ old('organization_id') == $org->id ? 'selected' : '' }}>
                                {{ $org->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('organization_id')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label for="password" class="block font-medium text-gray-700 mb-1">Contraseña</label>
                        <input type="password" id="password" wire:model.defer="password" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200" required>
                        @error('password') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="password_confirmation" class="block font-medium text-gray-700 mb-1">Confirmar Contraseña</label>
                        <input type="password" id="password_confirmation" wire:model.defer="password_confirmation" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200" required>
                        @error('password_confirmation') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="role" class="block font-medium text-gray-700 mb-1">Rol</label>
                        <select id="role" wire:model.defer="role" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200">
                            <option value="">Seleccione un rol</option>
                            <option value="lider">Líder</option>
                            <option value="miembro">Miembro</option>
                        </select>
                        @error('role') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="bg-blue-700 hover:bg-blue-800 text-white px-6 py-2 rounded shadow transition">Registrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>