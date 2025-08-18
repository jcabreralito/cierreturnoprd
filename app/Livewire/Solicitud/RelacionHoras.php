<?php

namespace App\Livewire\Solicitud;

use App\Http\Controllers\PermisoController;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\SolicitudesRelacionController;
use App\Traits\FiltersSolicitudRelacionTrait;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class RelacionHoras extends Component
{
    use WithPagination;
    use FiltersSolicitudRelacionTrait;

    public $permisos = [];
    public $departamentos;
    public $semanas;
    public $gruposJornada;

    /**
     * Función para montar el componente.
     *
     * @return void
     */
    public function mount(): void
    {
        $this->permisos = (new PermisoController())->getPermisos();

        if (!in_array(11, $this->permisos)) {
            redirect()->route('main');
        }

        $this->departamentos = (new SolicitudesRelacionController())->getDepartamentos();

        $this->resetPage('solicitudes-relacion');

        $this->semanas = (new SolicitudController())->getSemanas();

        $this->gruposJornada = (new SolicitudesRelacionController())->getGrupoJornada();
    }

    /**
     * Función para actualizar la paginación.
     *
     * @param int $paginationF
     * @return void
     */
    public function updating()
    {
        $this->resetPage('solicitudes-relacion');
    }

    /**
     * Renderización del componente.
     *
     * @return View
     */
    public function render(): View
    {
        return view('livewire.solicitud.relacion-horas', [
            'solicitudesRelacion' => (new SolicitudesRelacionController)->getSolicitudesRelacion($this->getFiltersSolicitudRelacion()),
        ])->layout('layouts.main');
    }

    /**
     * Función para agregar las horas extras.
     *
     * @param string $anio
     * @param string $numsemana
     * @param string $numempleado
     * @param string $valor
     * @param string $motivo
     * @return void
     */
    public function addHrsFinales($anio, $numsemana, $numempleado, $valor, $motivo): void
    {
        try {
            // Validamos que el valor no sea el mismo que el que ya existe
            $horasFinales = (new SolicitudesRelacionController())->getVHorasFinales($anio, $numsemana, $numempleado);

            $timeFinal = $horasFinales->horas_finales != null ? $horasFinales->horas_finales : $horasFinales->hrsExtraReport;

            if ($horasFinales != null) {
                if ($timeFinal == $valor) {
                    return;
                }
            }

            $response = (new SolicitudesRelacionController())->addHrsFinales($anio, $numsemana, $numempleado, $valor, $motivo);

            if ($response) {
                $this->dispatch('toast', type: 'success', message: 'Horas extras agregadas correctamente');

                $this->resetPage('solicitudes-relacion');
            } else {
                $this->dispatch('resetInput', año: $anio, numsemana: $numsemana, numempleado: $numempleado);

                $this->dispatch('toast', type: 'error', message: 'Error al agregar las horas extras');
            }
        } catch (\Exception $e) {
            $this->dispatch('toast', type: 'error', message: 'Error al agregar las horas extras');
        }
    }
}
