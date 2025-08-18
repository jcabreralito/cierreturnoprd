<div>
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
        @include('livewire.solicitud.components.loader')

        {{--  Header  --}}
        <div class="flex justify-between items-center py-4">
            <h1 class="text-2xl font-semibold text-left text-gray-700 w-full">Relación horas</h1>

            <div>
                <div class="w-full flex justify-end items-center">
                    {{--  Validamos que si cualquiera de los filtros esta activo se muestre el botón  --}}
                    @if(($filtroAnio != '' && $filtroAnio != null) || ($filtroNumSemana != '' && $filtroNumSemana != null) || ($filtroDepartamento != '' && $filtroDepartamento != null) || ($filtroPersonal != '' && $filtroPersonal != null) || ($filtroEmpleado != '' && $filtroEmpleado != null) || ($filtroGrupoJornada != '' && $filtroGrupoJornada != null))
                        {{--  Botón para limpiar filtros  --}}
                        <div class="tooltip">
                            {{--  Botón para limpiar filtros  --}}
                            <x-button wire:click="clearFilters" class="bg-red-500 text-white text-sm border border-red-700 rounded-md shadow-md hover:bg-red-600 transition-all duration-300">
                                <i class="fa-solid fa-filter-circle-xmark"></i>
                            </x-button>

                            <span class="tooltiptext">Borrar filtros</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <hr class="py-2">

        @include('livewire.solicitud.components.filters-tbl-relaciones')

        <div class="mb-4">
            @include('livewire.solicitud.tables.tbl-relaciones')
        </div>
    </div>
</div>
