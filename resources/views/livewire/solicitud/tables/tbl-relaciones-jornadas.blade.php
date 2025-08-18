<div>
    <x-home.table.table :headers="[
        [0 => 'PERSONAL', 1 => true, 2 => '', 3 => 'personal'],
        [0 => 'NOMBRE', 1 => true, 2 => '', 3 => 'nombre'],
        [0 => 'DEPARTAMENTO', 1 => true, 2 => '', 3 => 'departamento'],
        [0 => 'SEMANA', 1 => true, 2 => 'text-center', 3 => 'semana'],
        [0 => 'CONFIG', 1 => true, 2 => 'text-center', 3 => 'nombre_config'],
        [0 => 'LUNES', 1 => true, 2 => 'text-center', 3 => 'jornadaLunes'],
        [0 => 'MARTES', 1 => true, 2 => 'text-center', 3 => 'jornadaMartes'],
        [0 => 'MIERCOLES', 1 => true, 2 => 'text-center', 3 => 'jornadaMiercoles'],
        [0 => 'JUEVES', 1 => true, 2 => 'text-center', 3 => 'jornadaJueves'],
        [0 => 'VIERNES', 1 => true, 2 => 'text-center', 3 => 'jornadaViernes'],
        [0 => 'SABADO', 1 => true, 2 => 'text-center', 3 => 'jornadaSabado'],
        [0 => 'DOMINGO', 1 => true, 2 => 'text-center', 3 => 'jornadaDomingo'],
        [0 => 'GPO. JORN.', 1 => true, 2 => '', 3 => 'grupo'],
        [0 => 'GPO. HRS.', 1 => true, 2 => '', 3 => 'default_hrs'],
        [0 => 'NO EXCEDE', 1 => true, 2 => 'text-center', 3 => 'hrs_extras'],
        [0 => 'HRS.JORN.', 1 => true, 2 => '', 3 => 'hrs_jornada_final'],
        [0 => 'HRS. EXTRAS', 1 => true, 2 => '', 3 => 'hrs_extras'],
    ]"
        tblClass="tblNormal"
    >
        @forelse ($solicitudesPorPersonal as $solicitudItemP)
            <tr wire:key="{{ $solicitudItemP->rjId }}" class="hover:bg-gray-100 transition-all duration-300">
                <x-home.table.td>{{ $solicitudItemP->personal }}</x-home.table.td>
                <x-home.table.td>{{ $solicitudItemP->nombre }}</x-home.table.td>
                <x-home.table.td>{{ $solicitudItemP->departamento }}</x-home.table.td>
                <x-home.table.td>{{ $solicitudItemP->semana }}</x-home.table.td>
                <x-home.table.td>
                    <div class="text-xxs">
                        {{--  Validamos si el registro es de la semana actual  --}}
                        @if ($solicitudItemP->estatus == 1)
                        <select id="rj{{ $solicitudItemP->rjId }}" class="select-tbl h-[30px] {{ $solicitudItemP->config_id == null ? '!bg-cyan-500 !text-white' : (($solicitudItemP->config_id != 21) ? '!bg-green-500 !text-white' : '!bg-amber-500 !text-white') }}" wire:change="changeConfigJornada('{{ $solicitudItemP->rjId }}', $event.target.value)">
                            <option value="-1">{{ ($solicitudItemP->config_id == 21) ? 'NUEVO' : 'Especial' }}</option>
                            @foreach ($catConfigJornadasTbl as $catConfigJornada)
                                @if ($solicitudItemP->config_id == $catConfigJornada->id)
                                    <option value="{{ $catConfigJornada->id }}" selected>{{ $catConfigJornada->nombre }}</option>
                                @else
                                    <option value="{{ $catConfigJornada->id }}">{{ $catConfigJornada->nombre }}</option>
                                @endif
                            @endforeach
                        </select>
                        @else
                        <span class="text-center font-semibold text-sky-500 w-full block">
                            @if ($solicitudItemP->nombre_config != null)
                                {{ $solicitudItemP->nombre_config }}
                            @else
                                Especial
                            @endif
                        </span>
                        @endif
                    </div>
                </x-home.table.td>
                <x-home.table.td>
                    <div class="text-xxs">
                        {{--  Validamos si el registro es de la semana actual  --}}
                        @if ($solicitudItemP->estatus == 1)
                        <select id="rjl{{ $solicitudItemP->rjId }}" class="select-tbl h-[30px] {{ in_array($solicitudItemP->lunes, [1, 7, 8]) ? '!bg-gray-400 !text-white' : '' }}" wire:change="changeJornada('{{ $solicitudItemP->rjId }}', $event.target.value, 'lunes', 'rjl')">
                            <option value="">Jornadas</option>
                            @foreach ($catJornadas as $catJornada)
                                <option value="{{ $catJornada->id }}" {{ $solicitudItemP->lunes == $catJornada->id ? 'selected' : '' }}>{{ $catJornada->horario }}</option>
                            @endforeach
                        </select>
                        @else
                        <span class="text-center font-semibold text-gray-700 w-full block">
                            @if ($solicitudItemP->lunes != null)
                                {{ $solicitudItemP->horarioLunes }}
                            @else
                                Sin jornada
                            @endif
                        </span>
                        @endif
                    </div>
                </x-home.table.td>
                <x-home.table.td>
                    <div class="text-xxs">
                        {{--  Validamos si el registro es de la semana actual  --}}
                        @if ($solicitudItemP->estatus == 1)
                        <select id="rjma{{ $solicitudItemP->rjId }}" class="select-tbl h-[30px] {{ in_array($solicitudItemP->martes, [1, 7, 8]) ? '!bg-gray-400 !text-white' : '' }}" wire:change="changeJornada('{{ $solicitudItemP->rjId }}', $event.target.value, 'martes', 'rjma')">
                            <option value="">Jornadas</option>
                            @foreach ($catJornadas as $catJornada)
                                <option value="{{ $catJornada->id }}" {{ $solicitudItemP->martes == $catJornada->id ? 'selected' : '' }}>{{ $catJornada->horario }}</option>
                            @endforeach
                        </select>
                        @else
                        <span class="text-center font-semibold text-gray-700 w-full block">
                            @if ($solicitudItemP->martes != null)
                                {{ $solicitudItemP->horarioMartes }}
                            @else
                                Sin jornada
                            @endif
                        </span>
                        @endif
                    </div>
                </x-home.table.td>
                <x-home.table.td>
                    <div class="text-xxs">
                        {{--  Validamos si el registro es de la semana actual  --}}
                        @if ($solicitudItemP->estatus == 1)
                        <select id="rjmi{{ $solicitudItemP->rjId }}" class="select-tbl h-[30px] {{ in_array($solicitudItemP->miercoles, [1, 7, 8]) ? '!bg-gray-400 !text-white' : '' }}" wire:change="changeJornada('{{ $solicitudItemP->rjId }}', $event.target.value, 'miercoles', 'rjmi')">
                            <option value="">Jornadas</option>
                            @foreach ($catJornadas as $catJornada)
                                <option value="{{ $catJornada->id }}" {{ $solicitudItemP->miercoles == $catJornada->id ? 'selected' : '' }}>{{ $catJornada->horario }}</option>
                            @endforeach
                        </select>
                        @else
                        <span class="text-center font-semibold text-gray-700 w-full block">
                            @if ($solicitudItemP->miercoles != null)
                                {{ $solicitudItemP->horarioMiercoles }}
                            @else
                                Sin jornada
                            @endif
                        </span>
                        @endif
                    </div>
                </x-home.table.td>
                <x-home.table.td>
                    <div class="text-xxs">
                        {{--  Validamos si el registro es de la semana actual  --}}
                        @if ($solicitudItemP->estatus == 1)
                        <select id="rjju{{ $solicitudItemP->rjId }}" class="select-tbl h-[30px] {{ in_array($solicitudItemP->jueves, [1, 7, 8]) ? '!bg-gray-400 !text-white' : '' }}" wire:change="changeJornada('{{ $solicitudItemP->rjId }}', $event.target.value, 'jueves', 'rjju')">
                            <option value="">Jornadas</option>
                            @foreach ($catJornadas as $catJornada)
                                <option value="{{ $catJornada->id }}" {{ $solicitudItemP->jueves == $catJornada->id ? 'selected' : '' }}>{{ $catJornada->horario }}</option>
                            @endforeach
                        </select>
                        @else
                        <span class="text-center font-semibold text-gray-700 w-full block">
                            @if ($solicitudItemP->jueves != null)
                                {{ $solicitudItemP->horarioJueves }}
                            @else
                                Sin jornada
                            @endif
                        </span>
                        @endif
                    </div>
                </x-home.table.td>
                <x-home.table.td>
                    <div class="text-xxs">
                        {{--  Validamos si el registro es de la semana actual  --}}
                        @if ($solicitudItemP->estatus == 1)
                        <select id="rjv{{ $solicitudItemP->rjId }}" class="select-tbl h-[30px] {{ in_array($solicitudItemP->viernes, [1, 7, 8]) ? '!bg-gray-400 !text-white' : '' }}" wire:change="changeJornada('{{ $solicitudItemP->rjId }}', $event.target.value, 'viernes', 'rjv')">
                            <option value="">Jornadas</option>
                            @foreach ($catJornadas as $catJornada)
                                <option value="{{ $catJornada->id }}" {{ $solicitudItemP->viernes == $catJornada->id ? 'selected' : '' }}>{{ $catJornada->horario }}</option>
                            @endforeach
                        </select>
                        @else
                        <span class="text-center font-semibold text-gray-700 w-full block">
                            @if ($solicitudItemP->viernes != null)
                                {{ $solicitudItemP->horarioViernes }}
                            @else
                                Sin jornada
                            @endif
                        </span>
                        @endif
                    </div>
                </x-home.table.td>
                <x-home.table.td>
                    <div class="text-xxs">
                        {{--  Validamos si el registro es de la semana actual  --}}
                        @if ($solicitudItemP->estatus == 1)
                        <select id="rjs{{ $solicitudItemP->rjId }}" class="select-tbl h-[30px] {{ in_array($solicitudItemP->sabado, [1, 7, 8]) ? '!bg-gray-400 !text-white' : '' }}" wire:change="changeJornada('{{ $solicitudItemP->rjId }}', $event.target.value, 'sabado', 'rjs')">
                            <option value="">Jornadas</option>
                            @foreach ($catJornadas as $catJornada)
                                <option value="{{ $catJornada->id }}" {{ $solicitudItemP->sabado == $catJornada->id ? 'selected' : '' }}>{{ $catJornada->horario }}</option>
                            @endforeach
                        </select>
                        @else
                        <span class="text-center font-semibold text-gray-700 w-full block">
                            @if ($solicitudItemP->sabado != null)
                                {{ $solicitudItemP->horarioSabado }}
                            @else
                                Sin jornada
                            @endif
                        </span>
                        @endif
                    </div>
                </x-home.table.td>
                <x-home.table.td>
                    <div class="text-xxs">
                        {{--  Validamos si el registro es de la semana actual  --}}
                        @if ($solicitudItemP->estatus == 1)
                        <select id="rjd{{ $solicitudItemP->rjId }}" class="select-tbl h-[30px] {{ in_array($solicitudItemP->domingo, [1, 7, 8]) ? '!bg-gray-400 !text-white' : '' }}" wire:change="changeJornada('{{ $solicitudItemP->rjId }}', $event.target.value, 'domingo', 'rjd')">
                            <option value="">Jornadas</option>
                            @foreach ($catJornadas as $catJornada)
                                <option value="{{ $catJornada->id }}" {{ $solicitudItemP->domingo == $catJornada->id ? 'selected' : '' }}>{{ $catJornada->horario }}</option>
                            @endforeach
                        </select>
                        @else
                        <span class="text-center font-semibold text-gray-700 w-full block">
                            @if ($solicitudItemP->domingo != null)
                                {{ $solicitudItemP->horarioDomingo }}
                            @else
                                Sin jornada
                            @endif
                        </span>
                        @endif
                    </div>
                </x-home.table.td>
                <x-home.table.td>
                    <p class="text-xxs text-center font-semibold text-gray-700 w-full">
                        @switch ($solicitudItemP->cgjId)
                            @case(1)
                                <span class="text-cyan-500">{{ $solicitudItemP->grupo }}</span>
                            @break

                            @case(2)
                                <span class="text-blue-500">{{ $solicitudItemP->grupo }}</span>
                            @break

                            @case(3)
                                <span class="text-gray-500">{{ $solicitudItemP->grupo }}</span>
                            @break

                        @endswitch
                    </p>
                </x-home.table.td>
                <x-home.table.td>
                    <p class="text-xxs text-center font-semibold text-gray-700 w-full">
                        {{ $solicitudItemP->default_hrs }}
                    </p>
                </x-home.table.td>
                <x-home.table.td>
                    @if ($solicitudItemP->hrs_jornada_final != null)
                    <div class="text-center text-lg">
                        {{--  Validamos si el registro es de la semana actual  --}}
                        @if ($solicitudItemP->esExcedente == 1)
                            <div class="tooltip">
                                <span class="text-yellow-500 font-semibold text-center w-full">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                </span>

                                <span class="tooltiptext">Excede</span>
                            </div>
                        @else
                            <div class="tooltip">
                                <span class="text-blue-500 font-semibold text-center w-full">
                                    <i class="fa-solid fa-circle-minus"></i>
                                </span>

                                <span class="tooltiptext">No excede</span>
                            </div>
                        @endif
                    </div>
                    @endif
                </x-home.table.td>
                <x-home.table.td>
                    <p class="text-xxs text-center font-semibold text-gray-700 w-full">
                        {{ $solicitudItemP->hrs_jornada_final }}
                    </p>
                </x-home.table.td>
                <x-home.table.td>
                    <p class="text-xxs text-center font-semibold text-gray-700 w-full">
                        @if ($solicitudItemP->hrs_jornada_final != null)
                            {{ $solicitudItemP->hrs_extras }}
                        @endif
                    </p>
                </x-home.table.td>
            </tr>
        @empty
            <tr>
                <x-home.table.td colspan="17" class="text-center">
                    <div class="flex justify-center flex-col items-center space-y-2">
                        <span>No se encontraron registros</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                            <g fill="none" stroke="#484848" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="1.5">
                                <path fill="#484848"
                                    d="M8.5 9a.5.5 0 1 1 0-1a.5.5 0 0 1 0 1m7 0a.5.5 0 1 1 0-1a.5.5 0 0 1 0 1" />
                                <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2S2 6.477 2 12s4.477 10 10 10" />
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
                {{ $solicitudesPorPersonal->links() }}
            </div>
        @else
            <div class="w-full flex justify-end items-center">
                <span class="text-sm text-gray-700">Total de registros: {{ $solicitudesPorPersonal->count() }}</span>
            </div>
        @endif
    </div>
</div>
