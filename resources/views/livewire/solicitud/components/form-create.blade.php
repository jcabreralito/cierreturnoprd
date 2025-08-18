<x-dialog-modal wire:model="modalCreate" maxWidth="5xl">
    <x-slot name="title">
        <h2 class="text-2xl font-semibold">Registro solicitud de tiempo extra</h2>
    </x-slot>

    <x-slot name="content">
        <form>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 pb-2">
                <div class="col-span-2 grid grid-cols-1 gap-4 sm:grid-cols-2 pb-2">
                    <div class="col-span-2">
                        <h6 class="text-sm font-semibold text-[#007BFF]">General</h6>
                        <hr class="mt-1">
                    </div>

                    <div>
                        <x-forms.select name="departamento_id" labelText="Departamento">
                            <option value="">Seleccione un departamento</option>
                            @foreach ($departamentos as $departamentoR)
                                <option value="{{ $departamentoR->id }}">{{ $departamentoR->departamento }}</option>
                            @endforeach
                        </x-forms.select>
                    </div>

                    <div>
                        <x-forms.select name="maquina_id" labelText="Recurso" wire:change="getMaxNumUsuarios()">
                            <option value="">Seleccione un recurso</option>
                            @foreach ($maquinas as $maquinaR)
                                <option value="{{ $maquinaR->id }}">{{ $maquinaR->maquina }}</option>
                            @endforeach
                        </x-forms.select>
                    </div>

                    <div>
                        <x-forms.input name="desde_dia" labelText="Desde" type="date" wire:change="getFechasFrom" />
                    </div>

                    <div>
                        <x-forms.input name="num_repeticiones" labelText="Repeticiones" class="isNumberInt" type="number" wire:keyup="getFechasFrom" wire:change="getFechasFrom" />
                    </div>

                    @if (count($listFechasFrom) > 0)
                    <div class="col-span-2">
                        {{--  Descripcion del numero de dias desde dia hasta el numero de repeticiones  --}}
                        <div class="bg-white rounded-md shadow-md px-2 py-3">
                            <p class="text-xs pb-2 font-semibold">Fechas que se asignaran a la solicitud de acuerdo al número de repeticiones</p>
                            <ul class="flex flex-wrap space-x-2 text-xs text-blue-700">
                                @foreach ($listFechasFrom as $item)
                                    <li>{{ $item }}
                                        @if (!$loop->last)
                                            <span>,</span>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    @endif

                    <div class="col-span-2">
                        <x-forms.input name="op" labelText="OP" wire:keyup="getNombreTrabajo" wire:keydown.enter="addOpToList()" />

                        <div class="w-full px-2 py-3 bg-white rounded-md border border-gray-200 shadow-md mt-3">
                            <div>
                                <p class="text-xs pb-2 font-semibold">Nombre del trabajo (Buscado)</p>
                                <p @if($trabajo != null && $trabajo != '')
                                    wire:click="addOpToList()"
                                    @endif
                                class="text-xs {{ ($trabajo != '') ? 'text-sky-700 cursor-pointer' : 'text-gray-500' }}">{{ ($trabajo != '') ? $trabajo : 'No se ha buscado una OP' }}</p>
                            </div>

                            <div class="mt-2">
                                <p class="text-xs pb-2 font-semibold">OPs seleccionadas</p>
                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 border border-gray-300 bg-gray-200 rounded-md shadow-md px-4 py-4">
                                    @foreach ($listOps as $opItem)
                                    <div class="p-2 flex items-center justify-between border border-gray-200 bg-white rounded-md shadow-md px-4">
                                        <p>{{ $opItem['op'] }} - {{ $opItem['trabajo'] }}</p>
                                        <button type="button" wire:click="removeOpFromList('{{ $opItem['op'] }}')" class="text-red-500 hover:text-red-700">Eliminar</button>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <x-forms.input name="horas" labelText="Horas" class="isNumberFloat" type="number" />
                    </div>

                    <div>
                        <x-forms.select name="turno_id" labelText="Turno">
                            <option value="">Seleccione un motivo</option>
                            @foreach ($turnos as $turnoR)
                                <option value="{{ $turnoR->id }}">{{ $turnoR->turno }}</option>
                            @endforeach
                        </x-forms.select>
                    </div>

                    <div>
                        <x-forms.select name="motivo_id" labelText="Motivo">
                            <option value="">Seleccione un motivo</option>
                            @foreach ($motivos as $motivoR)
                                <option value="{{ $motivoR->id }}">{{ $motivoR->motivo }}</option>
                            @endforeach
                        </x-forms.select>
                    </div>

                    <div>
                        <x-forms.input name="num_max_usuarios" labelText="Núm empleados máximo" class="isNumberInt" type="number" />
                    </div>

                    <div class="col-span-2">
                        <x-forms.text-area name="observaciones" labelText="Observaciones" />
                    </div>
                </div>
            </div>
        </form>
    </x-slot>

    <x-slot name="footer">
        <div class="space-x-2">
            <x-secondary-button wire:click="closeModalCreate" wire:loading.attr="disabled">
                Cancelar
            </x-secondary-button>

            <x-button wire:click="storeSolicitud" wire:loading.attr="disabled">
                Guardar
            </x-button>
        </div>
    </x-slot>
</x-dialog-modal>
