<div class="py-2">
    <x-label for="{{ $name }}">{{ $labelText }}</x-label>
    <textarea {{ $attributes->merge(['class' => 'form-control py-2 text-xxs rounded-md shadow-md mt-1 w-full border-gray-200 focus:outline-none focus:ring-0 focus:border-gray-300']) }} name="{{ $name }}" id="{{ $name }}" wire:model.live="{{ $name }}"
    @if ($isRequired)
    required
    @endif
    ></textarea>

    @if ($showErrors)
        @error($name)
            <span class="text-xxs text-red-500">{{ $message }}</span>
        @enderror
    @endif
</div>
