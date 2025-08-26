<div>
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8" x-data="{ showType: false }">
        {{--  Header  --}}
        <div class="flex justify-between items-center py-4">
            <h1 class="text-2xl font-semibold text-left text-gray-700 w-full">RE - Cierre de turnos</h1>
        </div>

        <hr class="py-2">

        <div class="mb-4">
            <div class="flex items-start bg-[#E9E9E9] py-1 px-4 shadow-md rounded-md mb-5">
                <div class="grid grid-cols-1 lg:grid-cols-4 md:gap-x-4 md:gap-y-0 gap-y-4 w-full">
                    <div wire:ignore>
                        <x-filters.select name="operador" labelText="Operador" id="operador">
                            <option value="">Seleccione un operador</option>
                            @foreach ($operadores as $operador)
                                <option value="{{ $operador['label'] }}">{{ $operador['label'] }}</option>
                            @endforeach
                        </x-filters.select>
                    </div>

                    <div>
                        <x-filters.select name="turno" labelText="Turno" :isLive="true">
                            <option value="">Seleccione un turno</option>
                            <option value="1">Turno 1</option>
                            <option value="2">Turno 2</option>
                        </x-filters.select>
                    </div>

                    <div>
                        <x-filters.input name="fecha_cierre" labelText="Fecha de Cierre" type="date" :isLive="true" />
                    </div>

                    @if (
                            ($turno != null && $turno != '') &&
                            ($fecha_cierre != null && $fecha_cierre != '')
                        )
                        <div class="h-full flex justify-center w-full items-center space-x-4">
                            <button wire:click="obtenerData()"
                                class="text-xs py-2 px-4 bg-cyan-500 hover:bg-cyan-600 text-white rounded">
                                Consultar reporte
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-full overflow-x-auto px-8 pb-6">
        {{--  <div>
            <x-home.table.table :headers="[
                [0 => 'N° ORDEN', 1 => false, 2 => 'text-center', 3 => ''],
                [0 => 'NOMBRE TRABAJO', 1 => false, 2 => 'text-center', 3 => ''],
                [0 => 'ID ACT', 1 => false, 2 => '', 3 => ''],
                [0 => 'DESCRIPCIÓN', 1 => false, 2 => 'text-center', 3 => ''],
                [0 => 'PROCESO', 1 => false, 2 => 'text-center', 3 => ''],
                [0 => 'CANTIDAD', 1 => false, 2 => 'text-center', 3 => ''],
                [0 => 'TURNO', 1 => false, 2 => 'text-center', 3 => ''],
                [0 => 'TIEMPO', 1 => false, 2 => 'text-center', 3 => ''],
                [0 => 'HORA INICIO', 1 => false, 2 => 'text-center', 3 => ''],
                [0 => 'HORA FIN', 1 => false, 2 => 'text-center', 3 => ''],
                [0 => 'FECHA PRODUCCIÓN', 1 => false, 2 => 'text-center', 3 => ''],
                [0 => 'OPERADOR', 1 => false, 2 => 'text-center', 3 => ''],
                [0 => 'MAQUINA', 1 => false, 2 => 'text-center', 3 => ''],
            ]" tblClass="tblNormal">
                @forelse ($this->cierresRealizados as $item)
                    <tr class="hover:bg-gray-100 transition-all duration-300" wire:key="item-{{ $item->folio }}">
                        <x-home.table.td class="text-center">{{ $item->numOrden }}</x-home.table.td>
                        <x-home.table.td class="text-center">{{ $item->NombreTrabajo }}</x-home.table.td>
                        <x-home.table.td class="text-center">{{ $item->idAct }}</x-home.table.td>
                        <x-home.table.td class="text-center">{{ $item->observacion }}</x-home.table.td>
                        <x-home.table.td class="text-center">{{ $item->proceso }}</x-home.table.td>
                        <x-home.table.td class="text-center">{{ number_format($item->Cantidad, 2) }}</x-home.table.td>
                        <x-home.table.td class="text-center">{{ $item->Turno }}</x-home.table.td>
                        <x-home.table.td class="text-center">{{ number_format($item->Tiempo, 2) }}</x-home.table.td>
                        <x-home.table.td class="text-center">{{ Carbon\Carbon::parse($item->HoraInicio)->format('Y/m/d H:i') }}</x-home.table.td>
                        <x-home.table.td class="text-center">{{ Carbon\Carbon::parse($item->HoraFin)->format('Y/m/d H:i') }}</x-home.table.td>
                        <x-home.table.td class="text-center">{{ Carbon\Carbon::parse($item->fechaproduccion)->format('Y/m/d') }}</x-home.table.td>
                        <x-home.table.td class="text-center">{{ $item->Empleado }}</x-home.table.td>
                        <x-home.table.td class="text-center">{{ $item->Maquina }}</x-home.table.td>
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
        </div>  --}}
    </div>

    {{--  Modal para el registro de una nueva capacitación  --}}
    @include('livewire.cierre-turno.components.loader')

    {{--  Configuracion e insercion de select2  --}}
    <div wire:ignore>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                initSelect2();
            });

            function initSelect2() {
                setTimeout(() => {
                    $('#operador').select2({
                        placeholder: 'Seleccione un operador',
                        allowClear: true,
                        width: '100%'
                    });

                    $('#operador').on('change', function(e) {
                        var data = $(this).val();
                        @this.set('operador', data);
                    });
                }, 1000);
            }
        </script>
    </div>
</div>
