{{--  Filtros  --}}
<div class="flex items-start bg-[#E9E9E9] py-1 px-4 shadow-md rounded-md mb-5">
    <div class="grid grid-cols-1 lg:grid-cols-5 md:gap-x-4 md:gap-y-0 gap-y-4 w-full">
        <div>
            <div class="flex justify-between">
                <x-filters.input name="filtroPersonal" labelText="N° Empleado" wire:keydown.enter="doSearch" />
            </div>
        </div>
        <div>
            <div class="flex justify-between">
                <x-filters.input name="filtroEmpleado" labelText="Nombre Empleado" wire:keydown.enter="doSearch" />
            </div>
        </div>
        <div>
            <x-filters.select name="filtroDepartamento" labelText="Departamento" wire:change="doSearch">
                <option value="">Departamento</option>
                @foreach ($departamentos as $departamento)
                    <option value="{{ $departamento->departamento }}">{{ $departamento->departamento }}</option>
                @endforeach
            </x-filters.select>
        </div>
        <div>
            <div class="" wire:change="doSearch">
                <x-filters.select name="filtroNumSemana" labelText="N° Semana">
                    <option value="">Semana</option>
                    @foreach ($semanas as $semana)
                        <option value="{{ $semana->NUMSEMANA }}">{{ $semana->SEMANA }} - {{ $semana->AÑO }}</option>
                    @endforeach
                </x-filters.select>
            </div>
        </div>
        <div>
            <div class="" wire:change="doSearch">
                <x-filters.select name="filtroGrupoJornada" labelText="Jornada">
                    <option value="">Jornada</option>
                    @foreach ($gruposJornada as $gruposJornadaItem)
                        <option value="{{ $gruposJornadaItem->gpoJornada }}">{{ $gruposJornadaItem->gpoJornada }}</option>
                    @endforeach
                </x-filters.select>
            </div>
        </div>
        {{--  <div>
            <x-filters.select name="filtroAnio" labelText="Año" wire:change="doSearch">
                <option value="">Año</option>
                <option value="2025">2025</option>
            </x-filters.select>
        </div>  --}}
    </div>
</div>
