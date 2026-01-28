<div>
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8" x-data="{ showType: false }">
        {{--  Header  --}}
        <div class="flex justify-between items-center py-4">
            <h1 class="text-2xl font-semibold text-left text-gray-700 w-full">Historico</h1>
        </div>

        <hr class="py-2">

        <div class="mb-4">
            <div class="flex items-start bg-[#E9E9E9] py-1 px-4 shadow-md rounded-md mb-5">
                <div class="grid grid-cols-1 md:gap-x-4 md:gap-y-0 gap-y-4 w-full {{ auth()->user()->tipoUsuarioCierreTurno == 1 ? 'lg:grid-cols-7' : 'lg:grid-cols-6' }}">
                    <div wire:ignore class="{{ auth()->user()->tipoUsuarioCierreTurno == 1 || auth()->user()->tipoUsuarioCierreTurno == 2 ? '' : 'hidden' }}">
                        <x-filters.select name="operador" labelText="Operador" id="operador">
                            <option value="">Seleccione un operador</option>
                            @foreach ($operadores as $operadorItem)
                                <option value="{{ $operadorItem['value'] }}">{{ $operadorItem['label'] }}</option>
                            @endforeach
                        </x-filters.select>
                    </div>

                    <div wire:ignore class="{{ auth()->user()->tipoUsuarioCierreTurno == 1 || auth()->user()->tipoUsuarioCierreTurno == 3 ? '' : 'hidden' }}">
                        <x-filters.select name="supervisor" labelText="Supervisor" id="supervisor">
                            <option value="">Seleccione un supervisor</option>
                            @foreach ($supervisores as $supervisorItem)
                                <option value="{{ $supervisorItem->Id_Usuario }}">{{ $supervisorItem->nombre_completo }}</option>
                            @endforeach
                        </x-filters.select>
                    </div>

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
                        <div class="tooltip">
                            <button wire:click="obtenerData()"
                                class="text-xs py-2 px-4 bg-cyan-500 hover:bg-cyan-600 text-white rounded">
                                <i class="fa-solid fa-search"></i>
                            </button>
                            <span class="tooltiptext">Buscar</span>
                        </div>

                        @if ($filtroFolio != null || $filtroFechaCierreOperador != null || $filtroFechaCierreSupervisor != null || $operador != null || $supervisor != null)
                        <div class="tooltip">
                            <button wire:click="limpiarFiltros()"
                                class="ml-2 text-xs py-2 px-4 bg-red-500 hover:bg-red-600 text-white rounded">
                                <i class="fa-solid fa-filter-circle-xmark"></i>
                            </button>
                            <span class="tooltiptext">Limpiar filtros</span>
                        </div>
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
                [0 => 'OPERADOR', 1 => true, 2 => 'text-center', 3 => 'nombre_operador'],
                [0 => 'SUPERVISOR', 1 => true, 2 => 'text-center', 3 => 'nombre_supervisor'],
                [0 => 'ESTADO', 1 => true, 2 => 'text-center', 3 => 'estado'],
                [0 => 'FEC. CIERRE', 1 => true, 2 => '', 3 => 'fecha_cierre'],
                [0 => 'FEC. FIR. OP.', 1 => true, 2 => '', 3 => 'fecha_firma_operador'],
                [0 => 'FEC. FIR. SUP.', 1 => true, 2 => '', 3 => 'fecha_firma_supervisor'],
                [0 => 'ACCIONES', 1 => false, 2 => 'text-center', 3 => ''],
            ]" tblClass="tblNormal">
                @forelse ($cierres as $item)
                    <tr class="hover:bg-gray-100 transition-all duration-300" wire:key="item-{{ $item->id }}">
                        <x-home.table.td class="text-center">{{ $item->folio }}</x-home.table.td>
                        <x-home.table.td class="text-center">{{ $item->nombre_operador }}</x-home.table.td>
                        <x-home.table.td class="text-center">{{ $item->nombre_supervisor }}</x-home.table.td>
                        <x-home.table.td class="text-center">
                            @if ($item->estatus == 1)
                                <span class="px-2 inline-flex text-xxs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    {{ $item->nombre_accionado }}
                                </span>
                            @elseif ($item->estatus == 2)
                                <span class="px-2 inline-flex text-xxs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    {{ $item->nombre_accionado }}
                                </span>
                            @elseif ($item->estatus == 3)
                                <span class="px-2 inline-flex text-xxs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    {{ $item->nombre_accionado }}
                                </span>
                            @elseif ($item->estatus == 4)
                                <span class="px-2 inline-flex text-xxs leading-5 font-semibold rounded-full bg-amber-100 text-amber-800">
                                    {{ $item->nombre_accionado }}
                                </span>
                            @endif
                        </x-home.table.td>
                        <x-home.table.td class="">{{ ($item->fecha_cierre != null) ? Carbon\Carbon::parse($item->fecha_cierre)->format('Y/m/d') : 'Sin fecha' }}</x-home.table.td>
                        <x-home.table.td class="">{{ ($item->fecha_firma_operador != null) ? Carbon\Carbon::parse($item->fecha_firma_operador)->format('Y/m/d') : 'Sin fecha' }}</x-home.table.td>
                        <x-home.table.td class="">{{ ($item->fecha_firma_supervisor != null) ? Carbon\Carbon::parse($item->fecha_firma_supervisor)->format('Y/m/d') : 'Sin fecha' }}</x-home.table.td>
                        <x-home.table.td class="text-center space-x-2">
                            @if ($item->comentario != null && $item->comentario != '')
                            <div class="tooltip">
                                <button onclick="verComentarios('{{ $item->comentario }}')"
                                    class="text-xxs py-1 px-2 bg-gray-500 hover:bg-gray-600 text-white rounded">
                                    <i class="fa-solid fa-comment"></i>
                                </button>
                                <span class="tooltiptext">Ver comentarios</span>
                            </div>
                            @endif

                            <div class="tooltip">
                                <button wire:click="verDetalle('{{ $item->id }}')"
                                    class="text-xxs py-1 px-2 bg-blue-500 hover:bg-blue-600 text-white rounded">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                                <span class="tooltiptext">Ver detalle</span>
                            </div>

                            <div class="tooltip">
                                <button wire:click="verPdf('{{ $item->id }}')"
                                    class="text-xxs py-1 px-2 bg-purple-500 hover:bg-purple-600 text-white rounded">
                                    <i class="fa-solid fa-file-pdf"></i>
                                </button>
                                <span class="tooltiptext">Ver PDF</span>
                            </div>
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
                        {{ $cierres->links() }}
                    </div>
                @else
                    <div class="w-full flex justify-end items-center">
                        <span class="text-sm text-gray-700">Total de registros: {{ $cierres->count() }}</span>
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

        @include('livewire.cierre-turno.components.modal-pdf')
    @endif

    <div wire:ignore>
        <script>
            function realizarReCalculo(id) {
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "Esta acción recalculará el cierre seleccionado.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, recalcular',
                    cancelButtonText: 'Cancelar',
                    reverseButtons: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        @this.call('realizarReCalculo', id);
                    }
                });
            }

            document.addEventListener("DOMContentLoaded", function() {
                initSelect2();
            });

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

                    $('#supervisor').select2({
                        placeholder: 'Seleccione un supervisor',
                        allowClear: true,
                        width: '100%'
                    });
                }, 1000);
            }

            $('#operador').on('change', function(e) {
                var data = $(this).val();
                @this.set('operador', data);
                @this.call('obtenerData');
            });

            $('#supervisor').on('change', function(e) {
                var data = $(this).val();
                @this.set('supervisor', data);
                @this.call('obtenerData');
            });

            document.addEventListener('limpiarOperador', () => {
                $('#operador').val(null).trigger('change');
                initSelect2()
            });

            /**
            * Función para ver comentarios
            * @param {string} comentario - Comentario a mostrar
            * @returns {void}
            */
            function verComentarios(comentario) {
                Swal.fire({
                    title: 'Comentarios',
                    text: comentario || 'No hay comentarios disponibles.',
                    icon: 'info',
                    confirmButtonText: 'Cerrar'
                });
            }
        </script>
    </div>
</div>
