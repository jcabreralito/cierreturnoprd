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

        @if ((auth()->user()->tipoUsuarioCierreTurno == 1 || auth()->user()->tipoUsuarioCierreTurno == 3) && $reporte->estatus == 3)
        <form class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
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
            <div>
                <label class="block font-semibold mb-1">Compromiso de mejora (¿Qué acciones vas a realizar para mejorar la eficiencia?)</label>
                @foreach ($acciones_correctivas as $index => $accion)
                    <div class="flex items-center mb-2 w-full">
                        <x-forms.text-area
                            name="acciones_correctivas.{{ $index }}"
                            labelText="Compromiso #{{ $index + 1 }}"
                            placeholder="Ingrese acción correctiva aquí..."
                        />
                        @if ($index > 0)
                        <div class="tooltip">
                            <button type="button" class="ml-2 text-red-500" wire:click="removeAccionCorrectiva({{ $index }})">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                            <span class="tooltiptext">Eliminar acción correctiva</span>
                        </div>
                        @endif
                    </div>
                @endforeach
                <button type="button" class="mt-2 bg-sky-600 rounded-full text-white w-5" wire:click="addAccionCorrectiva">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </form>
        @else
        @if (count($causas) > 0 || count($compromisos) > 0)
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
