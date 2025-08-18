<x-dialog-modal wire:model="modalCreateCierreTurno" maxWidth="5xl">
    <x-slot name="title">
        <h2 class="text-2xl font-semibold">Realizar cierre de turno</h2>
    </x-slot>

    <x-slot name="content">
        <form>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 pb-2">
                <div class="col-span-2 grid grid-cols-1 gap-4 sm:grid-cols-2 pb-2">
                    <div class="col-span-2">
                        <h6 class="text-sm font-semibold text-[#007BFF]">General</h6>
                        <hr class="mt-1">
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
