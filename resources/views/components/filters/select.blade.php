@props([
    'name' => '',
    'labelText' => '',
    'type' => 'text',
    'isRequired' => false,
    'showErrors' => false,
])

<div class="py-2">
    <label class="text-xs text-gray-700 mb-1" for="{{ $name }}">{{ $labelText }}</label>
    <select {{ $attributes->merge(['class' => 'form-control w-full rounded-md shadow-md text-xs border-gray-200 focus:outline-none focus:ring-0 focus:border-gray-300']) }} name="{{ $name }}" id="{{ $name }}" wire:model="{{ $name }}"
        @if ($isRequired)
        required
        @endif

        x-data="{ selected: @entangle($name)}" @change="selected = $event.target.value" :class="{ 'bg-yellow-200': selected }"
        >
        {{ $slot }}
    </select>

    @if ($showErrors)
        @error($name)
            <span class="text-sm text-red-500 font-semibold">{{ $message }}</span>
        @enderror
    @endif
</div>
