
<x-home.table.table :headers="[
        [0 => 'Año', 1 => true, 2 => 'text-center', 3 => 'año'],
        [0 => 'Numsemana', 1 => true, 2 => 'text-center', 3 => 'numsemana'],
        [0 => 'Departamento', 1 => true, 2 => '', 3 => 'departamento'],
        [0 => 'Numempleado', 1 => true, 2 => 'text-center', 3 => 'numempleado'],
        [0 => 'Empleado', 1 => true, 2 => '', 3 => 'empleado'],
        [0 => 'Gpojornada', 1 => true, 2 => '', 3 => 'gpoJornada'],
        [0 => 'hrsJornada', 1 => true, 2 => 'text-center', 3 => 'hrsJornada'],
        [0 => 'tiejornada', 1 => true, 2 => 'text-center', 3 => 'tiejornada'],
        [0 => 'hrsExtrasT', 1 => true, 2 => 'text-center', 3 => 'hrsExtraTOTAL'],
        [0 => 'hrsExtraReport', 1 => true, 2 => 'text-center', 3 => 'hrsExtraReport'],
        [0 => 'hrsModificadas', 1 => false, 2 => 'text-center', 3 => ''],
        [0 => 'hrsExtraPagar', 1 => false, 2 => 'text-center', 3 => 'hrsExtraPagar'],
    ]" tblClass="tblNormal">
    @forelse ($solicitudesRelacion as $solicitudItemR)
        <tr class="hover:bg-gray-100 transition-all duration-300" >
            <x-home.table.td class="text-center">{{ $solicitudItemR->año }}</x-home.table.td>
            <x-home.table.td class="text-center">{{ $solicitudItemR->numsemana }}</x-home.table.td>
            <x-home.table.td>{{ $solicitudItemR->departamento }}</x-home.table.td>
            <x-home.table.td class="text-center">{{ $solicitudItemR->numempleado }}</x-home.table.td>
            <x-home.table.td>{{ $solicitudItemR->nombrempleado }}</x-home.table.td>
            <x-home.table.td>{{ $solicitudItemR->gpoJornada }}</x-home.table.td>
            <x-home.table.td class="text-center">{{ number_format($solicitudItemR->hrsJornada, 2) }}</x-home.table.td>
            <x-home.table.td class="text-center">{{ number_format($solicitudItemR->tiejornada, 2) }}</x-home.table.td>
            <x-home.table.td class="text-center">{{ number_format(($solicitudItemR->hrsExtraTOTAL), 2) }}</x-home.table.td>
            <x-home.table.td class="text-center">{{ number_format($solicitudItemR->hrsExtraReport, 2) }}</x-home.table.td>
            <x-home.table.td class="text-center text-gray-700 space-x-3 flex justify-between items-center">
                <div>
                    <input type="number" class="text-center w-16 p-0 text-xxs border-none focus:outline-none focus:ring-0
                    {{ $solicitudItemR->horas_finales != null ? 'text-sky-500 bg-sky-200 font-semibold' : '' }}
                    "
                    value="{{ $solicitudItemR->horas_finales != null ? $solicitudItemR->horas_finales : 0 }}"
                    id="horas_finales_{{ $solicitudItemR->año }}_{{ $solicitudItemR->numsemana }}_{{ $solicitudItemR->numempleado }}"
                    onkeyup="if(event.keyCode == 13) addHrsFinales('{{ $solicitudItemR->año }}', '{{ $solicitudItemR->numsemana }}', '{{ $solicitudItemR->numempleado }}', this.value, {{ $solicitudItemR->horas_finales != null ? $solicitudItemR->horas_finales : $solicitudItemR->hrsExtraReport }})"
                    onchange="addHrsFinales('{{ $solicitudItemR->año }}', '{{ $solicitudItemR->numsemana }}', '{{ $solicitudItemR->numempleado }}', this.value, {{ $solicitudItemR->horas_finales != null ? $solicitudItemR->horas_finales : $solicitudItemR->hrsExtraReport }})">
                </div>
                <div>
                    @if ($solicitudItemR->idHorasFinales != null)
                        <button type="button"><i class="fa-solid fa-comments text-sky-500" onclick="showComment('{{ $solicitudItemR->idHorasFinales }}')"></i></button>
                    @else
                        <span class="ml-3"></span>
                    @endif
                </div>
            </x-home.table.td>
            <x-home.table.td class="text-center">{{ number_format(($solicitudItemR->horas_finales != null ? $solicitudItemR->horas_finales : $solicitudItemR->hrsExtraReport), 2) }}</x-home.table.td>
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
            {{ $solicitudesRelacion->links() }}
        </div>
    @else
        <div class="w-full flex justify-end items-center">
            <span class="text-sm text-gray-700">Total de registros: {{ $solicitudesRelacion->count() }}</span>
        </div>
    @endif
</div>

<script>
    serve = '{{ env('APP_ENV') != 'local' ? '/horasextralito' : env('APP_URL') }}'

    /**
    * Función para mostrar el modal de comentarios
    *
    * @param {string} año - Año de la solicitud
    * @param {string} numsemana - Número de semana de la solicitud
    * @param {string} numempleado - Número de empleado de la solicitud
    * @param {string} horas_finales - Horas finales de la solicitud
    * @param {string} pre - Valor previo de horas finales
    * @return {void}
    */
    function addHrsFinales(año, numsemana, numempleado, horas_finales, pre) {
        // Validamos que el pre y el nuevo valor sean diferentes
        if (horas_finales == pre) {
            return;
        }

        Swal.fire({
            title: 'Motivo de la modificación',
            text: "Ingresa el motivo de la modificación",
            input: 'textarea',
            inputPlaceholder: 'Motivo de la modificación',
            inputAttributes: {
                'aria-label': 'Motivo de la modificación'
            },
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, guardar',
            preConfirm: (value) => {
                if (!value) {
                    Swal.showValidationMessage('Por favor ingresa un motivo')
                }
                return value
            }
        }).then((result) => {
            if (result.isConfirmed) {
                @this.addHrsFinales(año, numsemana, numempleado, horas_finales, result.value);
            } else {
                // Restaurar el valor original del input
                const input = document.getElementById(`horas_finales_${año}_${numsemana}_${numempleado}`);

                if (input) {
                    input.value = pre;
                }
            }
        })
    }

    /**
    * Función para mostrar el ultimo comentario
    *
    * @param horas_finales_id - ID de la solicitud
    * @return {void}
    */
    function showComment(horas_finales_id) {

        showLoader()

        let id = horas_finales_id != null && horas_finales_id != '' ? horas_finales_id : 0;

        fetch(serve + `/getlastcomment/${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.response && data.hf != null) {
                    Swal.fire({
                        title: 'Comentarios',
                        text: data.hf.motivo,
                        icon: 'info',
                        confirmButtonText: 'Cerrar'
                    });
                } else {
                    Swal.fire({
                        title: 'Comentarios',
                        text: 'No hay comentarios',
                        icon: 'info',
                        confirmButtonText: 'Cerrar'
                    });
                }

                hideLoader()
            })
    }

    /**
    * Función para resetear el input de horas finales
    *
    * @param {string} año - Año de la solicitud
    * @param {string} numsemana - Número de semana de la solicitud
    * @param {string} numempleado - Número de empleado de la solicitud
    * @return {void}
    */
    document.addEventListener('resetInput', function (event) {
        const { año, numsemana, numempleado, pre } = event.detail;

        // Restaurar el valor original del input
        const input = document.getElementById(`horas_finales_${año}_${numsemana}_${numempleado}`);

        if (input) {
            input.value = 0;
        }
    });
</script>
