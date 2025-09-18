{{--  Modal para mostrar el pdf  --}}
<x-dialog-modal wire:model="modalPdf" maxWidth="5xl">
    <x-slot name="title">
        <h2 class="text-2xl font-semibold">Reporte PDF cierre de turno</h2>
    </x-slot>

    <x-slot name="content">
        @if ($reportePdf != null)
        <div class="col-span-2 grid grid-cols-1 gap-4 md:grid-cols-4 pb-2">
            <div class="col-span-4">
                {{--  Mostramos el pdf decodificado en base 64  --}}
                <iframe src="data:application/pdf;base64,{{ $reportePdf }}" width="100%" height="550"
                    frameborder="0"
                    webkitallowfullscreen="true"
                    mozallowfullscreen="true"
                    allow="fullscreen"
                ></iframe>
            </div>
        </div>
        @endif
    </x-slot>

    <x-slot name="footer">
        <div class="space-x-2">
            <x-secondary-button wire:click="$toggle('modalPdf')" wire:loading.attr="disabled">
                Cerrar
            </x-secondary-button>
        </div>
    </x-slot>
</x-dialog-modal>
