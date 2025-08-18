
<x-home.table.table :headers="[
        [0 => '', 1 => false, 2 => '', 3 => ''],
        [0 => 'PERSONAL', 1 => false, 2 => '', 3 => 'personal'],
        [0 => 'NOMBRE', 1 => false, 2 => '', 3 => 'nombre'],
        [0 => 'HORAS SOLICITUD', 1 => false, 2 => 'text-center', 3 => 'numHorasSolicitudes'],
        [0 => 'HORAS REALES', 1 => false, 2 => 'text-center', 3 => 'numHorasReales'],
    ]">
    @forelse ($solicitudesPorPersonal as $solicitudItemP)
        <tr id="trPs-{{ $solicitudItemP->personal }}" class="hover:bg-gray-100 transition-all duration-300" >
            <x-home.table.td class="text-center w-20" onclick="toggleSubRowPs('{{ $solicitudItemP->personal }}')">
                <span>
                    <i class="icone fa-solid fa-circle-chevron-down text-xl text-gray-500 cursor-pointer"></i>
                </span>
            </x-home.table.td>
            <x-home.table.td>{{ $solicitudItemP->personal }}</x-home.table.td>
            <x-home.table.td>{{ $solicitudItemP->nombre }}</x-home.table.td>
            <x-home.table.td class="text-center">{{ $solicitudItemP->numHorasSolicitudes }}</x-home.table.td>
            <x-home.table.td class="text-center">{{ $solicitudItemP->numHorasReales }}</x-home.table.td>
        </tr>

        @if (!$inAction)
        <tr id="subtrPs-{{ $solicitudItemP->personal }}" class="hidden" wire:ignore>
            <x-home.table.td colspan="16">
                <div></div>
            </x-home.table.td>
        </tr>
        @endif
    @empty
        <tr>
            <x-home.table.td colspan="16" class="text-center">
                <div class="flex justify-center flex-col items-center space-y-2">
                    <span>No se encontraron registros</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><g fill="none" stroke="#484848" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"><path fill="#484848" d="M8.5 9a.5.5 0 1 1 0-1a.5.5 0 0 1 0 1m7 0a.5.5 0 1 1 0-1a.5.5 0 0 1 0 1"/><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2S2 6.477 2 12s4.477 10 10 10"/><path d="M7.5 15.5s1.5-2 4.5-2s4.5 2 4.5 2"/></g></svg>
                </div>
            </x-home.table.td>
        </tr>
    @endforelse
</x-home.table.table>

<div class="mt-4 flex justify-between items-center">
    <div class="w-full">
        <div class="w-1/12">
            <x-forms.select name="paginationF" labelText="Paginar" wire:model.live="paginationF">
                <option value="10">10</option>
                <option value="20">20</option>
                <option value="50">50</option>
                <option value="100">100</option>
                <option value="todos">Todos</option>
            </x-forms.select>
        </div>
    </div>

    @if ($paginationF != 'todos')
        <div class="w-full flex justify-end items-center">
            {{ $solicitudesPorPersonal->links() }}
        </div>
    @else
        <div class="w-full flex justify-end items-center">
            <span class="text-sm text-gray-700">Total de registros: {{ $solicitudesPorPersonal->count() }}</span>
        </div>
    @endif
</div>
