<x-dialog-modal wire:model="modalSelectPersonal" maxWidth="5xl">
    <x-slot name="title">
        <h2 class="text-2xl font-semibold">
            @if ($typeSp == 1)
                Asignar personal
            @else
                Actualizar personal
            @endif
        </h2>
    </x-slot>

    <x-slot name="content">
        <div>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 pb-2">
                <div class="flex justify-between items-center col-span-2">
                    <div class="w-full">
                        <h4 class="font-semibold mb-2">Folio de solicitudes vinculadas</h4>
                        <div class="flex items-center flex-wrap gap-2">
                            @foreach ($folios as $sl)
                                <div class="text-gray-500">
                                    {{ $sl }}
                                    @if (!$loop->last)
                                    <span>,</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="w-full">
                        <p>
                            Número máximo de colaboradores a asignar: <span class="font-semibold">{{ $numMaximoPermitido }}</span>
                        </p>
                    </div>
                </div>

                <div class="col-span-2 grid grid-cols-1 gap-4 sm:grid-cols-2 pb-2">
                    <div class="col-span-2">
                        <x-forms.input type="text" name="personalSearch" labelText="Buscar personal (Por núm empleado o nombre completo)" wire:keydown.enter="searchAndAdd()" />

                        <div class="custom-scrollbar block overflow-y-auto shadow-md rounded-b-md {{ count($listPersonal) > 0 ? 'h-32' : 'h-auto' }}">
                            @forelse ($listPersonal as $lp)
                                <div class="p-2 cursor-pointer hover:bg-gray-100 border border-transparent flex items-center space-x-2 hover:border-gray-200" wire:click="selectPersonal('{{ $lp->Personal }}', '{{ $lp->nombreCompleto }}')">
                                    <p class="text-sm font-semibold">{{ $lp->Personal }}</p>
                                    <p class="text-xs text-gray-500">{{ $lp->nombreCompleto }}</p>
                                </div>
                            @empty
                                <div class="text-center text-gray-500 grid place-content-center h-full {{ count($listPersonal) == 0 ? 'py-4' : '' }}">No se encontraron resultados</div>
                            @endforelse
                        </div>
                    </div>

                    <div class="col-span-2">
                        <h4 class="text-center py-3 font-semibold">Colaboradores seleccionados</h4>
                        <div>
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 border border-gray-200 rounded-md shadow-md px-4 py-4">
                                @forelse ($selectedPersonal as $lps)
                                    <div class="p-2 flex items-center justify-between border border-gray-200 rounded-md shadow-md px-4">
                                        <p>{{ $lps['personal'] }} - {{ $lps['nombre'] }}</p>
                                        <button type="button" wire:click="deletePersonal('{{ $lps['personal'] }}')" class="text-red-500 hover:text-red-700">Eliminar</button>
                                    </div>
                                @empty
                                    <div class="text-center text-gray-500 h-full grid place-content-center col-span-2">No se han seleccionado colaboradores</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <x-slot name="footer">
        <div class="space-x-2">
            <x-secondary-button type="button" wire:click="closeModalAsign" wire:loading.attr="disabled">
                Cancelar
            </x-secondary-button>

            @if ($typeSp == 2)
                <x-button type="button" wire:click="updateAsignColaborador" wire:loading.attr="disabled">
                    Actualizar
                </x-button>
            @else
                <x-button type="button" wire:click="storeAsignColaborador" wire:loading.attr="disabled">
                    Guardar
                </x-button>
            @endif
        </div>
    </x-slot>
</x-dialog-modal>
