<?php

namespace App\Livewire\Admin;

use App\Http\Controllers\ReporteController;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class ReCierre extends Component
{
    public $fechaCierre;
    public $turno;
    public $operador;

    /**
     * Función para renderizar la vista del componente.
     *
     * @return View
     */
    public function render(): View
    {
        return view('livewire.admin.re-cierre', [
            'cierresRealizados' => (new ReporteController())->getReportesRealizados([
                'fecha_cierre' => $this->fechaCierre,
                'turno' => $this->turno,
                'operador' => $this->operador,
            ])
        ])->layout('layouts.main');
    }
}
