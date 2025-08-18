<?php

namespace App\Livewire\CierreTurno;

use App\Http\Controllers\CierreTurnoController;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
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

        dd($this->reporteActual);
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
}
