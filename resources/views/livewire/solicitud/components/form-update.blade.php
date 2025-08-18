<x-dialog-modal wire:model="modalUpdate" maxWidth="5xl">
    <x-slot name="title">
        <h2 class="text-2xl font-semibold">Actualizar solicitud de tiempo extra</h2>
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
                        <x-forms.select name="departamento_idU" labelText="Departamento" disabled>
                            <option value="">Seleccione un departamento</option>
                            @foreach ($departamentos as $departamentoR)
                                <option value="{{ $departamentoR->id }}"
                                    >{{ $departamentoR->departamento }}</option>
                            @endforeach
                        </x-forms.select>
                    </div>

                    <div>
                        <x-forms.select name="maquina_idU" labelText="Recurso" wire:change="getMaxNumUsuarios()" disabled>
                            <option value="">Seleccione un recurso</option>
                            @foreach ($maquinas as $maquinaR)
                                <option
                                    @if ($maquinaR->id == $solicitudUpdate?->maquina_id)
                                        selected
                                    @endif
                                value="{{ $maquinaR->id }}">{{ $maquinaR->maquina }}</option>
                            @endforeach
                        </x-forms.select>
                    </div>

                    <div class="col-span-2">
                        <x-forms.input name="opU" labelText="OP" wire:keyup="getNombreTrabajoU" wire:keydown.enter="addOpToListU()" />

                        <div class="w-full px-2 py-3 bg-white rounded-md border border-gray-200 shadow-md mt-3">
                            <div>
                                <p class="text-xs pb-2 font-semibold">Nombre del trabajo (Buscado)</p>
                                <p @if($trabajoU != null && $trabajoU != '')
                                    wire:click="addOpToListU()"
                                    @endif
                                class="text-xs {{ ($trabajoU != '') ? 'text-sky-700 cursor-pointer' : 'text-gray-500' }}">{{ ($trabajoU != '') ? $trabajoU : 'No se ha buscado una OP' }}</p>
                            </div>

                            <div class="mt-2">
                                <p class="text-xs pb-2 font-semibold">OPs seleccionadas</p>
                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 border border-gray-300 bg-gray-200 rounded-md shadow-md px-4 py-4">
                                    @foreach ($listOpsU as $opItem)
                                    <div class="p-2 flex items-center justify-between border border-gray-200 bg-white rounded-md shadow-md px-4">
                                        <p>{{ $opItem['op'] }} - {{ $opItem['trabajo'] }}</p>
                                        <button type="button" wire:click="removeOpFromListU('{{ $opItem['op'] }}')" class="text-red-500 hover:text-red-700">Eliminar</button>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <x-forms.input name="horasU" labelText="Horas" class="isNumberFloat" type="number" />
                    </div>

                    <div>
                        <x-forms.select name="motivo_idU" labelText="Motivo">
                            <option value="">Seleccione un motivo</option>
                            @foreach ($motivos as $motivoR)
                                <option value="{{ $motivoR->id }}">{{ $motivoR->motivo }}</option>
                            @endforeach
                        </x-forms.select>
                    </div>

                    <div>
                        <x-forms.input name="num_max_usuariosU" labelText="Núm empleados máximo" class="isNumberInt" type="number" />
                    </div>

                    <div class="col-span-2">
                        <x-forms.text-area name="observacionesU" labelText="Observaciones" />
                    </div>
                </div>
            </div>
        </form>
    </x-slot>

    <x-slot name="footer">
        <div class="space-x-2">
            <x-secondary-button wire:click="closeModalUpdate" wire:loading.attr="disabled">
                Cancelar
            </x-secondary-button>

            <x-button wire:click="updateSolicitud" wire:loading.attr="disabled">
                Actualizar
            </x-button>
        </div>
    </x-slot>
</x-dialog-modal>
