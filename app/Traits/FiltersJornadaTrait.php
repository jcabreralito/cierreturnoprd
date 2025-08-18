<?php

namespace App\Traits;

use App\Http\Controllers\SolicitudController;

trait FiltersJornadaTrait
{
    public $filtroDepartamento = '';
    public $filtroMaquina = '';
    public $filtroFolio = '';
    public $filtroEstatus = '';
    public $filtroJornada = '';
    public $filtroMotivo = '';
    public $filtroFecha = '';
    public $filtroHoraInicio = '';
    public $filtroHoraFin = '';
    public $filtroOp = '';
    public $filtroObservaciones = '';
    public $filtroSort = 'personal';
    public $filtroSortType = 'asc';
    public $filtroSemana = '';
    public $filtroPersonal = '';
    public $filtroPersonalNombre = '';
    public $paginationF = 50;
    public $filtroGrupoJornada = '';
    public $filtroConfJornada = '';
    public $filtroExcedente = '';

    /**
     * Función para limpiar los filtros.
     *
     * @return void
     */
    public function clearFilters(): void
    {
        $this->reset([
            'filtroDepartamento',
            'filtroMaquina',
            'filtroEstatus',
            'filtroJornada',
            'filtroMotivo',
            'filtroFecha',
            'filtroHoraInicio',
            'filtroHoraFin',
            'filtroOp',
            'filtroObservaciones',
            'filtroSort',
            'filtroFolio',
            'filtroSortType',
            'filtroPersonalNombre',
            'filtroPersonal',
            'filtroSemana',
            'filtroGrupoJornada',
            'filtroConfJornada',
            'filtroExcedente',
        ]);
    }

    /**
     * Función para asignar los filtros de ordenamiento.
     *
     * @param string $attribute
     * @return void
     */
    public function sort($attribute): void
    {
        $this->filtroSort = $attribute;

        if ($this->filtroSortType == 'asc') {
            $this->filtroSortType = 'desc';
        } else {
            $this->filtroSortType = 'asc';
        }
    }

    /**
     * Función para realizar la búsqueda.
     *
     * @return void
     */
    public function doSearch(): void
    {
        $this->resetPage('personal-jornadas');
    }

    /**
     * Función para colocar los filtros
     *
     * @return array
     */
    public function getFilters(): array
    {
        return [
            'filtroDepartamento' => trim($this->filtroDepartamento),
            'filtroMaquina' => trim($this->filtroMaquina),
            'filtroEstatus' => trim($this->filtroEstatus),
            'filtroMotivo' => trim($this->filtroMotivo),
            'filtroJornada' => trim($this->filtroJornada),
            'filtroFecha' => trim($this->filtroFecha),
            'filtroHoraInicio' => trim($this->filtroHoraInicio),
            'filtroHoraFin' => trim($this->filtroHoraFin),
            'filtroOp' => trim($this->filtroOp),
            'filtroObservaciones' => trim($this->filtroObservaciones),
            'filtroSort' => trim($this->filtroSort),
            'filtroSortType' => trim($this->filtroSortType),
            'filtroFolio' => trim($this->filtroFolio),
            'paginationF' => trim($this->paginationF),
            'filtroSemana' => trim($this->filtroSemana),
            'filtroPersonal' => trim($this->filtroPersonal),
            'filtroPersonalNombre' => trim($this->filtroPersonalNombre),
            'filtroGrupoJornada' => trim($this->filtroGrupoJornada),
            'filtroConfJornada' => trim($this->filtroConfJornada),
            'filtroExcedente' => trim($this->filtroExcedente),
        ];
    }
}
