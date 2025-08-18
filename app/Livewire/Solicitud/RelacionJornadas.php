<?php

namespace App\Livewire\Solicitud;

use App\Http\Controllers\DepartamentoController;
use App\Http\Controllers\EstatusController;
use App\Http\Controllers\JornadasController;
use App\Http\Controllers\MaquinaController;
use App\Http\Controllers\MotivoController;
use App\Http\Controllers\PermisoController;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\TurnoController;
use App\Traits\FiltersJornadaTrait;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class RelacionJornadas extends Component
{
    use WithPagination;
    use FiltersJornadaTrait;

    public $permisos;
    public $role;

    public $estatus;
    public $departamentos;
    public $turnos;
    public $motivos;
    public $semanas;
    public $semanaActual;
    public $semanaPrevia;

    // Tipo para distinguir que solicitudes se van a marcar como finalizadas
    // 1: Solicitudes con estatus 1 = pendiente, 2 = registrando, 3 por autorizar
    public $tipoDistinccion = 1;

    // Variable bandera para saber se se inicio el registro de jornadas
    public $banderaRegistroJornadas = false;
    public $banderaRegistrosRegistrados = false;

    public $catJornadas;
    public $catGpoJornadas;
    public $catConfigJornadas;
    public $catConfigJornadasTbl;

    public $modalConfigJornada = false;

    /**
     * Función para montar el componente.
     *
     * @return void
     */
    public function mount(): void
    {
        $this->role = auth()->user()->tipoUsuarioHorasExtra;

        // Obtenemos los permisos del usuario
        $this->permisos = (new PermisoController())->getPermisos();

        // permisos
        if (!in_array(12, $this->permisos)) {
            redirect()->route('main');
        }

        // Reseteamos la paginación
        $this->resetPage('personal-jornadas');

        $this->estatus = (new EstatusController())->index();
        $this->departamentos = (new DepartamentoController())->index();
        $this->turnos = (new TurnoController())->index();
        $this->motivos = (new MotivoController())->index();
        $this->semanas = (new SolicitudController())->getSemanas();

        // Obtenemos la semana actual de la etl
        $this->filtroSemana = (new SolicitudController())->getSemanaActual();

        $this->catJornadas = (new JornadasController())->obtenerCatJornadas();
        $this->catGpoJornadas = (new JornadasController())->obtenerCatGpoJornadas();

        $this->semanaActual = (new SolicitudController())->getSemanaActualNormal();
        $this->semanaPrevia = (new SolicitudController())->getSemanaPasada();

        $this->catConfigJornadas = (new JornadasController())->obtenerCatConfigJornadas();
        $this->catConfigJornadasTbl = (new JornadasController())->obtenerCatConfigJornadasTbl();

        // Verificamos si existen registros de jornadas para la semana actual
        $this->banderaRegistrosRegistrados = (new JornadasController())->verificarSiExistenRegistrosJornadas($this->semanaActual);
    }

    /**
     * Función para actualizar la paginación
     *
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function updating($name, $value): void
    {
        $this->resetPage('personal-jornadas');
    }

    /**
     * Renderización del componente.
     *
     * @return View
     */
    public function render(): View
    {
        return view('livewire.solicitud.relacion-jornadas', [
            'solicitudesPorPersonal' => (new SolicitudController())->indexPpNotSolicitudes($this->getFilters()),
            'diasConf' => (new JornadasController())->getCatalogoConfiguraciones(),
            'maquinas' => (new MaquinaController())->index([
                'departamento_id' => $this->getDepartamentoId(),
            ]),
        ])->layout('layouts.main');
    }

    /**
     * Función para obtener el departamento seleccionado.
     *
     * @return int
     */
    public function getDepartamentoId(): int
    {
        if ($this->filtroDepartamento != null && $this->filtroDepartamento != '') {
            return $this->filtroDepartamento;
        } else {
            return 0;
        }
    }

    /**
     * Función para cambiar la jornada.
     *
     * @param $jornadaId
     * @param $valor
     * @param $dia
     * @param $prefix
     * @return void
     */
    public function changeJornada($jornadaId, $valor, $dia, $prefix): void
    {
        try {
            $response = (new JornadasController())->actualizarJornada($jornadaId, $valor, $dia);

            // Validamos si ya se finalizo la asignacion de la semana
            $verificarSemana = (new JornadasController())->verificarSiFinalizoSemana($jornadaId);

            // validamos si el cambio no excede el limite de horas por ley
            $responseVal = (new JornadasController())->validarHorasPorLey($jornadaId, $valor, $dia, $response['previo']);

            if ($response['response'] && $verificarSemana && $responseVal['response']) {
                $this->dispatch('toast', type: 'success', message: 'Jornada asignada correctamente');
                $this->dispatch('refreshData', id: $jornadaId);
            } else {
                if (!$responseVal['response']) {
                    $this->dispatch('refreshRow', id: $jornadaId, value: $response['previo'], prefix: $prefix);
                    $this->dispatch('toast', type: 'error', message: $responseVal['message'] ?? 'Error al cambiar la jornada, excede el límite de horas por ley');
                    return;
                }

                $this->dispatch('toast', type: 'error', message: $responseVal['message'] ?? 'Error al cambiar la jornada');
            }
        } catch (\Exception $e) {
            $this->dispatch('toast', type: 'error', message: 'Error al cambiar el estatus de la jornada');
        }
    }

    /**
     * Función para colocar la configuración de la jornada.
     *
     * @param $jornadaId
     * @param $valor
     * @return void
     */
    public function changeConfigJornada($jornadaId, $valor): void
    {
        try {
            $response = (new JornadasController())->actualizarConfigJornada($jornadaId, $valor);

            if ($response['response']) {
                $this->dispatch('toast', type: 'success', message: 'Configuración de jornada asignada correctamente');
                $this->dispatch('refreshDataConfig', id: $jornadaId, data: $response['data']);
            } else {
                $this->dispatch('toast', type: 'error', message: 'Error al cambiar la configuración de la jornada');
            }
        } catch (\Exception $e) {
            $this->dispatch('toast', type: 'error', message: 'Error al cambiar la configuración de la jornada');
        }
    }

    /**
     * Función para generar el reporte de jornadas.
     *
     * @return void
     */
    public function generarExcel(): void
    {
        $this->dispatch('generarReporteJornadas', filters: $this->getFilters(), semana: (($this->filtroSemana != null) ? $this->filtroSemana : $this->semanaActual), nombreDocumento: 'reporte_jornadas_' . (($this->filtroSemana != null) ? $this->filtroSemana : $this->semanaActual) . '.xlsx');
    }
}
