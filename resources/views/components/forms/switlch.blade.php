@props(['id' => null, 'name' => 'toggle', 'checked' => false, 'isDisabled' => false, 'change' => '', 'color' => 'sky'])

<div class="flex items-center justify-center" {{ $attributes }} id="div{{ $id }}">
    <label class="relative inline-flex items-center h-4 w-8 {{ $isDisabled ? '' : 'cursor-pointer' }}" for="{{ $id }}">
        <input type="checkbox"
            id="{{ $id }}"
            name="{{ $name }}"
            class="sr-only peer"
            {{ $checked ? 'checked' : '' }}
            {{ $isDisabled ? 'disabled aria-disabled=true' : '' }}
            @if ($change) wire:change="{{ $change }}" @endif
        >
        <span
            class="absolute inset-0 rounded-full transition-colors duration-200 ease-in
                peer-focus:ring-0
                {{ $isDisabled ? 'bg-gray-100 border border-gray-300 cursor-not-allowed peer-checked:bg-sky-300' : 'bg-gray-400 peer-checked:bg-sky-500 peer-checked:border-sky-500' }}">
        </span>
        <span
            class="border inline-block w-4 h-4 bg-white rounded-full shadow transform transition-transform duration-200 ease-in
                peer-checked:translate-x-4
                peer-checked:border-sky-600
                {{ $isDisabled ? 'border-gray-300 cursor-not-allowed' : 'border-gray-100' }}">
        </span>
    </label>
</div>
