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

        @if ($reporte->estatus == 2)
            <div>
                <h3 class="text-xl font-semibold mt-4 mb-2">Fechas y horas del cierre</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-gray-700">Nombre del supervisor: {{ $reporte->nombre_firma_supervisor }}</p>
                        <p class="text-gray-700">Fecha firma supervisor: {{ Carbon\Carbon::parse($reporte->fecha_firma_supervisor)->format('d/m/Y H:i:s') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-700">Nombre del operador: {{ $reporte->nombre_firma_operador }}</p>
                        <p class="text-gray-700">Fecha firma operador: {{ Carbon\Carbon::parse($reporte->fecha_firma_operador)->format('d/m/Y H:i:s') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if ($listadoMotivosRechazo != null && count($listadoMotivosRechazo) > 0)
        <div class="mt-4">
            <h3 class="text-xl font-semibold mb-2">Motivos de rechazó</h3>

            <ul class="space-y-1 list-disc list-inside text-gray-700 grid grid-cols-1">
                {{--  Listado de los motivos de rechazó  --}}
                @foreach ($listadoMotivosRechazo as $motivo)
                    <li>{{ $motivo->motivo }} - {{ Carbon\Carbon::parse($motivo->fecha_registro)->format('d/m/Y H:i:s') }}</li>
                @endforeach
            </ul>
        </div>

        <hr class="my-4">
        @endif

        @if ((count($causas) > 0 || count($compromisos) > 0))
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
            <x-secondary-button wire:click="$toggle('modalDetalle')" wire:loading.attr="disabled">
                Cancelar
            </x-secondary-button>

            @if ((auth()->user()->tipoUsuarioCierreTurno == 1 || auth()->user()->tipoUsuarioCierreTurno == 3) && $reporte->estatus == 3)
            <x-button wire:click="corregir" wire:loading.attr="disabled">
                Corregir cierre
            </x-button>
            @endif
        </div>
    </x-slot>
</x-dialog-modal>

