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
    public $reporteActual = [];

    public $modalCreateCierreTurno = false;
    public $color = '';
    public $esBueno = false;

    public $observaciones = '';
    public $acciones_correctivas = '';
    public $password = '';

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
        $data = [
            'operador' => $this->operador,
            'maquina' => $this->maquina,
            'turno' => $this->turno,
            'fecha_cierre' => $this->fecha_cierre,
            'tipo_reporte' => $this->tipo_reporte,
        ];

        $this->list = (new CierreTurnoController())->getActividades($data);
        $this->reporteActual = (new CierreTurnoController())->getReporte($data);
        $this->color = $this->getEficienciaColor();

        if ($this->list && $this->reporteActual) {
            $this->realizarCierre = true;
        }
    }

    /**
     * Si se actualiza el tipo de reporte se limpia la bandera y el listado
     */
    public function updatedTipoReporte(): void
    {
        $this->realizarCierre = false;
        $this->list = [];
    }

    /**
     * Función para realizar el cierre de turno
     *
     * @return void
     */
    public function realizarCierreAccion(): void
    {
        $this->modalCreateCierreTurno = true;
    }

    /**
     * Finalizar el cierre de turno
     *
     * @return void
     */
    public function finalizarCierre(): void
    {
        // Validamos
        $this->validate([
            'observaciones' => 'required|string|max:500',
            'acciones_correctivas' => 'required|string|max:500',
            'password' => 'required|string',
        ], [
            'observaciones.required' => 'El campo observaciones es obligatorio.',
            'acciones_correctivas.required' => 'El campo acciones correctivas es obligatorio.',
            'password.required' => 'El campo contraseña es obligatorio.',
        ]);

        // Validamos que la contraseña ingresada sea del usuario que va realizar la acción
        if ($this->password !== auth()->user()->Password) {
            $this->addError('password', 'La contraseña ingresada es incorrecta.');
            return;
        }

        $data = [
            'reporte' => [
                'operador' => explode('-', $this->operador)[0],
                'maquina' => $this->maquina,
                'turno' => $this->turno,
                'fecha_cierre' => $this->fecha_cierre,
                'tipo_reporte' => $this->tipo_reporte,
                'estatus' => 1,
            ],
            'reporteActual' => $this->reporteActual,
            'razones' => [
                'observaciones' => $this->observaciones,
                'acciones_correctivas' => $this->acciones_correctivas,
            ]
        ];

        $resultado = (new CierreTurnoController())->cerrarTurno($data);
        $this->dispatch('toast', icon: 'success', title: 'Cierre de turno exitoso');

        // Limpiamos lo campos
        $this->reset([
            'modalCreateCierreTurno',
            'observaciones',
            'acciones_correctivas',
            'password',
        ]);
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
}
