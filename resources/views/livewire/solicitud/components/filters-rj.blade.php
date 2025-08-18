{{--  Filtros  --}}
<div class="flex items-start bg-[#E9E9E9] py-1 px-4 shadow-md rounded-md mb-5">
    <div class="grid grid-cols-1 lg:grid-cols-8 md:gap-x-4 md:gap-y-0 gap-y-4 w-full">
        <div>
            <div class="flex justify-between">
                <x-filters.input name="filtroPersonal" labelText="N° Empleado" wire:keydown.enter="doSearch" />
            </div>
        </div>
        <div>
            <div class="flex justify-between">
                <x-filters.input name="filtroPersonalNombre" labelText="Nombre Empleado" wire:keydown.enter="doSearch" />
            </div>
        </div>
        <div>
            <x-filters.select name="filtroDepartamento" labelText="Departamentos" wire:change="doSearch">
                <option value="">Departamentos</option>
                @foreach ($departamentos as $departamento)
                    <option value="{{ $departamento->id }}">{{ $departamento->departamento }}</option>
                @endforeach
            </x-filters.select>
        </div>
        <div>
            <x-filters.select name="filtroJornada" labelText="Jornada" wire:change="doSearch">
                <option value="">Jornada</option>
                @foreach ($catJornadas as $jornadaItem)
                    <option value="{{ $jornadaItem->id }}">{{ $jornadaItem->horario }}</option>
                @endforeach
            </x-filters.select>
        </div>
        <div>
            <x-filters.select name="filtroGrupoJornada" labelText="Grupo Jornada" wire:change="doSearch">
                <option value="">Grupo Jornada</option>
                @foreach ($catGpoJornadas as $jornadaItem)
                    <option value="{{ $jornadaItem->id }}">{{ $jornadaItem->grupo }}</option>
                @endforeach
            </x-filters.select>
        </div>
        <div>
            <x-filters.select name="filtroConfJornada" labelText="Conf. Jornada" wire:change="doSearch">
                <option value="">Conf. Jornada</option>
                @foreach ($catConfigJornadas as $jornadaItemConf)
                    <option value="{{ $jornadaItemConf->id }}">{{ $jornadaItemConf->nombre }}</option>
                @endforeach
            </x-filters.select>
        </div>
        <div>
            <x-filters.select name="filtroExcedente" labelText="Excedente (tiempo H.E)" wire:change="doSearch">
                <option value="">Excedente</option>
                <option value="1">Excede</option>
                <option value="2">No Excede</option>
            </x-filters.select>
        </div>
        <div>
            <div class="flex justify-between" wire:change="doSearch">
                <x-filters.select name="filtroSemana" labelText="Semana">
                    <option value="">Semana</option>
                    @foreach ($semanas as $semana)
                        <option value="{{ $semana->SEMANA }}">{{ $semana->SEMANA }} - {{ $semana->AÑO }}</option>
                    @endforeach
                </x-filters.select>
            </div>
        </div>
    </div>
</div>
