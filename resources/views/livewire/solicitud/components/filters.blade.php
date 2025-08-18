{{--  Filtros  --}}
<div class="flex items-start bg-[#E9E9E9] py-1 px-4 shadow-md rounded-md mb-5">
    <div class="grid grid-cols-1 lg:grid-cols-12 md:gap-x-4 md:gap-y-0 gap-y-4 w-full">
        <div>
            <div class="flex justify-between">
                <x-filters.input name="filtroFolio" labelText="Folio" wire:keydown.enter="doSearch" class="isNumber" />
            </div>
        </div>
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
            <div class="flex justify-between">
                <x-filters.input name="filtroOp" labelText="OP" wire:keydown.enter="doSearch" />
            </div>
        </div>
        <div>
            <div class="flex justify-between">
                <x-filters.input name="filtroObservaciones" labelText="Observaciones" wire:keydown.enter="doSearch" />
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
            <x-filters.select name="filtroMaquina" labelText="Recursos" wire:change="doSearch">
                <option value="">Recursos</option>
                @foreach ($maquinas as $maquina)
                    <option value="{{ $maquina->id }}">{{ $maquina->maquina }}</option>
                @endforeach
            </x-filters.select>
        </div>
        <div>
            <x-filters.select name="filtroEstatus" labelText="Estatus" wire:change="doSearch">
                <option value="">Estatus</option>
                @foreach ($estatus as $estatusF)
                    <option value="{{ $estatusF->id }}">{{ $estatusF->estatus }}</option>
                @endforeach
            </x-filters.select>
        </div>
        <div>
            <x-filters.select name="filtroMotivo" labelText="Motivo" wire:change="doSearch">
                <option value="">Motivo</option>
                @foreach ($motivos as $motivo)
                    <option value="{{ $motivo->id }}">{{ $motivo->motivo }}</option>
                @endforeach
            </x-filters.select>
        </div>
        <div>
            <x-filters.select name="filtroTurno" labelText="Turno" wire:change="doSearch">
                <option value="">Turno</option>
                @foreach ($turnos as $turno)
                    <option value="{{ $turno->id }}">{{ $turno->turno }}</option>
                @endforeach
            </x-filters.select>
        </div>
        <div>
            <div class="flex justify-between">
                <x-filters.input type="date" name="filtroFecha" labelText="Desde día" wire:change="doSearch" />
            </div>
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
