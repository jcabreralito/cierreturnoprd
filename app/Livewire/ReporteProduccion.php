<?php

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;
use App\Http\Controllers\CierreTurnoController;
use App\Http\Controllers\ReporteProduccionController;
use SebastianBergmann\CodeCoverage\Report\Xml\Report;

class ReporteProduccion extends Component
{
    public $tipo_reporte = '';
    public $operador = '';
    public $maquina = '';
    public $turno = '';
    public $grupo = '';
    public $fecha_desde = '';
    public $fecha_hasta = '';

    public $list = [];
    public $realizarCierre = false;
    public $limpiarBuscadores = false;
    public $reporteActual = [];

    public $modalCreateCierreTurno = false;
    public $color = '';
    public $esBueno = false;
    public $sinResultados = false;
    public $yaRealizoCierre = false;

    // public $observaciones = '';
    // public $acciones_correctivas = '';

    public $password = '';
    public $login = '';

    public $loginOperador = '';
    public $loginSupervisor = '';
    public $passwordOperador = '';
    public $passwordSupervisor = '';

    public $observaciones = [''];
    public $acciones_correctivas = [''];

    public $supervisores = [];
    public $supervisor = '';

    public $filtroSort = 'ID';
    public $filtroSortType = 'asc';

    public $maquinas = [];
    public $operadores = [];
    public $grupos = [];

    /**
     * Montamos algunas variables para que tengan valor
     *
     * @return void
     */
    public function mount(): void
    {
        if (auth()->user()->tipoUsuarioCierreTurno == 3 || auth()->user()->tipoUsuarioCierreTurno == 2) {
            $this->fecha_desde = now()->format('Y-m-d');
            $this->fecha_hasta = now()->format('Y-m-d');
        }

        $this->operadores = (new ReporteProduccionController())->getOperadores();
        $this->maquinas = (new ReporteProduccionController())->getMaquinas();
        $this->grupos = (new ReporteProduccionController())->getGrupos();
    }

    /**
     * Render the component view.
     *
     * @return View
     */
    public function render(): View
    {
        return view('livewire.reporte-produccion')->layout('layouts.main');
    }

    /**
     * Función para obtener data
     *
     * @return void
     */
    public function obtenerData()
    {
        $this->reset([
            'list',
            'reporteActual',
            'color',
            'limpiarBuscadores',
            'sinResultados',
            'realizarCierre',
        ]);

        if (($this->operador == null || $this->operador == '') && $this->tipo_reporte == 'Operador') {
            $this->dispatch('toast', type: 'error', title: 'El campo operador es obligatorio');
            return;
        }

        if (($this->maquina == null || $this->maquina == '') && $this->tipo_reporte == 'Maquina') {
            $this->dispatch('toast', type: 'error', title: 'El campo máquina es obligatorio');
            return;
        }

        if (($this->grupo == null || $this->grupo == '') && $this->tipo_reporte == 'Grupo') {
            $this->dispatch('toast', type: 'error', title: 'El campo grupo es obligatorio');
            return;
        }

        $data = [
            'operador' => $this->operador,
            'maquina' => $this->maquina,
            'turno' => $this->turno,
            'grupo' => $this->grupo,
            'fecha_desde' => $this->fecha_desde,
            'fecha_desde' => $this->fecha_desde,
            'fecha_hasta' => $this->fecha_hasta,
            'tipo_reporte' => $this->tipo_reporte,
        ];

        $this->list = (new ReporteProduccionController())->apiReporte($data);

        $this->reporteActual = (new ReporteProduccionController())->getReporte($data);
        $this->color = $this->getEficienciaColor();

        if (count($this->list) > 0) {
            if (count($this->reporteActual) == 0) {
                $this->dispatch('toast', type: 'info', title: 'No se encontraron datos para el cálculo de eficiencia');
                return;
            }
            $this->dispatch('toast', type: 'success', title: 'Consulta realizada con éxito');
        } else {
            $this->sinResultados = true;
            $this->dispatch('toast', type: 'info', title: 'No se encontraron resultados para la consulta');
        }
    }

    /**
     * Si se actualiza el tipo de reporte se limpia la bandera y el listado
     */
    public function updatedTipoReporte(): void
    {
        $this->realizarCierre = false;
        $this->list = [];

        if ($this->tipo_reporte == 'Operador') {
            $this->maquina = '';
            $this->grupo = '';
        } else if ($this->tipo_reporte == 'Maquina') {
            $this->operador = '';
            $this->grupo = '';
        } else if ($this->tipo_reporte == 'Grupo') {
            $this->maquina = '';
            $this->operador = '';
        }
    }

    /**
     * Función para obtener el color de la eficiencia
     *
     * @return string
     */
    public function getEficienciaColor(): string
    {
        if (count($this->reporteActual) > 0) {
            $global = $this->reporteActual[0]->GLOBAL;
            $convencional = $this->reporteActual[0]->CONVENCIONAL;

            $color = '';

            if ($global == null) {
                if ($convencional < 75) {
                    $color = "#F8696B"; // Rojo
                    $this->esBueno = false;
                } else if ($convencional >= 75 && $convencional < 90) {
                    $color = "#FDD17F";
                    $this->esBueno = false;
                } else if ($convencional >= 90) {
                    $color = "#63BE7B";
                    $this->esBueno = true;
                }
            } else {
                if ($global < 60) {
                    $color = "#F8696B";
                    $this->esBueno = false;
                } else if ($global >= 60 && $global < 70) {
                    $color = "#FDD17F";
                    $this->esBueno = false;
                } else if ($global >= 70) {
                    $color = "#63BE7B";
                    $this->esBueno = true;
                }
            }

            return $color;
        }

        return '#000000'; // Default color
    }

    /**
     * Función para limpiar todos los campos
     *
     * @return void
     */
    public function limpiarCampos(): void
    {
        $this->operador = null;
        $this->maquina = null;
        $this->turno = null;
        $this->fecha_desde = null;
        $this->fecha_hasta = null;

        $this->list = [];
        $this->realizarCierre = false;
        $this->reporteActual = [];
        $this->modalCreateCierreTurno = false;
        $this->color = '';
        $this->esBueno = false;
        $this->limpiarBuscadores = false;
        $this->sinResultados = false;
    }

    /**
     * Función para genrerar el reporte en PDF
     *
     * @return void
     */
    public function generarReportePdf()
    {
        $data = [
            'operador' => $this->operador,
            'maquina' => $this->maquina,
            'turno' => $this->turno,
            'grupo' => $this->grupo,
            'fecha_desde' => $this->fecha_desde,
            'fecha_desde' => $this->fecha_desde,
            'fecha_hasta' => $this->fecha_hasta,
            'tipo_reporte' => $this->tipo_reporte,
        ];

        $this->dispatch('showPdf', data: $data);
    }

}
