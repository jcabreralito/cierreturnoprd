<?php

namespace App\Traits;

trait FiltersSolicitudRelacionTrait
{
    public $filtroSort = 'año';
    public $filtroSortType = 'desc';
    public $paginationF = 50;

    public $filtroAnio;
    public $filtroNumSemana;
    public $filtroDepartamento;
    public $filtroPersonal;
    public $filtroEmpleado;
    public $filtroGrupoJornada;

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
        $this->resetPage('solicitudes-relacion');
    }

    /**
     * Función para limpiar los filtros
     *
     * @return void
     */
    public function clearFilters()
    {
        $this->reset([
            'filtroAnio',
            'filtroNumSemana',
            'filtroDepartamento',
            'filtroPersonal',
            'filtroEmpleado',
            'filtroGrupoJornada',
        ]);
    }

    /**
     * Función para colocar los filtros
     *
     * @return array
     */
    public function getFiltersSolicitudRelacion()
    {
        return [
            'paginationF' => $this->paginationF,
            'filtroSort' => $this->filtroSort,
            'filtroSortType' => $this->filtroSortType,
            'filtroAnio' => $this->filtroAnio,
            'filtroNumSemana' => $this->filtroNumSemana,
            'filtroDepartamento' => $this->filtroDepartamento,
            'filtroPersonal' => $this->filtroPersonal,
            'filtroEmpleado' => $this->filtroEmpleado,
            'filtroGrupoJornada' => $this->filtroGrupoJornada,
        ];
    }
}
