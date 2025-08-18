@props(['isSortable' => true, 'classList' => '', 'tblname' => 'folio'])

<th class="p-2 border-b border-blue-gray-100 bg-[#535b61] asc hover:bg-[#6C757D] transition-all duration-300 {{ $isSortable ? 'sortFiltert cursor-pointer' : '' }} "
    @if ($isSortable != '') wire:click="sort('{{ $tblname }}')" @endif
>
    <div class="font-sans justify-between text-xxs antialiased leading-none text-white font-[400] flex items-center">
        <p class="w-full {{ $classList }}">
            {{ $slot }}
        </p>
        <div class="ml-3 {{ $isSortable ? '' : 'hidden' }}">
            <i class="fa-solid fa-sort"></i>
        </div>
    </div>
</th>
