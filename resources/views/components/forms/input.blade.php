<div>
    <x-label for="{{ $name }}" :value="$labelText" :is-required="$isRequired" />
    <input style="padding: 2px 5px;" {{ $attributes->merge(['class' => 'form-control py-2 text-xxs rounded-md shadow-md mt-1 w-full border-gray-200 focus:outline-none focus:ring-0 focus:border-gray-300']) }} type="{{ $type }}" name="{{ $name }}" id="{{ $name }}" wire:model.live="{{ $name }}"
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

    @if ($showErrors)
        @error($name)
            <span class="text-xxs text-red-500 pt-3">{{ $message }}</span>
        @enderror
    @endif
</div>
