<div>
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8" x-data="{ showType: false }">
        {{--  Header  --}}
        <div class="flex justify-between items-center py-4">
            <h1 class="text-2xl font-semibold text-left text-gray-700 w-full">Solicitudes de horas extras</h1>

            <div class="w-full flex justify-end items-center space-x-4">
                <button type="button" class="bg-gray-200 text-gray-700 hover:bg-gray-300 transition-all duration-300 px-4 py-2 rounded-md text-xxs font-semibold" x-on:click="showType = !showType">
                    <span x-show="!showType">Mostrar por personal</span>
                    <span x-show="showType">Mostrar por solicitud</span>
                </button>

                <div class="py-6 flex justify-end items-center">
                    @include('livewire.solicitud.tables.acciones-solicitudes')
                </div>
            </div>
        </div>

        <hr class="py-2">

        @include('livewire.solicitud.components.filters')


        <div class="mb-4" x-show="!showType">
            @include('livewire.solicitud.tables.solicitudes')
        </div>

        <div class="mb-4" x-show="showType">
            @include('livewire.solicitud.tables.personal')
        </div>
    </div>

    {{--  admin gnr, admin oper, programador  --}}
    @if (in_array(1, $permisos) && ($mode == 2 || $modeAction == 2))
    {{--  Botón de registro de una nueva capacitación  --}}
    <div class="fixed bottom-4 right-4">
        <button type="button" wire:click="openModalCreate()" class="flex items-center justify-center w-12 h-12 bg-sky-500 text-white rounded-full shadow-lg hover:bg-sky-600 transition-all duration-300">
            <i class="fa-solid fa-plus"></i>
        </button>
    </div>
    @endif

    {{--  Modal para el registro de una nueva capacitación  --}}
    @include('livewire.solicitud.components.form-create')

    {{--  Modal para el registro de una nueva capacitación  --}}
    @include('livewire.solicitud.components.form-update')

    {{--  Modal para la adicion de personal a la solicitud  --}}
    @include('livewire.solicitud.components.form-add-personal')

    @include('livewire.solicitud.components.loader')
</div>

@include('livewire.solicitud.components.scripts')
