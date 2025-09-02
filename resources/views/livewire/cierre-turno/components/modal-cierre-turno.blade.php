<x-dialog-modal wire:model="modalCreateCierreTurno" maxWidth="7xl">
    <x-slot name="title">
        <h2 class="text-2xl font-semibold">Realizar cierre de turno</h2>
    </x-slot>

    <x-slot name="content">
        <form>
            <div class="grid grid-cols-1 gap-2 pb-2">
                @if (count($reporteActual) > 0)
                    <div>
                        @include('livewire.cierre-turno.components.eficiencia')
                    </div>
                @endif
                @if (!$esBueno)
                    {{-- Causas de la ineficiencia --}}
                    <div>
                        <label class="block font-semibold mb-1">Causa de la ineficiencia (¿A que consideras que se deba la ineficiencia?)</label>
                        @foreach ($observaciones as $index => $observacion)
                            <div class="flex items-center mb-2 w-full">
                                <x-forms.text-area
                                    name="observaciones.{{ $index }}"
                                    labelText="Causa #{{ $index + 1 }}"
                                    placeholder="Ingrese observación aquí..."
                                />
                                @if ($index > 0)
                                <button type="button" class="ml-2 text-red-500" wire:click="removeObservacion({{ $index }})">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                                @endif
                            </div>
                        @endforeach
                        <button type="button" class="mt-2 bg-sky-600 rounded-full text-white w-5" wire:click="addObservacion">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>

                    {{-- Compromisos de mejora --}}
                    <div class="mt-4">
                        <label class="block font-semibold mb-1">Compromiso de mejora (¿Qué acciones vas a realizar para mejorar la eficiencia?)</label>
                        @foreach ($acciones_correctivas as $index => $accion)
                            <div class="flex items-center mb-2 w-full">
                                <x-forms.text-area
                                    name="acciones_correctivas.{{ $index }}"
                                    labelText="Compromiso #{{ $index + 1 }}"
                                    placeholder="Ingrese acción correctiva aquí..."
                                />
                                @if ($index > 0)
                                <button type="button" class="ml-2 text-red-500" wire:click="removeAccionCorrectiva({{ $index }})">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                                @endif
                            </div>
                        @endforeach
                        <button type="button" class="mt-2 bg-sky-600 rounded-full text-white w-5" wire:click="addAccionCorrectiva">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                @endif
            </div>
        </form>
    </x-slot>

    <x-slot name="footer">
        <div class="space-x-2">
            <x-secondary-button wire:click="closeModalCreate" wire:loading.attr="disabled">
                Cancelar
            </x-secondary-button>

            <x-button wire:click="confirmarCierre" wire:loading.attr="disabled">
                Realizar cierre
            </x-button>
        </div>
    </x-slot>
</x-dialog-modal>
