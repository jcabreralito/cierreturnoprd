<x-dialog-modal wire:model="modalCreateCierreTurno" maxWidth="7xl">
    <x-slot name="title">
        <h2 class="text-2xl font-semibold">Realizar cierre de turno</h2>
    </x-slot>

    <x-slot name="content">
        <form>
            <div class="grid grid-cols-1 gap-4 pb-2">
                @if (count($reporteActual) > 0)
                    <div>
                        @include('livewire.cierre-turno.components.eficiencia')
                    </div>
                @endif
                @if (!$esBueno)
                    <div>
                        <x-forms.text-area labelText="Observaciones" placeholder="Ingrese observaciones aquí..." />
                    </div>

                    <div>
                        <x-forms.text-area labelText="Acciones Correctivas" placeholder="Ingrese acciones correctivas aquí..." />
                    </div>

                    <div>
                        <x-forms.input type="password" labelText="Ingresa tu contraseña para poder continuar" placeholder="Ingrese tu contraseña aquí..." />
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

            <x-button wire:click="storeSolicitud" wire:loading.attr="disabled">
                Guardar
            </x-button>
        </div>
    </x-slot>
</x-dialog-modal>
