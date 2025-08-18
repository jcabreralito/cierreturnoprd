@props(['type' => 1])

@if ($type == 1)
<td {{ $attributes->merge(['class' => 'p-2 border-b border-blue-gray-50 max-w-[200px]']) }}>
    <p class="block font-sans text-xxs antialiased font-normal leading-normal text-blue-gray-900">
        {{ $slot }}
    </p>
</td>
@else
<td {{ $attributes->merge(['class' => 'p-2 border-b border-blue-gray-50 max-w-[200px]']) }}>
    {{ $slot }}
</td>
@endif
