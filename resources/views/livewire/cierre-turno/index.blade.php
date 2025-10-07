<div>
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8" x-data="{ showType: false }">
        {{--  Header  --}}
        <div class="flex justify-between items-center py-4">
            <h1 class="text-2xl font-semibold text-left text-gray-700 w-full">Cierre de turnos</h1>
        </div>

        <hr class="py-2">

        <div class="mb-4">
            <div class="flex items-start bg-[#E9E9E9] py-1 px-4 shadow-md rounded-md mb-5">
                <div class="grid grid-cols-1 md:gap-x-4 md:gap-y-0 gap-y-4 w-full {{ ($maquinas && count($maquinas) >= 2) ? 'md:grid-cols-5' : 'md:grid-cols-4' }}">
                    <div wire:ignore>
                        <x-filters.select name="operador" labelText="Operador" id="operador">
                            <option value="">Seleccione un operador</option>
                            @foreach ($operadores as $operadorItem)
                                <option value="{{ $operadorItem['label'] }}">{{ $operadorItem['label'] }}</option>
                            @endforeach
                        </x-filters.select>
                    </div>

                    @if ($maquinas && count($maquinas) >= 2)
                    <div>
                        <x-filters.select name="maquina" labelText="Máquina" id="maquina">
                            <option value="">Seleccione una máquina</option>
                            @foreach ($maquinas as $maquinaItem)
                                <option value="{{ $maquinaItem }}">{{ $maquinaItem }}</option>
                            @endforeach
                        </x-filters.select>
                    </div>
                    @endif

                    <div>
                        <x-filters.select name="turno" labelText="Turno" :isLive="true">
                            <option value="">Seleccione un turno</option>
                            <option value="1">Turno 1</option>
                            <option value="2">Turno 2</option>
                        </x-filters.select>
                    </div>

                    <div>
                        <x-filters.input name="fecha_cierre" labelText="Fecha de Cierre" type="date" :isLive="true" wire:change="operadorTrabajoEnVariasMaquinas"
                            :min="(auth()->user()->tipoUsuarioCierreTurno == 3 || auth()->user()->tipoUsuarioCierreTurno == 2) ? date('Y-m-d', strtotime('-1 days')) : date('Y-m-d', strtotime('-100 days'))" :max="date('Y-m-d', strtotime('+1 days'))"
                            />
                    </div>
                    @if (
                            ($turno != null && $turno != '') &&
                            ($fecha_cierre != null && $fecha_cierre != '') &&
                            ($operador != null && $operador != '')
                        )
                        <div class="h-full flex justify-center w-full items-center space-x-4">
                            <div class="tooltip">
                                <button wire:click="obtenerData()"
                                    class="text-xs py-2 px-4 bg-cyan-500 hover:bg-cyan-600 text-white rounded">
                                    Consultar reporte
                                </button>
                                <span class="tooltiptext">Consultar reporte</span>
                            </div>

                            @if ($yaRealizoCierre)
                                <div>
                                    <p class="text-xs text-amber-100 bg-amber-500 rounded-md shadow-md shadow-amber-700 py-1 px-2 text-xxs my-auto">Cierre concluido</p>
                                </div>
                            @endif

                            @if ($realizarCierre && !$yaRealizoCierre)
                                <div class="tooltip">
                                    <button wire:click="realizarCierreAccion" class="text-xs py-2 px-4 bg-green-500 hover:bg-green-600 text-white rounded">
                                        Realizar cierre
                                    </button>
                                    <span class="tooltiptext">Realizar cierre</span>
                                </div>
                            @endif

                            @if (count($list) > 0)
                            <div class="tooltip">
                                <button wire:click="generarPDF()"
                                    class="text-xs py-2 px-4 bg-blue-500 hover:bg-blue-600 text-white rounded">
                                    <i class="fa-solid fa-file-pdf"></i>
                                </button>
                                <span class="tooltiptext">Generar PDF</span>
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
                    [0 => 'DESCRIPCIÓN', 1 => true, 2 => '', 3 => 'observacion'],
                    [0 => 'PROCESO', 1 => true, 2 => '', 3 => 'proceso'],
                    [0 => 'CANTIDAD', 1 => true, 2 => 'text-center', 3 => 'Cantidad'],
                    [0 => 'TIEMPO', 1 => true, 2 => 'text-center', 3 => 'Tiempo'],
                    [0 => 'HORA INICIO', 1 => true, 2 => 'text-center', 3 => 'HoraInicio'],
                    [0 => 'HORA FIN', 1 => true, 2 => 'text-center', 3 => 'HoraFin'],
                    [0 => 'MAQUINA', 1 => true, 2 => '', 3 => 'Maquina'],
                    [0 => 'NOTAS', 1 => true, 2 => '', 3 => 'Notas'],
                ]" tblClass="tblNormal">
                    @forelse ($this->list as $item)
                        <tr class="hover:bg-gray-100 transition-all duration-300" wire:key="item-{{ $item->ID }}">
                            <x-home.table.td class="text-center">{{ $item->numOrden }}</x-home.table.td>
                            <x-home.table.td class="">{{ $item->NombreTrabajo }}</x-home.table.td>
                            <x-home.table.td class="">{{ $item->observacion }}</x-home.table.td>
                            <x-home.table.td class="">{{ $item->proceso }}</x-home.table.td>
                            <x-home.table.td class="text-center">
                                {{--  Validamos si el valor tiene decimales  --}}
                                {{ intval($item->Cantidad) == $item->Cantidad ? number_format($item->Cantidad, 0) : number_format($item->Cantidad, 2) }}
                            </x-home.table.td>
                            <x-home.table.td class="text-center">{{ number_format($item->Tiempo, 2) }}</x-home.table.td>
                            <x-home.table.td class="text-center">{{ Carbon\Carbon::parse($item->HoraInicio)->format('Y/m/d H:i') }}</x-home.table.td>
                            <x-home.table.td class="text-center">{{ Carbon\Carbon::parse($item->HoraFin)->format('Y/m/d H:i') }}</x-home.table.td>
                            <x-home.table.td class="">{{ $item->Maquina }}</x-home.table.td>
                            <x-home.table.td class="">{{ $item->Notas }}</x-home.table.td>
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
                    <p class="text-xs mt-2">
                        {{ count($this->list) }} registros encontrados
                    </p>
                </div>
            </div>

            @if (count($reporteActual) > 0)
                @include('livewire.cierre-turno.components.eficiencia')
            @endif
        @endif
    </div>

    {{--  Modal para el registro de una nueva capacitación  --}}
    @include('livewire.cierre-turno.components.modal-cierre-turno')
    @include('livewire.cierre-turno.components.modal-pdf-raw')
    @include('livewire.cierre-turno.components.loader')

    {{--  Configuracion e insercion de select2  --}}
    <div wire:ignore>
        <script>
            const role = @js(auth()->user()->tipoUsuarioCierreTurno);

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

                    if (role == 3) {
                        $('#operador').prop('disabled', true);
                    }
                }, 1000);
            }

            $('#operador').on('change', function(e) {
                var data = $(this).val();
                @this.set('operador', data);
                @this.call('operadorTrabajoEnVariasMaquinas')
            });

            document.addEventListener('confirmarCierre', (event) => {
                let operador = event.detail.operador;
                let supervisorOptions = '<option value="">Seleccione un supervisor</option>';
                window.supervisores.forEach(sup => {
                    supervisorOptions += `<option value="${sup.Id_Usuario}">${sup.nombre_completo}</option>`;
                });
                Swal.fire({
                    title: 'Confirmación',
                    text: '¿Está seguro de que desea realizar el cierre de turno?',
                    html: `
                        <form onsubmit="return false;">
                            <div>
                                <label for="supervisor" class="block font-medium text-gray-700">Supervisor:</label>
                                <select id="supervisor" class="form-control py-2 rounded-md shadow-md mt-1 w-full border-gray-200 focus:outline-none focus:ring-0 focus:border-gray-300">
                                    ${supervisorOptions}
                                </select>
                            </div>

                            <div class="mt-4">
                                <label for="passwordOperador" class="block font-medium text-gray-700">Contraseña Operador:</label>
                                <input type="password" id="passwordOperador" class="form-control py-2 rounded-md shadow-md mt-1 w-full border-gray-200 focus:outline-none focus:ring-0 focus:border-gray-300" />
                            </div>
                        </form>
                    `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, realizar cierre',
                    cancelButtonText: 'Cancelar',
                    reverseButtons: true,
                    preConfirm: () => {
                        const supervisor = Swal.getPopup().querySelector('#supervisor').value;
                        const passwordOperador = Swal.getPopup().querySelector('#passwordOperador').value;

                        if (!supervisor) {
                            Swal.showValidationMessage(`Por favor seleccione un supervisor`);
                            return;
                        }

                        if (!passwordOperador) {
                            Swal.showValidationMessage(`Por favor ingrese las contraseñas`);
                            return;
                        }

                        // Retorna una promesa para controlar el cierre
                        return fetch(serve + '/validate-user', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    },
                                    body: JSON.stringify({
                                        passwordOperador: passwordOperador,
                                        supervisor: supervisor,
                                        operador: operador,
                                    }),
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (!data.response) {
                                        Swal.showValidationMessage('Contraseña incorrecta');
                                        // Rechaza la promesa para mantener el modal abierto
                                        return Promise.reject();
                                    }
                                    // Si todo está bien, retorna los datos y el modal se cierra
                                    return { loginOperador: data.operador, passwordOperador: passwordOperador, supervisor: supervisor };
                                }).catch(() => {
                                    // No hace falta nada aquí, el modal sigue abierto
                                });
                    }
                }).then((result) => {
                    if (result.isConfirmed && result.value) {
                        @this.set('loginOperador', result.value.loginOperador);
                        @this.set('passwordOperador', result.value.passwordOperador);
                        @this.set('supervisor', result.value.supervisor);
                        @this.finalizarCierre();
                    } else {
                        @this.set('modalCreateCierreTurno', true);
                    }
                });
            });

            document.addEventListener('limpiarOperador', () => {
                $('#operador').val(null).trigger('change');
                initSelect2()
            });

            document.addEventListener('cargarSupervisores', (event) => {
                window.supervisores = event.detail.supervisores;
            });
        </script>
    </div>
</div>
