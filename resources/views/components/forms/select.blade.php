<div class="w-full">
    @if($hasEtiqueta)
        <x-label for="{{ $name }}" :value="$labelText" />
    @endif

    <select style="padding: 2px 5px;" {{ $attributes->merge(['class' => 'form-control text-xxs rounded-md shadow-md mt-1 py-2 w-full border-gray-200 focus:outline-none focus:ring-0 focus:border-gray-300']) }} name="{{ $name }}" id="{{ $name }}"

        @if ($hasWireModel)
        wire:model.live="{{ $name }}"
        @endif

        @if ($isRequired)
        required
        @endif

        @if ($isDisabled)
        disabled
        @endif

        @if ($isReadOnly)
        readonly
        @endif
        >
        {{ $slot }}
    </select>

    @if ($showErrors)
        @error($name)
            <span class="text-xxs text-red-500 pt-3">{{ $message }}</span>
        @enderror
    @endif
</div>
