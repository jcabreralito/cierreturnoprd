<?php

namespace App\Livewire\CierreTurno;

use App\Http\Controllers\CausaController;
use App\Http\Controllers\CierreTurnoController;
use App\Http\Controllers\ComentarioController;
use App\Http\Controllers\CompromisoController;
use App\Http\Controllers\DetalleReporteController;
use App\Http\Controllers\DocumentoReporteController;
use App\Http\Controllers\MotivoRechazoController;
use App\Http\Controllers\ReporteController;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class ListaCierres extends Component
{
    use WithPagination;

    public $filtroFolio;
    public $filtroFechaCierreOperador;
    public $filtroFechaCierreSupervisor;
    public $filtroEstatus;
    public $operador;
    public $supervisor;
    public $paginationF = 10;
    public $filtroSort = 'id';
    public $filtroSortType = 'desc';

    public $modalDetalle = false;
    public $reporte;
    public $reporteActual = [];
    public $color = '';
    public $esBueno = false;
    public $estatus;

    public $causas = [];
    public $compromisos = [];
    public $listadoMotivosRechazo = [];

    public $observaciones = [""];
    public $acciones_correctivas = [""];

    public $modalPdf = false;
    public $reportePdf = '';

    /**
     * Función para renderizar la vista de mis cierres
     *
     * @return View
     */
    public function render(): View
    {
        return view('livewire.cierre-turno.lista-cierres',[
            'cierres' => (new ReporteController())->cierres([
                'folio' => $this->filtroFolio,
                'fecha_cierre_operador' => $this->filtroFechaCierreOperador,
                'fecha_cierre_supervisor' => $this->filtroFechaCierreSupervisor,
                'estatus' => $this->filtroEstatus,
                'operador' => $this->operador,
                'supervisor' => $this->supervisor,
                'pagination' => $this->paginationF,
                'sort' => $this->filtroSort,
                'sort_type' => $this->filtroSortType,
            ]),
            'catEstatus' => (new CierreTurnoController())->getEstatus(),
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

        // Si es operador o admin y el estatus es 3 (rechazado)
        // if ((auth()->user()->tipoUsuarioCierreTurno == 1 || auth()->user()->tipoUsuarioCierreTurno == 3 ) && $this->reporte->estatus == 3 && (count($this->causas) > 0 || count($this->compromisos) > 0)) {
        // }
        $this->observaciones = array_values($this->causas->pluck('causa')->toArray());
        $this->acciones_correctivas = array_values($this->compromisos->pluck('compromiso')->toArray());
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
        $this->filtroEstatus = null;
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
     * Función para firmar el cierre por parte del supervisor
     *
     * @param int $id
     * @param int $previoEstatus
     * @return void
     */
    public function firmarSupervisor($id, $previoEstatus)
    {
        $this->dispatch('firmarSupervisor', id: $id, previoEstatus: $previoEstatus);
    }

    /**
     * Función para finalizar la firma del cierre por parte del supervisor
     *
     * @param int $id
     * @param string $supervisor
     * @return void
     */
    public function finalizarFirmaSupervisor($id, $supervisor)
    {
        (new CierreTurnoController())->finalizarFirmaSupervisor($id, $supervisor);
        $this->dispatch('toast', type: 'success', message: 'Cierre firmado con éxito.');
    }

    /**
     * Función para rechazar el cierre por parte del supervisor
     *
     * @param int $id
     * @param string $motivoRechazo
     * @return void
     */
    public function rechazarCierreTurno($id, $motivoRechazo)
    {
        (new CierreTurnoController())->rechazarCierreTurno($id, $motivoRechazo);
        $this->dispatch('toast', type: 'success', message: 'Cierre marcado como rechazado.');
    }

    /**
     * Función para corregir el cierre
     *
     * @return void
     */
    public function corregir()
    {
        (new CierreTurnoController())->corregirCierre($this->reporte->id, $this->observaciones, $this->acciones_correctivas);
        $this->dispatch('toast', type: 'success', message: 'Cierre corregido con éxito.');
        $this->observaciones = [""];
        $this->acciones_correctivas = [""];
        $this->modalDetalle = false;
    }

    /**
     * Agrega una nueva observación.
     *
     * @return void
     */
    public function addObservacion()
    {
        $this->observaciones[] = '';
    }

    /**
     * Elimina una observación existente.
     *
     * @param int $index
     * @return void
     */
    public function removeObservacion($index)
    {
        unset($this->observaciones[$index]);
        $this->observaciones = array_values($this->observaciones);
    }

    /**
     * Agrega una nueva acción correctiva.
     *
     * @return void
     */
    public function addAccionCorrectiva()
    {
        $this->acciones_correctivas[] = '';
    }

    /**
     * Elimina una acción correctiva existente.
     *
     * @param int $index
     * @return void
     */
    public function removeAccionCorrectiva($index)
    {
        unset($this->acciones_correctivas[$index]);
        $this->acciones_correctivas = array_values($this->acciones_correctivas);
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

    /**
     * Función para guardar los comentarios del cierre
     *
     * @param $id
     * @param $comments
     * @return void
     */
    public function guardarComentario($id, $comments)
    {
        (new ComentarioController())->guardarComentario($id, $comments);
        $this->resetPage();
        $this->dispatch('toast', type: 'success', message: 'Comentarios agregados con éxito.');
    }
}
