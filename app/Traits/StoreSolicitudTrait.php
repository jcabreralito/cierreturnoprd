<?php

namespace App\Traits;

use App\Http\Controllers\BitacoraController;
use App\Http\Controllers\MaquinaController;
use App\Http\Controllers\SolicitudController;

trait StoreSolicitudTrait
{
    public $modalCreate = false;

    public $op;
    public $observaciones;
    public $departamento_id;
    public $maquina_id;
    public $motivo_id;
    public $estatus_id;
    public $user_id;
    public $turno_id;
    public $desde_dia;
    public $num_repeticiones = 1;
    public $horas;
    public $num_max_usuarios = 1;

    public $listFechasFrom = [];
    public $trabajo = '';

    public $listOps = [];

    /**
     * Función para obtener las fechas de inicio de las solicitudes.
     *
     * @return void
     */
    public function getFechasFrom(): void
    {
        // Obtenemos las fechas de inicio desde el dia hasta el numero de repeticiones por ejemplo si la fecha es 2022-10-01 y el numero de repeticiones es 3 entonces las fechas serian 2022-10-01, 2022-10-02, 2022-10-03
        $this->listFechasFrom = [];

        if ($this->num_repeticiones != null && $this->desde_dia != null) {
            for ($i = 0; $i < $this->num_repeticiones; $i++) {
                $this->listFechasFrom[] = date('Y-m-d', strtotime($this->desde_dia . ' + ' . $i . ' days'));
            }
        }
    }

    /**
     * Función para obtener el nombre del trabajo de la op
     *
     * @return void
     */
    public function getNombreTrabajo()
    {
        $trabajo = (new SolicitudController())->getNombreTrabajo($this->op);
        $this->trabajo = $trabajo?->Descricao;
    }

    /**
     * Función para abrir el modal de creación de solicitudes.
     *
     * @return void
     */
    public function openModalCreate(): void
    {
        $this->modalCreate = true;
        $this->inAction = true;
    }

    /**
     * Función para cerrar el modal de creación de solicitudes.
     *
     * @return void
     */
    public function closeModalCreate(): void
    {
        $this->modalCreate = false;
        $this->clearInputs();
    }

    /**
     * Función para limpiar los inputs del formulario.
     *
     * @return void
     */
    public function clearInputs(): void
    {
        $this->reset([
            'observaciones',
            'departamento_id',
            'maquina_id',
            'motivo_id',
            'turno_id',
            'estatus_id',
            'desde_dia',
            'num_repeticiones',
            'horas',
            'trabajo',
            'listFechasFrom',
            'inAction',
            'num_max_usuarios',
            'listOps',
            'op',
            'trabajo',
        ]);
    }

    /**
     * Función para realizar el guardado de la solicitud.
     *
     * @return void
     */
    public function storeSolicitud(): void
    {
        $this->validate();

        // Validamos si se ha seleccionado una op
        if (count($this->listOps) == 0) {
            // Regresamos un mesaje de error para el input de la op de tipo validation
            $this->addError('op', 'Selecciona al menos una OP');
            return;
        }

        try {
            // Iteramos el listado de fechas
            foreach ($this->listFechasFrom as $fecha) {

                // Validamos si la solicitud que se va registrar cuenta con la fecha aun no asignada
                $verificaFecha = (new SolicitudController())->verificaFecha($this->maquina_id, $fecha, $this->turno_id);

                // Si no existe una solicitud con la fecha ya asignada
                if ($verificaFecha) {
                    // Guardamos la solicitud
                    $solicitud = (new SolicitudController())->store([
                        'observaciones' => $this->observaciones,
                        'departamento_id' => $this->departamento_id,
                        'maquina_id' => $this->maquina_id,
                        'motivo_id' => $this->motivo_id,
                        'estatus_id' => $this->estatus_id,
                        'turno_id' => $this->turno_id,
                        'desde_dia' => $fecha,
                        'num_repeticiones' => $this->num_repeticiones,
                        'horas' => $this->horas,
                        'num_max_usuarios' => $this->num_max_usuarios,
                        'tipo' => ($this->role == 3) ? 2 : 1,
                        'ops' => collect($this->listOps)->pluck('op')->toArray(),
                    ]);

                    // Generamos la bitácora
                    (new BitacoraController())->store('Creación de Solicitud', null, 'Solicitud creada', auth()->user()->Id_Usuario, $solicitud->id);

                    // Verificamos si es la ultima iteración del foreach
                    $this->dispatch('toast', message: 'Solicitud para la fecha ' . $fecha . ', guardada correctamente', type: 'success');
                } else {
                    $this->dispatch('toast', message: 'La solicitud ya existe para la fecha: ' . $fecha, type: 'error');
                }
            }

            $this->closeModalCreate();
        } catch (\Exception $e) {
            $this->dispatch('toast', message: 'Error al guardar la solicitud', type: 'error');
        }
    }

    /**
     * Función para actualizar el estado de la solicitud.
     *
     * @param int $id
     * @param int $estatus_id
     * @param $preVal
     * @return void
     */
    public function changeEstatus(int $id, int $estatus_id, $preval): void
    {
        // Validamos si el estatus es nulo o vacío
        if ($estatus_id == null || $estatus_id == '') {
            return;
        }

        // Validamos si la solicitud ya cuenta con todos sus usuarios autorizados
        $solicitudItem = (new SolicitudController())->showV($id);

        if ($solicitudItem->totalPersonalesAutorizados != $solicitudItem->totalPersonalesRelacionados && $solicitudItem->totalPersonalesRelacionados > 0) {
            $this->dispatch('toast', message: 'No se puede cambiar el estatus de la solicitud, ya que no todos los usuarios han sido autorizados.', type: 'error');
            $this->dispatch('refreshFieldStatus', id: $id, preValue: $preval);
            return;
        }

        try {
            // Indicamos que estamos en acción
            $this->inAction = true;

            // Obtenemos la solicitud actualizada
            $solicitud = (new SolicitudController())->show($id);

            // Actualizamos el estatus de la solicitud
            $updatedSolicitud = (new SolicitudController())->updateEstatus($id, $estatus_id);


            // Generamos la bitácora
            (new BitacoraController())->store(
                'Cambio de estatus de la solicitud',
                'Solicitud con ID: ' . $id . ' de ' . $solicitud->estatus?->nombre . ', con estatus: ' . $solicitud->estatus?->estatus,
                'A Solicitud con ID: ' . $id . ' de ' . $solicitud->estatus?->nombre . ', con estatus: ' . $updatedSolicitud->estatus?->estatus,
                auth()->user()->Id_Usuario,
                $id
            );

            // Indicamos que ya no estamos en acción
            $this->inAction = false;

            $this->dispatch('refreshSubrows', ids: [$id], requiredToggle: false);

            $this->dispatch('toast', tipo: 'success', message: 'Estatus actualizado correctamente.', type: 'success');
        } catch (\Exception $e) {
            $this->dispatch('toast', tipo: 'error', message: 'Error al actualizar el estatus.', type: 'error');
        }
    }

    /**
     * Función para insertal la op en la solicitud
     *
     * @return void
     */
    public function addOpToList(): void
    {
        $addedOp = $this->op;
        $trabajo = (new SolicitudController())->getNombreTrabajo($addedOp);
        $trabajoNombre = $trabajo?->Descricao;

        if ($trabajoNombre == null) {
            $this->dispatch('toast', message: 'La OP buscada no existe.', type: 'error');
            return;
        }

        $this->listOps[$trabajo->NumOrdem] = ['op' => $trabajo->NumOrdem, 'trabajo' => $trabajoNombre];

        $this->op = null;
        $this->trabajo = null;
    }

    /**
     * Función para eliminar la op de la lista
     *
     * @param $op
     * @return void
     */
    public function removeOpFromList($op): void
    {
        unset($this->listOps[$op]);
    }

    /**
     * Función para obtener el numero de usuarios maximos por maquina
     *
     * @return void
     */
    public function getMaxNumUsuarios(): void
    {
        $this->num_max_usuarios = (new MaquinaController())->getMaxPersonalMaquina($this->maquina_id);
    }

    /**
     * Función para eliminar la solicitud
     *
     * @param int $id
     * @return void
     */
    public function deleteSolicitudW(int $id): void
    {
        try {
            // Eliminamos la solicitud
            (new SolicitudController())->destroy($id);

            // Generamos la bitácora
            (new BitacoraController())->store('Eliminación de Solicitud', null, 'Solicitud eliminada', auth()->user()->Id_Usuario, $id);

            // Actualizamos el listado de solicitudes
            $this->resetPage('solicitudes');

            $this->dispatch('toast', message: 'Solicitud eliminada correctamente.', type: 'success');
        } catch (\Exception $e) {
            $this->dispatch('toast', message: 'Error al eliminar la solicitud.', type: 'error');
        }
    }
}
