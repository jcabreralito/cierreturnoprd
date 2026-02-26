<?php

namespace App\Livewire\Admin;

use App\Http\Controllers\CausaController;
use App\Http\Controllers\CierreTurnoController;
use App\Http\Controllers\CompromisoController;
use App\Http\Controllers\DetalleReporteController;
use App\Http\Controllers\DocumentoReporteController;
use App\Http\Controllers\MotivoRechazoController;
use App\Http\Controllers\ReporteController;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class Historico extends Component
{
    use WithPagination;

    public $filtroFolio;
    public $filtroFechaCierreOperador;
    public $filtroFechaCierreSupervisor;
    public $operador;
    public $supervisor;
    public $paginationF = 50;
    public $filtroSort = 'id';
    public $filtroSortType = 'desc';

    public $modalDetalle = false;
    public $reporte;
    public $reporteActual = [];
    public $color = '';
    public $esBueno = false;

    public $causas = [];
    public $compromisos = [];
    public $listadoMotivosRechazo = [];

    public $modalPdf = false;
    public $reportePdf = '';

    /**
     * Función para renderizar la vista de mis cierres
     *
     * @return View
     */
    public function render(): View
    {
        return view('livewire.admin.historico',[
            'cierres' => (new ReporteController())->cierresHistorico([
                'folio' => $this->filtroFolio,
                'fecha_cierre_operador' => $this->filtroFechaCierreOperador,
                'fecha_cierre_supervisor' => $this->filtroFechaCierreSupervisor,
                'operador' => $this->operador,
                'supervisor' => $this->supervisor,
                'pagination' => $this->paginationF,
                'sort' => $this->filtroSort,
                'sort_type' => $this->filtroSortType,
            ]),
            'operadores' => (new CierreTurnoController())->getOperadores(),
            'supervisores' => (new CierreTurnoController())->getSupervisoresGeneral(),
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
        $this->reporteActual = (new DetalleReporteController())->getDetalleReporte($id);
        $this->color = $this->getEficienciaColor();
        $this->reporte = (new ReporteController())->obtenerReportePorId($id);
        $this->causas = (new CausaController())->obtenerCausas($id);
        $this->compromisos = (new CompromisoController())->obtenerCompromisos($id);
        $this->listadoMotivosRechazo = (new MotivoRechazoController())->getMotivosRechazo($id);
    }

    /**
     * Función para cerrar el modal de detalle
     *
     * @return void
     */
    public function closeModalDetalleRecierre()
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
        $this->operador = null;
        $this->supervisor = null;
        $this->resetPage();
        $this->dispatch('limpiarOperador');
    }

    /**
     * Función para realizar el re-cálculo del cierre
     *
     * @param int $id
     * @return void
     */
    public function realizarReCalculo($id)
    {
        (new CierreTurnoController())->reCalculo($id);
        $this->dispatch('toast', type: 'success', message: 'Re-cálculo realizado con éxito.');
    }

    /**
     * Función para obtener el color de la eficiencia
     *
     * @return string
     */
    public function getEficienciaColor(): string
    {
        if ($this->reporteActual) {
            $global = $this->reporteActual?->eficiencia_global;

            $color = '';

            if ($global == 0) {
                $color = "#000000";
                $this->esBueno = true;
            } else if ($global <= 50) {
                $color = "#F8696B";
                $this->esBueno = false;
            } else if ($global > 50 && $global < 70) {
                $color = "#FDD17F";
                $this->esBueno = false;
            } else if ($global >= 70) {
                $color = "#63BE7B";
                $this->esBueno = true;
            }

            return $color;
        }

        return '#000000'; // Default color
    }

    /**
     * Función para ver el PDF del cierre
     *
     * @param int $id
     * @return void
     */
    public function verPdf($id)
    {
        $this->modalPdf = true;
        $this->reporte = (new ReporteController())->obtenerReportePorId($id);
        $this->reportePdf = (new DocumentoReporteController())->obtenerPdf($id);
    }
}
