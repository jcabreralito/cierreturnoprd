<div>
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
        @include('livewire.solicitud.components.loader')

        {{--  Header  --}}
        <div class="flex justify-between items-center py-4">
            <h1 class="text-2xl font-semibold text-left text-gray-700 w-full">Relación Jornadas</h1>

            <div>
                <div class="w-full flex justify-end items-center space-x-3">
                    {{--  Botón para mostrar el modal informativo de configuracion de jornadas  --}}
                    <div class="tooltip">
                        {{--  Botón para mostrar el modal informativo de configuracion de jornadas  --}}
                        <x-button wire:click="$toggle('modalConfigJornada')" class="bg-sky-500 text-white text-sm border border-sky-700 rounded-md shadow-md hover:bg-sky-600 transition-all duration-300">
                            <i class="fa-solid fa-circle-info"></i>
                        </x-button>

                        <span class="tooltiptext">Mostrar catálogo de configuraciones</span>
                    </div>

                    @if ($role == 1 || $role == 4)
                    {{--  Botón para generar el reporte de excel  --}}
                    <div class="tooltip">
                        {{--  Botón para generar el reporte de excel  --}}
                        <x-button wire:click="generarExcel()" class="bg-green-500 text-white text-sm border border-green-700 rounded-md shadow-md hover:bg-green-600 transition-all duration-300">
                            <i class="fa-solid fa-file-excel"></i>
                        </x-button>

                        <span class="tooltiptext">Generar excel</span>
                    </div>
                    @endif

                    {{--  Validamos que si cualquiera de los filtros esta activo se muestre el botón  --}}
                    @if(($filtroDepartamento != '' && $filtroDepartamento != null) ||
                    ($filtroMaquina != '' && $filtroMaquina != null) ||
                    ($filtroEstatus != '' && $filtroEstatus != null) ||
                    ($filtroJornada != '' && $filtroJornada != null) ||
                    ($filtroMotivo != '' && $filtroMotivo != null) ||
                    ($filtroFecha != '' && $filtroFecha != null) ||
                    ($filtroHoraInicio != '' && $filtroHoraInicio != null) ||
                    ($filtroHoraFin != '' && $filtroHoraFin != null) ||
                    ($filtroOp != '' && $filtroOp != null) ||
                    ($filtroObservaciones != '' && $filtroObservaciones != null) ||
                    ($filtroFolio != '' && $filtroFolio != null) ||
                    ($filtroPersonal != '' && $filtroPersonal != null) ||
                    ($filtroPersonalNombre != '' && $filtroPersonalNombre != null) ||
                    ($filtroSemana != '' && $filtroSemana != null) ||
                    ($filtroGrupoJornada != '' && $filtroGrupoJornada != null) ||
                    ($filtroConfJornada != '' && $filtroConfJornada != null) ||
                    ($filtroExcedente != '' && $filtroExcedente != null)
                    )
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

        @include('livewire.solicitud.components.filters-rj')

        <div class="mb-4">
            @include('livewire.solicitud.tables.tbl-relaciones-jornadas')
        </div>

        <div>
            @include('livewire.solicitud.components.modal-detalles-configuracion')
        </div>
    </div>

    <script>
        window.addEventListener('refreshData', function(event) {
            // Obtener el elemento select por su ID
            let id = event.detail.id;
            let select = document.getElementById('rj'+id);

            // Verificar si el elemento select existe
            if (select) {
                // Reiniciar el valor del select a su opción por defecto
                select.selectedIndex = 0; // Cambia el índice según la opción por defecto que desees
            }
        });

        window.addEventListener('refreshRow', function(event) {
            // Obtener el elemento select por su ID
            let id = event.detail.id;
            let prefix = event.detail.prefix;
            let select = document.getElementById(prefix + id);
            let value = event.detail.value;

            // Verificar si el elemento select existe
            if (select) {
                // Reiniciar el valor del select a su opción por defecto
                select.value = value; // Cambia el índice según la opción por defecto que desees
            }
        });

        window.addEventListener('refreshDataConfig', function(event) {
            // Obtener el elemento select por su ID
            let id = event.detail.id;
            // Obtener la data del evento
            let data = event.detail.data;

            // Validamos si es array y si tiene datos
            document.getElementById('rjl' + id).value = data['lunes'];
            document.getElementById('rjma' + id).value = data['martes'];
            document.getElementById('rjmi' + id).value = data['miercoles'];
            document.getElementById('rjju' + id).value = data['jueves'];
            document.getElementById('rjv' + id).value = data['viernes'];
            document.getElementById('rjs' + id).value = data['sabado'];
            document.getElementById('rjd' + id).value = data['domingo'];
        });

    </script>
</div>
