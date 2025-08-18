<div class="space-x-3 flex">
    {{--  admin gnr, jefes de area, programador  --}}
    @if (in_array(2, $permisos))
        @if ($modeAction == 2)
            @if ($mode == 2)
            <div>
                <div class="tooltip">
                    <x-button type="button" class="bg-sky-500 text-white hover:bg-sky-600 transition-all duration-300" wire:click="activeDeactiveSeleccion(1, 1)">
                        <i class="fa-solid fa-square-check"></i>
                    </x-button>
                    <span class="tooltiptext">Activar selección personal</span>
                </div>
            </div>
            @else
            <div class="space-x-3 flex items-center">
                <div class="tooltip">
                    <x-button type="button" class="bg-green-500 text-white hover:bg-green-600 transition-all duration-300" wire:click="addPersonal">
                        <i class="fa-solid fa-user-plus"></i>
                    </x-button>
                    <span class="tooltiptext">Seleccionar personal</span>
                </div>

                <div class="tooltip">
                    <x-button type="button" class="bg-red-500 text-white hover:bg-red-600 transition-all duration-300" wire:click="activeDeactiveSeleccion(2, 1)">
                        <i class="fa-solid fa-rectangle-xmark"></i>
                    </x-button>

                    <span class="tooltiptext">Desactivar selección personal</span>
                </div>
            </div>
            @endif
        @endif
    @endif

    {{--  admin gnr, jefes de area, programador  --}}
    @if (in_array(8, $permisos))
        @if ($mode == 2)
            @if ($modeAction == 2)
            <div>
                @if ($role == 1 || $role == 3)
                <div class="tooltip">
                    <button type="button" class="bg-cyan-500 text-white hover:bg-cyan-600 btn" onclick="preguntarTipoDeMarcado(1, 2)">
                        <i class="fa-solid fa-check-double"></i>
                    </button>
                    <span class="tooltiptext">Activar selección para finalizar</span>
                </div>
                @else
                <div class="tooltip">
                    <button type="button" class="bg-cyan-500 text-white hover:bg-cyan-600 btn" wire:click="activeDeactiveSeleccion(1, 2)">
                        <i class="fa-solid fa-check-double"></i>
                    </button>
                    <span class="tooltiptext">Activar selección para finalizar</span>
                </div>
                @endif
            </div>
            @else
            <div class="space-x-3 flex items-center">
                <div class="tooltip">
                    <button type="button" class="bg-blue-500 text-white hover:bg-blue-600 btn" onclick="markAsFinalized('{{ $role == 3 ? 2 : 1 }}')">
                        <i class="fa-solid fa-square-check"></i>
                    </button>
                    <span class="tooltiptext">Marcar solcitudes como finalizadas</span>
                </div>

                <div class="tooltip">
                    <button type="button" class="bg-red-500 text-white hover:bg-red-600 btn" wire:click="activeDeactiveSeleccion(2, 2)">
                        <i class="fa-solid fa-rectangle-xmark"></i>
                    </button>

                    <span class="tooltiptext">Desactivar selección para finalizar</span>
                </div>
            </div>
            @endif
        @endif
    @endif

    <div class="w-full flex justify-end items-center">
        {{--  Validamos que si cualquiera de los filtros esta activo se muestre el botón  --}}
        @if(($filtroDepartamento != '' && $filtroDepartamento != null) || ($filtroMaquina != '' && $filtroMaquina != null) || ($filtroEstatus != '' && $filtroEstatus != null) || ($filtroTurno != '' && $filtroTurno != null) || ($filtroMotivo != '' && $filtroMotivo != null) || ($filtroFecha != '' && $filtroFecha != null) || ($filtroHoraInicio != '' && $filtroHoraInicio != null) || ($filtroHoraFin != '' && $filtroHoraFin != null) || ($filtroOp != '' && $filtroOp != null) || ($filtroObservaciones != '' && $filtroObservaciones != null) || ($filtroSemana != '' && $filtroSemana != null) || ($filtroFolio != '' && $filtroFolio != null) || ($filtroPersonal != '' && $filtroPersonal != null) || ($filtroPersonalNombre != '' && $filtroPersonalNombre != null))
            {{--  Botón para limpiar filtros  --}}
            <div class="tooltip">
                {{--  Botón para limpiar filtros  --}}
                <x-button wire:click="clearFilters" class="bg-red-500 text-white text-sm border border-red-700 rounded-md shadow-md hover:bg-red-600 transition-all duration-300">
                    <i class="fa-solid fa-filter-circle-xmark"></i>
                </x-button>

                <span class="tooltiptext">Borrar filtros</span>
            </div>
        @endif
    </div>
</div>
