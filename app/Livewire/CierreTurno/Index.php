<?php

namespace App\Livewire\CierreTurno;

use App\Http\Controllers\CierreTurnoController;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Index extends Component
{
    public $tipo_reporte = 'Operador';
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

    /**
     * Montamos algunas variables para que tengan valor
     *
     * @return void
     */
    public function mount(): void
    {
        if (auth()->user()->tipoUsuarioCierreTurno == 3 || auth()->user()->tipoUsuarioCierreTurno == 2) {
            $this->fecha_cierre = now()->format('Y-m-d');
        }
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

        if ($this->operador == null || $this->operador == '') {
            $this->dispatch('toast', type: 'error', title: 'El campo operador es obligatorio');
            return;
        }

        if (count($this->maquinas) >= 2) {
            if ($this->maquina == null || $this->maquina == '') {
                $this->dispatch('toast', type: 'error', title: 'El campo máquina es obligatorio');
                return;
            }
        }

        $data = [
            'operador' => $this->operador,
            'maquina' => $this->maquina == null || $this->maquina == '' ? (count($this->maquinas) > 0 ? $this->maquinas[0] : null) : $this->maquina,
            'turno' => $this->turno,
            'fecha_cierre' => $this->fecha_cierre,
            'tipo_reporte' => $this->tipo_reporte,

            'filtroSort' => $this->filtroSort,
            'filtroSortType' => $this->filtroSortType,
        ];

        $this->obtenerListado();

        // Espacio para validar si es maquina normal o pegadora dentro del listado
        $keysPegadoras = [4001, 4031, 4002, 4032, 4003, 4033, 4004, 4034, 4005, 4035, 4006, 4036];

        $existePegadora = $this->list->filter(function ($item) use ($keysPegadoras) {
            return in_array($item->KeyProceso, $keysPegadoras);
        })->isNotEmpty();

        $data['tipo_reporte_generar'] = ($existePegadora ? 2 : 1);

        $this->reporteActual = (new CierreTurnoController())->getReporte($data);
        $this->yaRealizoCierre = (new CierreTurnoController())->yaRealizoCierre($data);
        $this->color = $this->getEficienciaColor();
        $this->limpiarBuscadores = true;
        $this->supervisores = (new CierreTurnoController())->getSupervisores($this->operador);
        $this->dispatch('cargarSupervisores', supervisores: $this->supervisores);

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
                'firma_supervisor' => $this->loginSupervisor,
                'firma_operador' => $this->loginOperador,
                'tipo_reporte_generar' => $this->reporteActual[0]['Tipo'],
                'supervisor_id' => $this->supervisor,
            ],
            'reporteActual' => $this->reporteActual,
            'razones' => [
                'causas' => $this->observaciones,
                'compromisos' => $this->acciones_correctivas,
            ],
            'contieneRazones' => $this->esBueno
        ];

        $resultado = (new CierreTurnoController())->cerrarTurno($data);
        $this->dispatch('toast', type: 'success', title: 'Cierre de turno exitoso');
        $this->dispatch('limpiarOperador');

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
                'observaciones.*' => 'required|string',
                'acciones_correctivas.*' => 'required|string',
            ], [
                'observaciones.*.required' => 'El campo observaciones es obligatorio.',
                'acciones_correctivas.*.required' => 'El campo acciones correctivas es obligatorio.',
            ]);
        }

        $this->dispatch('confirmarCierre', operador: $this->operador, isRequiredPasswordOperador: !$this->esBueno);

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
            $global = $this->reporteActual[0]['GLOBAL'];

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
     * Función para limpiar todos los campos
     *
     * @return void
     */
    public function limpiarCampos(): void
    {
        $this->operador = null;
        $this->maquina = null;
        $this->turno = null;
        $this->fecha_cierre = null;
        $this->observaciones = [''];
        $this->acciones_correctivas = [''];
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
     * Función para ordenar por columnas
     *
     * @param string $campo
     * @return void
     */
    public function sort($campo): void
    {
        if ($this->filtroSort == $campo) {
            $this->filtroSortType = $this->filtroSortType === 'asc' ? 'desc' : 'asc';
        } else {
            $this->filtroSort = $campo;
            $this->filtroSortType = 'asc';
        }

        $this->obtenerListado();
    }

    /**
     * Función para realizar la búsqueda de los cierres realizados
     *
     * @return void
     */
    public function obtenerListado()
    {
        $data = [
            'operador' => $this->operador,
            'maquina' => $this->maquina,
            'turno' => $this->turno,
            'fecha_cierre' => $this->fecha_cierre,
            'tipo_reporte' => $this->tipo_reporte,

            'filtroSort' => $this->filtroSort,
            'filtroSortType' => $this->filtroSortType,
        ];

        $this->list = (new CierreTurnoController())->getListadoActividades($data);
    }

    /**
     * Función para saber si el operador trabajó en más de una máquina el mismo día
     *
     * @return void
     */
    public function operadorTrabajoEnVariasMaquinas()
    {
        $this->maquina = '';

        // Validamos si la fecha tiene el formato correcto
        if ($this->fecha_cierre != null && $this->fecha_cierre != '') {
            $date = \DateTime::createFromFormat('Y-m-d', $this->fecha_cierre);
            if (!$date || $date->format('Y-m-d') !== $this->fecha_cierre) {
                return;
            }
        }

        if ($this->operador != null && $this->operador != '' && $this->fecha_cierre != null && $this->fecha_cierre != '' && $this->turno != null && $this->turno != '') {
            $listado = (new CierreTurnoController())->getListadoActividades([
                'operador' => $this->operador,
                'maquina' => $this->maquina,
                'turno' => $this->turno,
                'fecha_cierre' => $this->fecha_cierre,
                'tipo_reporte' => $this->tipo_reporte,
            ]);

            if (count($listado) > 0) {
                // Obtener las máquinas asociadas al operador seleccionado del listado
                $this->maquinas = (count($listado) > 0) ? $listado->pluck('Maquina')->unique() : [];
            } else {
                $this->maquinas = [];
            }
        }
    }
}
