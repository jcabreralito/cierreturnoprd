<?php

namespace App\Livewire\Solicitud;

use App\Http\Controllers\BitacoraController;
use App\Http\Controllers\DepartamentoController;
use App\Http\Controllers\EstatusController;
use App\Http\Controllers\MaquinaController;
use App\Http\Controllers\MotivoController;
use App\Http\Controllers\PermisoController;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\TurnoController;
use App\Http\Requests\StoreSolicitudRequest;
use App\Http\Requests\UpdateSolicitudRequest;
use App\Traits\AssignPersonalTrait;
use App\Traits\FiltersTrait;
use App\Traits\StoreSolicitudTrait;
use App\Traits\UpdateSolicitudTrait;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    use FiltersTrait;
    use StoreSolicitudTrait;
    use UpdateSolicitudTrait;
    use AssignPersonalTrait;

    public $type = 1; // 1: Guardar, 2: Actualizar

    public $solicitudId;
    public $modalUpdate = false;
    public $inAction = false;
    public $role;

    public $estatus;
    public $departamentos;
    public $turnos;
    public $motivos;
    public $semanas;

    public $permisos = [];

    // Variable para saber el tipo de acción 1 seleccion multiple de colaboradores, 2 multi autorización
    public $tipoAccion = 1;
    public $modeAction = 2; // 1: Selección, 2: No selección
    public $solicitudesPorFinalizar = [];

    // Tipo para distinguir que solicitudes se van a marcar como finalizadas
    // 1: Solicitudes con estatus 1 = pendiente, 2 = registrando, 3 por autorizar
    public $tipoDistinccion = 1;

    /**
     * Función para obtener las reglas de validación.
     *
     * @return array
     */
    public function rules(): array
    {
        if ($this->type == 1) {
            return (new StoreSolicitudRequest)->rules();
        } else {
            return (new UpdateSolicitudRequest)->rules();
        }
    }

    /**
     * Función para obtener los mensajes de validación.
     *
     * @return array
     */
    public function messages(): array
    {
        if ($this->type == 1) {
            return (new StoreSolicitudRequest)->messages();
        } else {
            return (new UpdateSolicitudRequest)->messages();
        }
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
        $this->resetPage('solicitudes');
        $this->resetPage('pesonal-solicitudes');
    }

    /**
     * Función para actualizar las validaciones en tiempo real
     *
     * @param string $field
     * @return void
     */
    public function updated($field): void
    {
        $this->validateOnly($field);
    }

    /**
     * Montamos el componente
     *
     * @return void
     */
    public function mount(): void
    {
        $this->role = auth()->user()->tipoUsuarioHorasExtra;

        // Obtenemos la semana actual de la etl
        $this->filtroSemana = (new SolicitudController())->getSemanaActual();

        $this->resetPage('solicitudes');
        $this->resetPage('pesonal-solicitudes');

        // Obtenemos los permisos del usuario
        $this->permisos = (new PermisoController())->getPermisos();

        $this->estatus = (new EstatusController())->index();
        $this->departamentos = (new DepartamentoController())->index(2);
        $this->turnos = (new TurnoController())->index();
        $this->motivos = (new MotivoController())->index();
        $this->semanas = (new SolicitudController())->getSemanas();
    }

    /**
     * Renderiza la vista de la página de solicitudes.
     *
     * @return View
     */
    public function render(): View
    {
        return view('livewire.solicitud.index', [
            'solicitudes' => (new SolicitudController())->index($this->getFilters()),
            'solicitudesPorPersonal' => (new SolicitudController())->indexPp($this->getFilters()),
            'maquinas' => (new MaquinaController())->index([
                'departamento_id' => $this->getDepartamentoId(),
            ]),
            'listPersonal' => (new SolicitudController())->getPersonal([
                'search' => $this->personalSearch,
                'personales' => $this->getSelectedPersonal(),
            ]),
        ]);
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
        } elseif ($this->type == 1 && $this->departamento_id != null && $this->departamento_id != '') {
            return $this->departamento_id;
        } elseif ($this->type == 2 && $this->departamento_idU != null && $this->departamento_idU != '') {
            // $this->maquina_idU = null;
            return $this->departamento_idU;
        } else {
            return 0;
        }
    }

    /**
     * Función para preguntar el tipo de marcado que desea realizar
     *
     * @param int $tipo
     * @return void
     */
    public function preguntarTipoDeMarcadoW(int $tipo, $opc1, $opc2): void
    {
        $this->tipoDistinccion = $tipo;

        $this->activeDeactiveSeleccion($opc1, $opc2);
    }
}
