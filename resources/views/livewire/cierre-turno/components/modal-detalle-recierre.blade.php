<x-dialog-modal wire:model="modalDetalle" maxWidth="7xl">
    <x-slot name="title">
        <h2 class="text-2xl font-semibold">Detalles - Cierre {{ $reporte->folio }}</h2>
    </x-slot>

    <x-slot name="content">
        <div>
            @if ($reporteActual)
            @include('livewire.cierre-turno.components.eficiencia-std')
            @endif
        </div>

        @if ($listadoMotivosRechazo != null && count($listadoMotivosRechazo) > 0)
        <div class="mt-4">
            <h3 class="text-xl font-semibold mb-2">Motivos de rechazó</h3>

            <ul class="space-y-1 list-disc list-inside text-gray-700">
                {{--  Listado de los motivos de rechazó  --}}
                @foreach ($listadoMotivosRechazo as $motivo)
                    <li>{{ $motivo->motivo }}</li>
                @endforeach
            </ul>
        </div>

        <hr class="my-4">
        @endif

        @if (!$esBueno)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <div class="md:col-span-2">
                <h3 class="text-xl font-semibold">Información de ineficiencia</h3>
            </div>

            {{--  Listado de las causas  --}}
            <div>
                <h3 class="text-lg font-semibold mb-2">Causas de la ineficiencia</h3>
                @if (count($causas) > 0)
                    <ul class="list-disc list-inside">
                        @foreach ($causas as $causa)
                            <li class="mb-1">{{ $causa->causa }}</li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-gray-600">No se encontraron causas registradas.</p>
                @endif
            </div>

            {{--  Listado de los compromisos  --}}
            <div>
                <h3 class="text-lg font-semibold mb-2">Compromisos de mejora</h3>
                @if (count($compromisos) > 0)
                    <ul class="list-disc list-inside">
                        @foreach ($compromisos as $compromiso)
                            <li class="mb-1">{{ $compromiso->compromiso }}</li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-gray-600">No se encontraron compromisos registrados.</p>
                @endif
            </div>
        </div>
        @endif
    </x-slot>

    <x-slot name="footer">
        <div class="space-x-2">
            <x-secondary-button wire:click="closeModalDetalleRecierre" wire:loading.attr="disabled">
                Cancelar
            </x-secondary-button>
        </div>
    </x-slot>
</x-dialog-modal>
