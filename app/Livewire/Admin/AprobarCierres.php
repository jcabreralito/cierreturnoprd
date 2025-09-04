<?php

namespace App\Livewire\Admin;

use App\Http\Controllers\CausaController;
use App\Http\Controllers\CierreTurnoController;
use App\Http\Controllers\CompromisoController;
use App\Http\Controllers\ReporteController;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class AprobarCierres extends Component
{
    use WithPagination;

    public $filtroFolio;
    public $filtroFechaCierreOperador;
    public $filtroFechaCierreSupervisor;
    public $paginationF = 10;
    public $filtroSort = 'id';
    public $filtroSortType = 'desc';

    public $modalDetalle = false;
    public $reporte;
    public $reporteActual = [];
    public $color = '';
    public $esBueno = false;

    public $causas = [];
    public $compromisos = [];

    /**
     * Función para renderizar la vista de mis cierres
     *
     * @return View
     */
    public function render(): View
    {
        return view('livewire.admin.aprobar-cierres',[
            'reportesRealizados' => (new ReporteController())->getReportesRealizados([
                'folio' => $this->filtroFolio,
                'fecha_cierre_operador' => $this->filtroFechaCierreOperador,
                'fecha_cierre_supervisor' => $this->filtroFechaCierreSupervisor,
                'pagination' => $this->paginationF,
                'sort' => $this->filtroSort,
                'sort_type' => $this->filtroSortType,
            ]),
        ])
        ->layout('layouts.main');
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
     * Función para realizar la búsqueda de los cierres realizados
     *
     * @return void
     */
    public function obtenerData()
    {
        $this->resetPage();
    }

    /**
     * Función para ver el detalle del cierre
     *
     * @param int $id
     * @return void
     */
    public function verDetalle($id)
    {
        $this->modalDetalle = true;
        $this->reporteActual = (new CierreTurnoController())->getDataEficiencia($id);
        $this->color = $this->getEficienciaColor();
        $this->reporte = (new ReporteController())->obtenerReportePorId($id);
        $this->causas = (new CausaController())->obtenerCausas($id);
        $this->compromisos = (new CompromisoController())->obtenerCompromisos($id);
    }

    /**
     * Función para cerrar el modal de detalle
     *
     * @return void
     */
    public function closeModalDetalle()
    {
        $this->modalDetalle = false;
        $this->reporte = null;
    }

    /**
     * Función para limpiar los filtros de búsqueda
     *
     * @return void
     */
    public function limpiarFiltros()
    {
        $this->filtroFolio = null;
        $this->filtroFechaCierreOperador = null;
        $this->filtroFechaCierreSupervisor = null;
        $this->resetPage();
    }

    /**
     * Función para obtener el color de la eficiencia
     *
     * @return string
     */
    public function getEficienciaColor(): string
    {
        if (count($this->reporteActual) > 0) {
            $global = $this->reporteActual[0]['GLOBAL'];
            $convencional = $this->reporteActual[0]['CONVENCIONAL'];

            $color = '';

            if ($global == null) {
                if ($convencional < 60) {
                    $color = "#F8696B"; // Rojo
                    $this->esBueno = false;
                } else if ($convencional >= 60 && $convencional <= 70) {
                    $color = "#FDD17F";
                    $this->esBueno = true;
                } else if ($convencional > 70) {
                    $color = "#63BE7B";
                    $this->esBueno = true;
                }
            } else {
                if ($global < 60) {
                    $color = "#F8696B";
                    $this->esBueno = false;
                } else if ($global >= 60 && $global <= 70) {
                    $color = "#FDD17F";
                    $this->esBueno = false;
                } else if ($global > 70) {
                    $color = "#63BE7B";
                    $this->esBueno = true;
                }
            }

            return $color;
        }

        return '#000000'; // Default color
    }
}
