<?php

namespace App\Livewire\CierreTurno;

use App\Http\Controllers\CierreTurnoController;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Index extends Component
{
    public $tipo_reporte;
    public $operador = '';
    public $maquina = '';
    public $turno = '';
    public $fecha_cierre = '';

    public $list = [];
    public $realizarCierre = false;
    public $limpiarBuscadores = false;
    public $reporteActual = [];

    public $modalCreateCierreTurno = false;
    public $color = '';
    public $esBueno = false;
    public $sinResultados = false;
    public $yaRealizoCierre = false;

    public $observaciones = '';
    public $acciones_correctivas = '';

    public $password = '';
    public $login = '';

    /**
     * Montamos los registros que se utilizaran para los desplegables
     *
     * @return void
     */
    public function mount(): void
    {
    }

    /**
     * Renderizacion del componente de cierre de turno
     *
     * @return View
     */
    public function render(): View
    {
        return view('livewire.cierre-turno.index', [
            'operadores' => (new CierreTurnoController())->getOperadores(),
            'maquinas' => (new CierreTurnoController())->getMaquinas(),
        ]);
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

        $data = [
            'operador' => $this->operador,
            'maquina' => $this->maquina,
            'turno' => $this->turno,
            'fecha_cierre' => $this->fecha_cierre,
            'tipo_reporte' => $this->tipo_reporte,
        ];

        $this->list = (new CierreTurnoController())->getActividades($data);
        $this->reporteActual = (new CierreTurnoController())->getReporte($data);
        $this->yaRealizoCierre = (new CierreTurnoController())->yaRealizoCierre($data);
        $this->color = $this->getEficienciaColor();
        $this->limpiarBuscadores = true;

        if (count($this->list) > 0) {
            if (count($this->reporteActual) == 0) {
                $this->realizarCierre = false;
                $this->dispatch('toast', type: 'info', title: 'No se encontraron datos para el cálculo de eficiencia, por ende no se puede realizar un cierre de turno');
                return;
            }

            $this->realizarCierre = true;
            $this->dispatch('toast', type: 'success', title: 'Consulta realizada con éxito');
        } else {
            $this->realizarCierre = false;
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
        } else {
            $this->operador = '';
        }
    }

    /**
     * Función para realizar el cierre de turno
     *
     * @return void
     */
    public function realizarCierreAccion(): void
    {
        $this->modalCreateCierreTurno = true;
        $this->resetErrorBag();
        $this->reset([
            'observaciones',
            'acciones_correctivas',
            'login',
            'password'
        ]);
    }

    /**
     * Función para realizar nueva consulta
     *
     * @return void
     */
    public function reiniciarConsulta(): void
    {
        $this->limpiarCampos();
    }

    /**
     * Finalizar el cierre de turno
     *
     * @return void
     */
    public function finalizarCierre(): void
    {
        $this->modalCreateCierreTurno = true;

        $data = [
            'reporte' => [
                'operador' => explode('-', $this->operador)[0],
                'maquina' => $this->maquina,
                'turno' => $this->turno,
                'fecha_cierre' => $this->fecha_cierre,
                'tipo_reporte' => $this->tipo_reporte,
                'estatus' => 1,
                'usuario_cerro' => $this->login,
            ],
            'reporteActual' => $this->reporteActual,
            'razones' => [
                'observaciones' => $this->observaciones,
                'acciones_correctivas' => $this->acciones_correctivas,
            ],
            'contieneRazones' => $this->esBueno
        ];

        $resultado = (new CierreTurnoController())->cerrarTurno($data);
        $this->dispatch('toast', type: 'success', title: 'Cierre de turno exitoso');

        // Limpiamos lo campos
        $this->limpiarCampos();
    }

    /**
     * Función para cerrar el modal de creación de cierre de turno
     *
     * @return void
     */
    public function closeModalCreate(): void
    {
        $this->modalCreateCierreTurno = false;
    }

    /**
     * Función para confirmar cierre
     *
     * @return mixed
     */
    public function confirmarCierre()
    {
        if (!$this->esBueno) {
            $this->validate([
                'observaciones' => 'required|string|max:500',
                'acciones_correctivas' => 'required|string|max:500',
            ], [
                'observaciones.required' => 'El campo observaciones es obligatorio.',
                'acciones_correctivas.required' => 'El campo acciones correctivas es obligatorio.',
            ]);
        }

        $this->dispatch('confirmarCierre', operador: $this->operador);

        $this->modalCreateCierreTurno = false;
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
        $this->tipo_reporte = null;
        $this->operador = null;
        $this->maquina = null;
        $this->turno = null;
        $this->fecha_cierre = null;
        $this->observaciones = null;
        $this->acciones_correctivas = null;
        $this->password = null;

        $this->list = [];
        $this->realizarCierre = false;
        $this->reporteActual = [];
        $this->modalCreateCierreTurno = false;
        $this->color = '';
        $this->esBueno = false;
        $this->limpiarBuscadores = false;
        $this->sinResultados = false;
    }
}
