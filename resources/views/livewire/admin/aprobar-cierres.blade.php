<div>
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8" x-data="{ showType: false }">
        {{--  Header  --}}
        <div class="flex justify-between items-center py-4">
            <h1 class="text-2xl font-semibold text-left text-gray-700 w-full">Aprobar cierres de turno</h1>
        </div>

        <hr class="py-2">

        <div class="mb-4">
            <div class="flex items-start bg-[#E9E9E9] py-1 px-4 shadow-md rounded-md mb-5">
                <div class="grid grid-cols-1 lg:grid-cols-4 md:gap-x-4 md:gap-y-0 gap-y-4 w-full">
                    <div>
                        <x-filters.input name="filtroFolio" labelText="Folio" type="text" :isLive="false" wire:keydown.enter="obtenerData"/>
                    </div>

                    <div>
                        <x-filters.input name="filtroFechaCierreOperador" labelText="Fecha de Cierre Operador" type="date" :isLive="true" />
                    </div>

                    <div>
                        <x-filters.input name="filtroFechaCierreSupervisor" labelText="Fecha de Cierre Supervisor" type="date" :isLive="true" />
                    </div>

                    <div class="h-full flex justify-center w-full items-center">
                        <button wire:click="obtenerData()"
                            class="text-xs py-2 px-4 bg-cyan-500 hover:bg-cyan-600 text-white rounded">
                            <i class="fa-solid fa-search"></i>
                        </button>

                        @if ($filtroFolio != null || $filtroFechaCierreOperador != null || $filtroFechaCierreSupervisor != null)
                            <button wire:click="limpiarFiltros()"
                                class="ml-2 text-xs py-2 px-4 bg-red-500 hover:bg-red-600 text-white rounded">
                                <i class="fa-solid fa-filter-circle-xmark"></i>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-full overflow-x-auto px-8 pb-6">
        <div>
            <x-home.table.table :headers="[
                [0 => 'FOLIO', 1 => true, 2 => 'text-center', 3 => 'folio'],
                [0 => 'ESTADO', 1 => true, 2 => 'text-center', 3 => 'estado'],
                [0 => 'FEC. CIERRE', 1 => true, 2 => '', 3 => 'fecha_cierre'],
                [0 => 'FEC. FIR. OP.', 1 => true, 2 => '', 3 => 'fecha_firma_operador'],
                [0 => 'FEC. FIR. SUP.', 1 => true, 2 => '', 3 => 'fecha_firma_supervisor'],
                [0 => 'ACCIONES', 1 => false, 2 => 'text-center', 3 => ''],
            ]" tblClass="tblNormal">
                @forelse ($reportesRealizados as $item)
                    <tr class="hover:bg-gray-100 transition-all duration-300" wire:key="item-{{ $item->id }}">
                        <x-home.table.td class="text-center">{{ $item->folio }}</x-home.table.td>
                        <x-home.table.td class="text-center">
                            @if ($item->estatus == 1)
                                <span class="px-2 inline-flex text-xxs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    {{ $item->estatus_nombre }}
                                </span>
                            @elseif ($item->estatus == 2)
                                <span class="px-2 inline-flex text-xxs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    {{ $item->estaus_nombre }}
                                </span>
                            @elseif ($item->estatus == 3)
                                <span class="px-2 inline-flex text-xxs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    {{ $item->estaus_nombre }}
                                </span>
                            @elseif ($item->estatus == 4)
                                <span class="px-2 inline-flex text-xxs leading-5 font-semibold rounded-full bg-amber-100 text-amber-800">
                                    {{ $item->estaus_nombre }}
                                </span>
                            @endif
                        </x-home.table.td>
                        <x-home.table.td class="">{{ ($item->fecha_cierre != null) ? Carbon\Carbon::parse($item->fecha_cierre)->format('Y/m/d') : 'Sin fecha' }}</x-home.table.td>
                        <x-home.table.td class="">{{ ($item->fecha_firma_operador != null) ? Carbon\Carbon::parse($item->fecha_firma_operador)->format('Y/m/d') : 'Sin fecha' }}</x-home.table.td>
                        <x-home.table.td class="">{{ ($item->fecha_firma_supervisor != null) ? Carbon\Carbon::parse($item->fecha_firma_supervisor)->format('Y/m/d') : 'Sin fecha' }}</x-home.table.td>
                        <x-home.table.td class="text-center">
                            <button wire:click="verDetalle('{{ $item->id }}')"
                                class="text-xxs py-1 px-2 bg-blue-500 hover:bg-blue-600 text-white rounded">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </x-home.table.td>
                    </tr>
                @empty
                    <tr>
                        <x-home.table.td colspan="10" class="text-center">
                            <div class="flex justify-center flex-col items-center space-y-2">
                                <span>No se encontraron registros</span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24">
                                    <g fill="none" stroke="#484848" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="1.5">
                                        <path fill="#484848"
                                            d="M8.5 9a.5.5 0 1 1 0-1a.5.5 0 0 1 0 1m7 0a.5.5 0 1 1 0-1a.5.5 0 0 1 0 1" />
                                        <path
                                            d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2S2 6.477 2 12s4.477 10 10 10" />
                                        <path d="M7.5 15.5s1.5-2 4.5-2s4.5 2 4.5 2" />
                                    </g>
                                </svg>
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
                        {{ $reportesRealizados->links() }}
                    </div>
                @else
                    <div class="w-full flex justify-end items-center">
                        <span class="text-sm text-gray-700">Total de registros: {{ $reportesRealizados->count() }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{--  Modal para el registro de una nueva capacitación  --}}
    @include('livewire.cierre-turno.components.loader')

    @if ($reporte)
        {{--  Modal para el registro de una nueva capacitación  --}}
        @include('livewire.cierre-turno.components.modal-detalle-cierre')
    @endif
</div>
