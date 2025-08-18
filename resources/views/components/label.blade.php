@props(['value' => null, 'isRequired' => true])

<label {{ $attributes->merge(['class' => 'block font-medium text-xxs text-gray-700']) }}>
    {{ $value ?? $slot }} @if ($isRequired) <span class="text-red-500 text-xxs">*</span> @endif
</label>
