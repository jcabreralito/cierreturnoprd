<div>
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8" x-data="{ showType: false }">
        {{--  Header  --}}
        <div class="flex justify-between items-center py-4">
            <h1 class="text-2xl font-semibold text-left text-gray-700 w-full">Reporte de producción</h1>
        </div>

        <hr class="py-2">

        <div class="mb-4">
            <div class="flex items-start bg-[#E9E9E9] py-1 px-4 shadow-md rounded-md mb-5">
                <div class="grid grid-cols-1 md:gap-x-4 md:gap-y-0 gap-y-4 w-full md:grid-cols-6">
                    <div wire:ignore>
                        {{--  Tipo de reporte  --}}
                        <x-filters.select name="tipo_reporte" labelText="Tipo de Reporte" :isLive="true" onchange="initSelect2()">
                            <option value="">Seleccione un tipo de reporte</option>
                            <option value="Operador">Operador</option>
                            <option value="Maquina">Máquina</option>
                            <option value="Grupo">Grupo</option>
                        </x-filters.select>
                    </div>

                    @if ($tipo_reporte == 'Operador')
                    <div wire:ignore>
                        <x-filters.select name="operador" labelText="Operador">
                            <option value="">Seleccione un operador</option>
                            @foreach ($operadores as $operadorItem)
                                <option value="{{ $operadorItem['value'] }}">{{ $operadorItem['label'] }}</option>
                            @endforeach
                        </x-filters.select>
                    </div>
                    @endif

                    @if ($tipo_reporte == 'Maquina')
                    <div wire:ignore>
                        <x-filters.select name="maquina" labelText="Máquina">
                            <option value="">Seleccione una máquina</option>
                            @foreach ($maquinas as $maquinaItem)
                                <option value="{{ $maquinaItem['value'] }}">{{ $maquinaItem['label'] }}</option>
                            @endforeach
                        </x-filters.select>
                    </div>
                    @endif

                    @if ($tipo_reporte == 'Grupo')
                    <div>
                        <x-filters.select name="grupo" labelText="Grupo">
                            <option value="">Seleccione un grupo</option>
                            @foreach ($grupos as $grupoItem)
                                <option value="{{ $grupoItem['value'] }}">{{ $grupoItem['label'] }}</option>
                            @endforeach
                        </x-filters.select>
                    </div>
                    @endif

                    <div>
                        <x-filters.select name="turno" labelText="Turno" :isLive="true">
                            <option value="">Seleccione un turno</option>
                            <option value="3">Todos</option>
                            <option value="1">Turno 1</option>
                            <option value="2">Turno 2</option>
                        </x-filters.select>
                    </div>

                    <div>
                        <x-filters.input name="fecha_desde" labelText="Fecha Inicio" type="date" :isLive="true" />
                    </div>

                    <div>
                        <x-filters.input name="fecha_hasta" labelText="Fecha Fin" type="date" :isLive="true" />
                    </div>
                    @if (
                            ($turno != null && $turno != '') &&
                            ($fecha_desde != null && $fecha_desde != '') &&
                            ($fecha_hasta != null && $fecha_hasta != '') &&
                            ($operador != null && $operador != '' || $maquina != null && $maquina != '' || $tipo_reporte == 'Grupo') &&
                            ($tipo_reporte != null && $tipo_reporte != '')
                        )
                        <div class="h-full flex justify-center w-full items-center space-x-4">
                            <div class="tooltip">
                                <button wire:click="obtenerData()"
                                    class="text-xs py-2 px-4 bg-cyan-500 hover:bg-cyan-600 text-white rounded">
                                    <i class="fas fa-search"></i>
                                </button>
                                <span class="tooltiptext">Consultar reporte</span>
                            </div>

                            @if (count($list) > 0)
                            <div class="tooltip">
                                <button wire:click="generarPDF()"
                                    class="text-xs py-2 px-4 bg-cyan-500 hover:bg-cyan-600 text-white rounded">
                                    <i class="fas fa-print"></i>
                                </button>

                                <span class="tooltiptext">Imprimir reporte</span>
                            </div>

                            <div class="tooltip">
                                <button wire:click="generarExcel()"
                                    class="text-xs py-2 px-4 bg-emerald-500 hover:bg-emerald-600 text-white rounded">
                                    <i class="fa-solid fa-file-excel"></i>
                                </button>
                                <span class="tooltiptext">Generar Excel</span>
                            </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-full overflow-x-auto px-8 pb-6">
        @if ($sinResultados)
            <div class="text-center py-4 text-lg font-semibold text-gray-700">
                <span>No se encontraron resultados</span>
            </div>
        @endif

        @if (count($list) > 0)
            <div>
                <x-home.table.table :headers="[
                    [0 => 'N° ORDEN', 1 => true, 2 => 'text-center', 3 => 'numOrden'],
                    [0 => 'NOMBRE TRABAJO', 1 => true, 2 => '', 3 => 'NombreTrabajo'],
                    [0 => 'ID ACT', 1 => true, 2 => '', 3 => 'idAct'],
                    [0 => 'DESCRIPCIÓN', 1 => true, 2 => '', 3 => 'observacion'],
                    [0 => 'PROCESO', 1 => true, 2 => '', 3 => 'proceso'],
                    [0 => 'CANTIDAD', 1 => true, 2 => 'text-center', 3 => 'Cantidad'],
                    [0 => 'TURNO', 1 => true, 2 => 'text-center', 3 => 'Turno'],
                    [0 => 'TIEMPO (HRS)', 1 => true, 2 => 'text-center', 3 => 'Tiempo'],
                    [0 => 'FECHA INICIO', 1 => true, 2 => 'text-center', 3 => 'HoraInicio'],
                    [0 => 'FECHA FIN', 1 => true, 2 => 'text-center', 3 => 'HoraFin'],
                    [0 => 'FECHA PRODUCCIÓN', 1 => true, 2 => 'text-center', 3 => 'fechaproduccion'],
                    [0 => 'OPERADOR', 1 => true, 2 => '', 3 => 'Empleado'],
                    [0 => 'MAQUINA', 1 => true, 2 => '', 3 => 'Maquina'],
                ]" tblClass="tblNormal">
                    @forelse ($this->list as $item)
                        <tr class="hover:bg-gray-100 transition-all duration-300" wire:key="item-{{ $item->idAct }}">
                            <x-home.table.td class="text-center">{{ $item->numOrden }}</x-home.table.td>
                            <x-home.table.td class="">{{ $item->NombreTrabajo }}</x-home.table.td>
                            <x-home.table.td class="text-center">{{ $item->idAct }}</x-home.table.td>
                            <x-home.table.td class="">{{ $item->observacion }}</x-home.table.td>
                            <x-home.table.td class="">{{ $item->proceso }}</x-home.table.td>
                            <x-home.table.td class="text-center">
                                {{--  Validamos si el valor tiene decimales  --}}
                                {{ intval($item->Cantidad) == $item->Cantidad ? number_format($item->Cantidad, 0) : number_format($item->Cantidad, 2) }}
                            </x-home.table.td>
                            <x-home.table.td class="text-center">{{ $item->Turno }}</x-home.table.td>
                            <x-home.table.td class="text-center">{{ number_format($item->Tiempo, 2) }}</x-home.table.td>
                            <x-home.table.td class="text-center">{{ Carbon\Carbon::parse($item->HoraInicio)->format('Y/m/d H:i') }}</x-home.table.td>
                            <x-home.table.td class="text-center">{{ Carbon\Carbon::parse($item->HoraFin)->format('Y/m/d H:i') }}</x-home.table.td>
                            <x-home.table.td class="text-center">{{ Carbon\Carbon::parse($item->fechaproduccion)->format('Y/m/d H:i') }}</x-home.table.td>
                            <x-home.table.td class="">{{ $item->Empleado }}</x-home.table.td>
                            <x-home.table.td class="">{{ $item->Maquina }}</x-home.table.td>
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

                <div>
                    <span class="text-gray-700 text-xs mt-2">
                        Total de registros: <strong>{{ count($list) }}</strong>
                    </span>
                </div>
            </div>

            @if (count($reporteActual) > 0)
                @include('livewire.cierre-turno.components.eficiencia')
            @endif

            @include('livewire.cierre-turno.components.modal-pdf-raw')
        @endif
    </div>

    {{--  Configuracion e insercion de select2  --}}
    <div>
        <script>
            document.addEventListener("livewire:navigated", function() {
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

                    $('#maquina').select2({
                        placeholder: 'Seleccione una máquina',
                        allowClear: true,
                        width: '100%'
                    });

                    $('#maquina').on('change', function(e) {
                        var data = $(this).val();
                        @this.set('maquina', data);
                    });
                }, 500);
            }

            document.addEventListener('showPdf', function(event) {
                let serve = '{{ env('APP_ENV') != 'local' ? '/cierreturno' : env('APP_URL') }}';

                // Obtenemos los datos a enviar
                const dataToSend = event.detail.data;
                const url = serve + '/reporte-produccion/pdf'; // Cambia esto por la URL correcta de tu ruta
                // Hacemos la petición
                fetch(url, {
                    method: 'POST', // Cambiamos el método a POST
                    headers: {
                        'Content-Type': 'application/json', // Indicamos que enviamos JSON
                        'X-CSRF-TOKEN': '{{ csrf_token() }}', // Incluimos el token CSRF para Laravel
                    },
                    body: JSON.stringify(dataToSend), // Convertimos los datos a JSON
                })
                .then(response => response.blob())
                .then(blob => {
                    const pdfUrl = URL.createObjectURL(blob);
                    window.open(pdfUrl, '_blank');
                });
            });
        </script>
    </div>

    @include('livewire.cierre-turno.components.loader')
</div>

