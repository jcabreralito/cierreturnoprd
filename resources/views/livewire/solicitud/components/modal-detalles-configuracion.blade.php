<x-dialog-modal wire:model="modalConfigJornada" maxWidth="5xl">
    <x-slot name="title">
        <h2 class="text-2xl font-semibold">
            Detalles de configuraciones predefinidas de jornadas
        </h2>
    </x-slot>

    <x-slot name="content">
        <div>
            <x-home.table.table :headers="[
                    [0 => 'Nombre', 1 => true, 2 => 'text-center', 3 => 'nombre'],
                    [0 => 'Lunes', 1 => true, 2 => 'text-center', 3 => 'lun'],
                    [0 => 'Martes', 1 => true, 2 => 'text-center', 3 => 'mar'],
                    [0 => 'Miércoles', 1 => true, 2 => 'text-center', 3 => 'mie'],
                    [0 => 'Jueves', 1 => true, 2 => 'text-center', 3 => 'jue'],
                    [0 => 'Viernes', 1 => true, 2 => 'text-center', 3 => 'vie'],
                    [0 => 'Sábado', 1 => true, 2 => 'text-center', 3 => 'sab'],
                    [0 => 'Domingo', 1 => true, 2 => 'text-center', 3 => 'dom'],
                ]" tblClass="tblNormal">
                @forelse ($diasConf as $diasConfiguracion)
                    <tr class="hover:bg-gray-100 transition-all duration-300" >
                        <x-home.table.td class="text-center">{{ $diasConfiguracion->nombre }}</x-home.table.td>
                        <x-home.table.td class="text-center">{{ $diasConfiguracion->lun }}</x-home.table.td>
                        <x-home.table.td class="text-center">{{ $diasConfiguracion->mar }}</x-home.table.td>
                        <x-home.table.td class="text-center">{{ $diasConfiguracion->mie }}</x-home.table.td>
                        <x-home.table.td class="text-center">{{ $diasConfiguracion->jue }}</x-home.table.td>
                        <x-home.table.td class="text-center">{{ $diasConfiguracion->vie }}</x-home.table.td>
                        <x-home.table.td class="text-center">{{ $diasConfiguracion->sab }}</x-home.table.td>
                        <x-home.table.td class="text-center">{{ $diasConfiguracion->dom }}</x-home.table.td>
                    </tr>
                @empty
                    <tr>
                        <x-home.table.td colspan="12" class="text-center">
                            <div class="flex justify-center flex-col items-center space-y-2">
                                <span>No se encontraron registros</span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><g fill="none" stroke="#484848" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"><path fill="#484848" d="M8.5 9a.5.5 0 1 1 0-1a.5.5 0 0 1 0 1m7 0a.5.5 0 1 1 0-1a.5.5 0 0 1 0 1"/><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2S2 6.477 2 12s4.477 10 10 10"/><path d="M7.5 15.5s1.5-2 4.5-2s4.5 2 4.5 2"/></g></svg>
                            </div>
                        </x-home.table.td>
                    </tr>
                @endforelse
            </x-home.table.table>
        </div>
    </x-slot>

    <x-slot name="footer">
        <div class="space-x-2">
            <x-secondary-button type="button" wire:click="$toggle('modalConfigJornada')" wire:loading.attr="disabled">
                Cerrar
            </x-secondary-button>
        </div>
    </x-slot>
</x-dialog-modal>
