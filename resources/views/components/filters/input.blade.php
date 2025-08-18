@props([
    'name' => '',
    'labelText' => '',
    'type' => 'text',
    'isRequired' => false,
    'showErrors' => false,
    'isDisabled' => false,
    'isLive' => false
])

<div class="py-2 w-full">
    <label class="text-xs text-gray-700 mb-1" for="{{ $name }}">{{ $labelText }}</label>
    <input {{ $attributes->merge(['class' => 'form-control block rounded-md shadow-md text-xs border-gray-200 focus:outline-none focus:ring-0 focus:border-gray-300 w-full disabled:opacity-50 disabled:cursor-not-allowed']) }} type="{{ $type }}" name="{{ $name }}" id="{{ $name }}"

    @if ($isLive)
        wire:model.live="{{ $name }}"
    @else
        wire:model="{{ $name }}"
    @endif

    @if ($isRequired)
    required
    @endif

    @if ($isDisabled)
        disabled
    @endif

    placeholder="Ingrese un valor para filtrar"
    >

    @if ($showErrors)
        @error($name)
            <span class="text-sm text-red-500 font-semibold">{{ $message }}</span>
        @enderror
    @endif
</div>
