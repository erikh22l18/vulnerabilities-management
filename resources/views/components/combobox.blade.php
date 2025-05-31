<div class="relative">
    <label for="{{ $name }}" class="block font-medium text-gray-700 mb-1">{{ $label }}</label>
    <input type="text" id="{{ $name }}_search" placeholder="Buscar..." class="w-full border border-gray-300 rounded px-2 py-1 mb-1 focus:ring focus:ring-blue-200">
    <select name="{{ $name }}[]" id="{{ $name }}" multiple class="w-full border border-gray-300 rounded px-2 py-1 focus:ring focus:ring-blue-200">
        @foreach($options as $option)
            <option value="{{ $option['id'] }}" {{ collect($selected)->contains($option['id']) ? 'selected' : '' }}>
                {{ $option['name'] }}
            </option>
        @endforeach
    </select>
    @error($name) <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
</div>