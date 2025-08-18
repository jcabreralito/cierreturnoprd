<?php

namespace App\Traits;

use App\Http\Controllers\BitacoraController;
use App\Http\Controllers\MaquinaController;
use App\Http\Controllers\SolicitudController;

trait UpdateSolicitudTrait
{
    public $modalCreate = false;

    public $observacionesU;
    public $departamento_idU;
    public $maquina_idU;
    public $motivo_idU;
    public $estatus_idU;
    public $horasU;
    public $num_max_usuariosU = 1;
    public $opU;
    public $trabajoU = '';
    public $solicitudUpdate;

    public $listOpsU = [];

    /**
     * Función para abrir el modal de modificación de solicitudes.
     *
     * @return void
     */
    public function openModalEdit(int $id): void
    {
        $this->clearInputsUpdate();

        $this->solicitudUpdate = (new SolicitudController())->show($id);

        $this->type = 2;

        if ($this->solicitudUpdate != null) {
            $this->solicitudId = $id;
            $this->observacionesU = $this->solicitudUpdate->observaciones;

            $this->maquinas = (new MaquinaController())->index([
                'departamento_id' => $this->solicitudUpdate->departamento_id,
            ]);

            $this->departamento_idU = $this->solicitudUpdate->departamento_id;
            $this->maquina_idU = $this->solicitudUpdate->maquina_id;

            $this->motivo_idU = $this->solicitudUpdate->motivo_id;
            $this->estatus_idU = $this->solicitudUpdate->estatus_id;
            $this->horasU = $this->solicitudUpdate->horas;
            $this->num_max_usuariosU = $this->solicitudUpdate->num_max_usuarios;

            // Obtenemos las ops asociadas a la solicitud
            $ops = (new SolicitudController())->getOps([
                'solicitud_id' => $this->solicitudId,
            ]);

            foreach ($ops as $key => $op) {
                $this->listOpsU[$op->NumOrdem] = [
                    'op' => $op->NumOrdem,
                    'trabajo' => $op->Descricao,
                ];
            }
        }

        $this->modalUpdate = true;
    }

    /**
     * Función para obtener el nombre del trabajo de la op
     *
     * @return void
     */
    public function getNombreTrabajoU()
    {
        $trabajo = (new SolicitudController())->getNombreTrabajo($this->opU);
        $this->trabajoU = $trabajo?->Descricao;
    }

    /**
     * Función para remover la OP seleccionada.
     *
     * @return void
     */
    public function removeOpFromListU($op): void
    {
        unset($this->listOpsU[$op]);
    }

    /**
     * Función para insertal la op en la solicitud
     *
     * @return void
     */
    public function addOpToListU(): void
    {
        $addedOp = $this->opU;
        $trabajo = (new SolicitudController())->getNombreTrabajo($addedOp);
        $trabajoNombre = $trabajo?->Descricao;

        if ($trabajoNombre == null) {
            $this->dispatch('toast', tipo: 'error', message: 'La OP buscada no existe.', type: 'error');
            return;
        }

        $this->listOpsU[$trabajo->NumOrdem] = ['op' => $trabajo->NumOrdem, 'trabajo' => $trabajoNombre];

        $this->opU = null;
        $this->trabajoU = null;
    }

    /**
     * Función para cerrar el modal de creación de solicitudes.
     *
     * @return void
     */
    public function closeModalUpdate(): void
    {
        $this->modalUpdate = false;
        $this->clearInputsUpdate();
    }

    /**
     * Función para limpiar los inputs del formulario.
     *
     * @return void
     */
    public function clearInputsUpdate(): void
    {
        $this->reset([
            'observacionesU',
            'departamento_idU',
            'maquina_idU',
            'motivo_idU',
            'estatus_idU',
            'horasU',
            'num_max_usuariosU',
            'opU',
            'type',
            'trabajoU',
            'solicitudUpdate',
            'listOpsU',
        ]);

        $this->resetErrorBag();
    }

    /**
     * Función para realizar el guardado de la solicitud.
     *
     * @return void
     */
    public function updateSolicitud(): void
    {
        // Validamos los campos
        $this->validate();

        $solicitudV = (new SolicitudController())->showV($this->solicitudId);

        // Validamos que el numero maximo de usuarios no sea mayor al numero de usuarios ya registrados
        if ($this->num_max_usuariosU < $solicitudV->totalPersonalesRelacionados) {
            $this->dispatch('toast', message: 'El número máximo de usuarios no puede ser menor al número de usuarios ya registrados.', type: 'error');
            return;
        }

        // Validamos que la solicitud no cambie a menos horas de las que ya tiene
        if ($this->horasU < $solicitudV->maxHorasExtrasPersonal) {
            $this->dispatch('toast', message: 'El número de horas no puede ser menor al número de horas ya registradas.', type: 'error');
            return;
        }

        // Validamos si se ha seleccionado una op
        if (count($this->listOpsU) == 0) {
            // Regresamos un mesaje de error para el input de la op de tipo validation
            $this->addError('opU', 'Selecciona al menos una OP');
            return;
        }

        try {
            // Hacemos lo mimsmo con las ops
            $opsAnteriores = (new SolicitudController())->getOps([
                'solicitud_id' => $this->solicitudId,
            ]);

            $cambiosOpsAnteriores = $opsAnteriores->pluck('NumOrdem')->toArray();
            $cambiosOpsNuevos = collect($this->listOpsU)->pluck('op')->toArray();

            // Actualizamos la solicitud
            $solicitudUpdated = (new SolicitudController())->update([
                'observaciones' => $this->observacionesU,
                'departamento_id' => $this->departamento_idU,
                'maquina_id' => $this->maquina_idU,
                'motivo_id' => $this->motivo_idU,
                'estatus_id' => $this->estatus_idU,
                'horas' => $this->horasU,
                'num_max_usuarios' => $this->num_max_usuariosU,
                'ops' => collect($this->listOpsU)->pluck('op')->toArray(),
                'solicitud_id' => $this->solicitudId,
            ], $this->solicitudUpdate->id);

            $cambiosAnteriores = $this->solicitudUpdate->toArray();
            $cambiosNuevos = $solicitudUpdated->toArray();

            // Obtén las diferencias entre los arrays
            $diferencias = array_diff_assoc($cambiosNuevos, $cambiosAnteriores);

            // Obtenemos solo los cambios anteriores de acuerdo con los cambios nuevos
            $cambiosAnteriores = array_intersect_key($cambiosAnteriores, $diferencias);

            // Filtramos los cambios nuevos para que solo contengan los que han cambiado
            $diferencias = array_intersect_key($diferencias, $cambiosNuevos);

            // Convertimos los cambios a JSON para almacenarlos o mostrarlos
            $cambiosAnterioresJson = implode(', ', array_map(
                fn($key, $value) => "$key: $value",
                array_keys($cambiosAnteriores),
                $cambiosAnteriores
            ));

            // Convierte las diferencias a JSON para almacenarlas o mostrarlas
            $diferenciasJson = implode(', ', array_map(
                fn($key, $value) => "$key: $value",
                array_keys($diferencias),
                $diferencias
            ));

            // Comparar las OPs anteriores y nuevas
            $cambiosOpsAnterioresJson = implode(', ', array_map(
                fn($key, $value) => "$key: $value",
                array_keys($cambiosOpsAnteriores),
                $cambiosOpsAnteriores
            ));

            $cambiosOpsNuevosJson = implode(', ', array_map(
                fn($key, $value) => "$key: $value",
                array_keys($cambiosOpsNuevos),
                $cambiosOpsNuevos
            ));

            // Combinamos los cambios de las OPs con los cambios de la solicitud
            $cambiosAnterioresJson .= ', OPs anteriores: ' . $cambiosOpsAnterioresJson;
            $diferenciasJson .= ', OPs nuevas: ' . $cambiosOpsNuevosJson;

            // Ejemplo de uso en la bitácora
            (new BitacoraController())->store(
                'Modificación de Solicitud',
                'Solicitud antes de la modificación: ' . $cambiosAnterioresJson,
                'Cambios realizados: ' . $diferenciasJson,
                auth()->user()->Id_Usuario,
                $this->solicitudId,
            );

            // Cerramos el modal
            $this->closeModalUpdate();

            // Mostramos el mensaje de éxito
            $this->dispatch('toast', message: 'Solicitud guardada correctamente', type: 'success');

            // Emitimos el evento de actualización
            $this->dispatch('refreshSubrows', ids: [$this->solicitudId], requiredToggle: false);
        } catch (\Exception $e) {
            $this->dispatch('toast', message: 'Error al guardar la solicitud', type: 'error');
        }
    }
}
