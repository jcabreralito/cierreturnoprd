
<x-home.table.table :headers="[
        [0 => '', 1 => false, 2 => '', 3 => ''],
        $mode == 1 || $modeAction == 1 ? [0 => '', 1 => false, 2 => 'text-center', 3 => ''] : null,
        [0 => 'FOLIO', 1 => true, 2 => 'text-center', 3 => 'folio'],
        [0 => 'USUARIO REG.', 1 => true, 2 => '', 3 => 'usuario'],
        [0 => 'DEPTO.', 1 => true, 2 => '', 3 => 'departamento'],
        [0 => 'RECURSO', 1 => true, 2 => '', 3 => 'maquina'],
        [0 => 'DESDE DIA', 1 => true, 2 => '', 3 => 'desde_dia'],
        [0 => 'SEMANA', 1 => true, 2 => '', 3 => 'num_semana'],
        [0 => 'TURNO', 1 => true, 2 => '', 3 => 'turno'],
        [0 => 'HORAS', 1 => true, 2 => 'text-center', 3 => 'horas'],
        [0 => 'N°. MAX. EMPL.', 1 => true, 2 => 'text-center', 3 => 'num_max_usuarios'],
        [0 => 'PERS. ASIG.', 1 => true, 2 => 'text-center', 3 => 'totalPersonalesRelacionados'],
        [0 => 'OP', 1 => true, 2 => '', 3 => 'op'],
        [0 => 'MOTIVO', 1 => true, 2 => '', 3 => 'motivo'],
        [0 => 'OBSERVACIONES', 1 => true, 2 => '', 3 => 'observaciones'],
        [0 => 'ESTADO', 1 => true, 2 => '', 3 => 'estatus'],
        [0 => 'ACCIONES', 1 => false, 2 => '', 3 => ''],
    ]">
    @forelse ($solicitudes as $solicitudItem)
        <tr id="tr-{{ $solicitudItem->idSolicitud }}" class="hover:bg-gray-100 transition-all duration-300" >
            <x-home.table.td class="text-center" onclick="toggleSubRow('{{ $solicitudItem->idSolicitud }}')">
                <span>
                    <i class="icone fa-solid fa-circle-chevron-down text-xl text-gray-500 cursor-pointer"></i>
                </span>
            </x-home.table.td>
            @if ($mode == 1 || $modeAction == 1)
            <x-home.table.td class="text-center">
                @if ($tipoAccion == 1)
                    @if($solicitudItem->totalPersonalesRelacionados > 0 || !in_array(2, $permisos) || $solicitudItem->estatus_id == 3 || $solicitudItem->estatus_id == 4 || $solicitudItem->cerrada == 1)
                    <input type="checkbox" name="solicitudes[]" value="{{ $solicitudItem->idSolicitud }}" disabled class="cursor-not-allowed bg-blue-200 ">
                    @else
                    <input type="checkbox" name="solicitudes[]" value="{{ $solicitudItem->idSolicitud }}" wire:model="solicitudesSeleccionadas" class="focus:ring-0 focus:outline-none">
                    @endif
                @else
                    @if(in_array(8, $permisos) && ((($tipoDistinccion == 1 && $solicitudItem->estatus_id == 1) || ($tipoDistinccion == 2 && $solicitudItem->estatus_id == 6) || ($tipoDistinccion == 3 && $solicitudItem->estatus_id == 4)) && ((($role == 3 && $solicitudItem->estatus_id == 6) || ($role == 1 && $solicitudItem->estatus_id == 6) || ($role == 2 && $solicitudItem->estatus_id == 4)) || ((in_array($role, [1,2,5]) && $solicitudItem->estatus_id == 4 || in_array($role, [1,3,5]) && $solicitudItem->estatus_id == 1)) || $role == 3 && $solicitudItem->estatus_id == 1) && (($solicitudItem->totalPersonalesAutorizados == $solicitudItem->totalPersonalesRelacionados && $solicitudItem->totalPersonalesRelacionados > 0) || $solicitudItem->totalPersonalesRelacionados == 0)) && ($solicitudItem->cerrada != 1))
                    <input type="checkbox" name="solicitudesPf[]" value="{{ $solicitudItem->idSolicitud }}" wire:model="solicitudesPorFinalizar" class="focus:ring-0 focus:outline-none">
                    @else
                    <input type="checkbox" name="solicitudesPf[]" value="{{ $solicitudItem->idSolicitud }}" disabled class="cursor-not-allowed bg-blue-200">
                    @endif
                @endif
            </x-home.table.td>
            @endif
            <x-home.table.td class="text-center">{{ $solicitudItem->folio }}</x-home.table.td>
            <x-home.table.td>{{ $solicitudItem->usuario }}</x-home.table.td>
            <x-home.table.td>{{ $solicitudItem->departamento }}</x-home.table.td>
            <x-home.table.td>{{ $solicitudItem->maquina }}</x-home.table.td>
            <x-home.table.td>{{ $solicitudItem->desde_dia }}</x-home.table.td>
            <x-home.table.td>{{ $solicitudItem->semana }}</x-home.table.td>
            <x-home.table.td>{{ $solicitudItem->turno }}</x-home.table.td>
            <x-home.table.td class="text-center">{{ number_format($solicitudItem->horas, 1) }}</x-home.table.td>
            <x-home.table.td class="text-center">
                {{ $solicitudItem->num_max_usuarios }}
            </x-home.table.td>

            <x-home.table.td class="text-center">
                @if ($solicitudItem->totalPersonalesRelacionados == 0)
                <p class="text-red-500 font-semibold">{{ $solicitudItem->totalPersonalesRelacionados }}</p>
                @elseif ($solicitudItem->totalPersonalesAutorizados == 0)
                <p class="text-yellow-600 font-semibold">{{ $solicitudItem->totalPersonalesRelacionados }}</p>
                @elseif ($solicitudItem->totalPersonalesAutorizados != 0)
                <p class="text-green-500 font-semibold">{{ $solicitudItem->totalPersonalesRelacionados }}</p>
                @endif
            </x-home.table.td>

            <x-home.table.td>
                <span class="text-xs text-sky-500 cursor-pointer" onclick="getOps({{ $solicitudItem->idSolicitud }})">Mostrar ops ({{ $solicitudItem->totalOpsRelacionados }})</span>
            </x-home.table.td>
            <x-home.table.td>{{ $solicitudItem->motivo }}</x-home.table.td>
            <x-home.table.td>{{ $solicitudItem->observaciones }}</x-home.table.td>
            <x-home.table.td>
                <div class="flex justify-center items-center space-x-2 w-full">

                    @if(($mode != 1) &&
                        in_array(4, $permisos) &&
                        (($solicitudItem->estatus_id != 3 && $solicitudItem->estatus_id != 9) ||
                        ($solicitudItem->estatus_id == 3 && $role == 1)) &&
                        !($solicitudItem->estatus_id == 1 && $solicitudItem->tipo == 1 && $role == 2) &&
                        !($solicitudItem->estatus_id == 6 && $solicitudItem->tipo == 2 && $role == 2) &&
                        !($solicitudItem->cerrada == 1)
                    )
                        <select name="estatusF"
                            id="estatusF-{{ $solicitudItem->idSolicitud }}"
                        @if ($modeAction == 1)
                            disabled
                        @endif
                        style="padding: 2px 5px;" class="form-control text-xxs rounded-md shadow-md mt-1 py-2 w-full border-gray-200 focus:outline-none focus:ring-0 focus:border-gray-300" onchange="changeEstatus('{{ $solicitudItem->idSolicitud }}', this, '{{ $solicitudItem->estatus_id }}')">
                            <option value="null" disabled >Selec. una opción</option>
                            @include('livewire.solicitud.components.options-filter')
                        </select>
                    @else
                        @if($solicitudItem->estatus_id == 3 || $solicitudItem->estatus_id == 9)
                            <div class="bg-green-300 rounded-full text-center py-1 px-2">
                                {{ $solicitudItem->estatus }}
                            </div>
                        @elseif($solicitudItem->estatus_id == 6)
                            <div class="bg-yellow-300 rounded-full text-center py-1 px-2">
                                {{ $solicitudItem->estatus }}
                            </div>
                        @elseif($solicitudItem->estatus_id == 4)
                            <div class="bg-blue-300 rounded-full text-center py-1 px-2">
                                {{ $solicitudItem->estatus }}
                            </div>
                        @else
                            <div class="bg-gray-300 rounded-full text-center py-1 px-2">
                                {{ $solicitudItem->estatus }}
                            </div>
                        @endif
                    @endif
                </div>
            </x-home.table.td>
            <x-home.table.td class="text-center">
                <div class="flex space-x-3">
                    {{--  admin gnr, jefes de area, programador  --}}
                    @if ($solicitudItem->estatus_id != 3 && $solicitudItem->estatus_id != 9 && $solicitudItem->estatus_id != 4 && $mode != 1 && $modeAction != 1 && $solicitudItem->cerrada != 1)
                        @if (in_array(3, $permisos))
                        <div class="tooltip">
                            <div class="flex justify-center items-center space-x-2">
                                <button type="button" wire:click="openModalShowPersonal('{{ $solicitudItem->idSolicitud }}')" class="text-xs text-white bg-blue-500 px-2 py-1 rounded-md hover:bg-blue-600">
                                    <i class="fa-solid fa-user-plus"></i>
                                </button>
                            </div>
                            <span class="tooltiptext">Agregar personal</span>
                        </div>
                        @endif

                        @if (in_array(9, $permisos) && ($solicitudItem->estatus_id == 1 || $solicitudItem->estatus_id == 6) && ($role == 1 || $role == 2 || ($role == 3 && in_array(auth()->user()->Id_Usuario, [$solicitudItem->encargado_id, $solicitudItem->encargado2_id])) || ($role == 3 && in_array(auth()->user()->Id_Usuario, [12436, 12460]))))
                        <div class="tooltip">
                            <div class="flex justify-center items-center space-x-2">
                                <button type="button" onclick="deleteSolicitud('{{ $solicitudItem->idSolicitud }}')" class="text-xs text-white bg-red-500 px-2 py-1 rounded-md hover:bg-red-600">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                            <span class="tooltiptext">Eliminar</span>
                        </div>
                        @endif

                        @if (in_array(10, $permisos) && ($solicitudItem->estatus_id == 1 || $solicitudItem->estatus_id == 6) && ($role == 1 || $role == 2 || ($role == 3 && in_array(auth()->user()->Id_Usuario, [$solicitudItem->encargado_id, $solicitudItem->encargado2_id])) || ($role == 3 && in_array(auth()->user()->Id_Usuario, [12436, 12460]))))
                        <div class="tooltip">
                            <div class="flex justify-center items-center space-x-2">
                                <button type="button" wire:click="openModalEdit('{{ $solicitudItem->idSolicitud }}')" class="text-xs text-white bg-cyan-500 px-2 py-1 rounded-md hover:bg-cyan-600">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                            </div>
                            <span class="tooltiptext">Editar</span>
                        </div>
                        @endif
                    @endif
                </div>
            </x-home.table.td>
        </tr>

        @if (!$inAction)
        <tr id="subtr-{{ $solicitudItem->idSolicitud }}" class="hidden" wire:ignore>
            <x-home.table.td colspan="17">
                <div></div>
            </x-home.table.td>
        </tr>
        @endif
    @empty
        <tr>
            <x-home.table.td colspan="17" class="text-center">
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
            {{ $solicitudes->links() }}
        </div>
    @else
        <div class="w-full flex justify-end items-center">
            <span class="text-sm text-gray-700">Total de registros: {{ $solicitudes->count() }}</span>
        </div>
    @endif
</div>
