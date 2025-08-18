<?php

namespace App\Traits;

use App\Http\Controllers\BitacoraController;
use App\Http\Controllers\SolicitudController;

trait AssignPersonalTrait
{
    public $solicitudesSeleccionadas = [];

    public $modalSelectPersonal = false;
    public $selectedPersonal = [];
    public $personalSearch = '';
    public $mode = 2; // 1: Selección, 2: No selección
    public $typeSp = 1; // 1: Guardar, 2: Actualizar
    public $idSubrow; // id de la subrow
    public $numMaximoPermitido; // numero de personal maximo permitido

    public $horasReportables = []; // horas reportables

    // Folios
    public $folios = []; // folios de las solicitudes seleccionadas

    /**
     * Función para que al dar enter en el campo de búsqueda de personal se seleccione el primer registro y se agregue a la lista de personal seleccionado.
     *
     * @return void
     */
    public function searchAndAdd()
    {
        $personal = (new SolicitudController())->searchPersonal($this->personalSearch, $this->getSelectedPersonal());

        if ($personal != null) {
            $this->selectPersonal($personal->Personal, $personal->nombreCompleto);

            $this->personalSearch = '';
        }
    }

    /**
     * Función para abrir el modal para agregar personal a la solicitud.
     *
     * @return void
     */
    public function addPersonal(): void
    {
        // Validar si hay solicitudes seleccionadas
        if (count($this->solicitudesSeleccionadas) == 0) {
            $this->dispatch('toast', type: 'error', message: 'Selecciona al menos una solicitud');
            return;
        }

        // Validar si el numero de personal maximo permitido es igual para todas las solicitudes seleccionadas
        $isValid = (new SolicitudController())->validateMaxPersonal($this->solicitudesSeleccionadas);

        $this->folios = (new SolicitudController())->getFoliosBySolicitud($this->solicitudesSeleccionadas);

        // Verificamos si el numero de personal maximo permitido es igual para todas las solicitudes seleccionadas
        if (!$isValid) {
            $this->dispatch('toast', type: 'error', message: 'El número de personal máximo permitido no es igual para todas las solicitudes seleccionadas');
            return;
        }

        $this->modalSelectPersonal = true;

        $this->typeSp = 1;

        // Limpiamos los campos
        $this->reset([
            'selectedPersonal',
            'personalSearch',
            'numMaximoPermitido',
        ]);

        // Obtenemos el numero de personal maximo permitido de las solicitudes seleccionadas
        $this->numMaximoPermitido = (new SolicitudController())->getMaxPersonal($this->solicitudesSeleccionadas);
    }

    /**
     * Función para seleccionar personal.
     *
     * @param $personal
     * @param $nombre
     * @return void
     */
    public function selectPersonal($personal, $nombre): void
    {
        // Verificamos si el numero de personal ya fue seleccionado
        if (count($this->selectedPersonal) >= $this->numMaximoPermitido) {
            $this->dispatch('toast', type: 'error', message: 'Número de personal máximo permitido alcanzado');
            return;
        }

        // Verificamos que el personal que se seleccionara no este en otra solicitud para el mismo dia
        $personalExist = (new SolicitudController())->validatePersonal($this->solicitudesSeleccionadas, $personal);

        if ($personalExist) {
            $this->dispatch('toast', type: 'error', message: 'El personal ya fue seleccionado para otra solicitud en el mismo día');
            return;
        }

        $this->selectedPersonal[trim($personal)] = ['personal' => trim($personal), 'nombre' => $nombre];
    }

    /**
     * Función para eliminar personal seleccionado.
     *
     * @param $index
     * @return void
     */
    public function deletePersonal($index): void
    {
        // Eliminar el personal seleccionado
        unset($this->selectedPersonal[$index]);
    }

    /**
     * Función para cerrar el modal de selección de personal.
     *
     * @return void
     */
    public function closeModalAsign(): void
    {
        $this->reset([
            'selectedPersonal',
            'personalSearch',
            'modalSelectPersonal',
        ]);
    }

    /**
     * Función para guardar la relación de personal con la solicitud.
     *
     * @return void
     */
    public function storeAsignColaborador(): void
    {
        // Validamos que se haya seleccionado personal
        if (count($this->selectedPersonal) == 0) {
            $this->dispatch('toast', type: 'error', message: 'Selecciona al menos un personal');
            return;
        }

        // Guardamos la relación de personal con la solicitud
        $response = (new SolicitudController())->storeAsignColaborador([
            'ids' => $this->solicitudesSeleccionadas,
            'personal' => collect($this->selectedPersonal)->pluck('personal')->toArray(),
        ]);

        // Verificamos si se guardó correctamente
        if ($response) {
            $this->dispatch('refreshSubrows', ids: $this->solicitudesSeleccionadas, requiredToggle: false);

            $this->dispatch('toast', type: 'success', message: 'Personal asignado correctamente');
            $this->reset([
                'selectedPersonal',
                'personalSearch',
                'solicitudesSeleccionadas',
                'mode'
            ]);
            $this->closeModalAsign();
        } else {
            $this->dispatch('toast', type: 'error', message: 'Error al asignar el personal');
        }
    }

    /**
     * Función para abrir el modal para mostrar el listado de personal asignado.
     *
     * @param $solicitudId
     * @return void
     */
    public function openModalShowPersonal($solicitudId): void
    {
        $this->folios = (new SolicitudController())->getFoliosBySolicitud([$solicitudId]);
        $this->solicitudesSeleccionadas = [$solicitudId];
        $this->idSubrow = $solicitudId;
        // Obtenemos el personal asignado a la solicitud
        $personales = (new SolicitudController())->getPersonalAsignado($solicitudId);

        $this->selectedPersonal = [];

        // Ajustamos los datos
        foreach ($personales as $personal) {
            $this->selectedPersonal[$personal['personal']] = ['personal' => $personal['personal'], 'nombre' => $personal['nombre']];
        }

        $this->modalSelectPersonal = true;

        $this->typeSp = 2;

        // Obtenemos el numero de personal maximo permitido de las solicitudes seleccionadas
        $this->numMaximoPermitido = (new SolicitudController())->getMaxPersonal($this->solicitudesSeleccionadas);
    }

    /**
     * Función para actualizar el listado de personal asignado.
     *
     * @return void
     */
    public function updateAsignColaborador(): void
    {
        // Guardamos la relación de personal con la solicitud
        $response = (new SolicitudController())->updateAsignColaborador([
            'folios' => $this->solicitudesSeleccionadas,
            'personal' => collect($this->selectedPersonal)->pluck('personal')->toArray(),
        ]);

        // Verificamos si se guardó correctamente
        if ($response) {
            $this->dispatch('refreshSubrows', ids: $this->solicitudesSeleccionadas, requiredToggle: false);

            $this->dispatch('toast', type: 'success', message: 'Personal actualizado correctamente');
            $this->reset([
                'selectedPersonal',
                'personalSearch',
                'solicitudesSeleccionadas',
                'mode'
            ]);
            $this->closeModalAsign();
        } else {
            $this->dispatch('toast', type: 'error', message: 'Error al asignar el personal');
        }
    }

    /**
     * Función para activar la seleccion
     *
     * @param $mode 1: Selección, 2: No selección
     * @param $tipo 1: Asignar, 2: Autorizar
     * @return void
     */
    public function activeDeactiveSeleccion($mode, $tipo): void
    {
        if ($this->role == 2) {
            $this->tipoDistinccion = 3;
        }

        if ($tipo == 1) {
            $this->mode = $mode;
        } else {
            $this->modeAction = $mode;
        }

        $this->tipoAccion = $tipo;

        // Verificamos si el tipo es 1

        if ($tipo == 1) {
            $this->solicitudesSeleccionadas = [];

            if ($mode == 1) {
                $this->dispatch('toast', type: 'info', message: 'Modo selección activado');
            } else {
                // Limpiamos la selección
                $this->reset([
                    'selectedPersonal',
                    'personalSearch',
                    'solicitudesSeleccionadas',
                ]);
                $this->dispatch('toast', type: 'info', message: 'Modo selección desactivado');
            }
        } elseif ($tipo == 2) {
            $this->solicitudesPorFinalizar = [];

            if ($mode == 1) {
                $this->dispatch('toast', type: 'info', message: 'Modo selección activado');
            } else {
                // Limpiamos la selección
                $this->reset([
                    'selectedPersonal',
                    'personalSearch',
                    'solicitudesPorFinalizar',
                ]);
                $this->dispatch('toast', type: 'info', message: 'Modo selección desactivado');
            }
        }

    }

    /**
     * Función para actualizar el estado de la relacion de personal con la solicitud.
     *
     * @param $solicitudId
     * @param $estatus
     * @param $folio
     * @param $nh
     * @return void
     */
    public function changeEstatusPersonal($solicitudId, $estatus, $folio, $nh): void
    {
        // Obtenemos el registro de la solicitud
        $solicitudP = (new SolicitudController())->getSolicitudPersonal($solicitudId);

        // Guardamos la relación de personal con la solicitud
        $response = (new SolicitudController())->updateStatusRelationColaborator($solicitudId, $estatus);

        // Obtener el id de la solicitud por el folio
        $solicitudIdF = (new SolicitudController())->getIdByFolio($folio);

        // Primero verificamos si la hora de la solicitud no es nula
        $solicitudePersonal = (new SolicitudController())->getSolicitudPersonal($solicitudId);

        if ($solicitudePersonal != null) {
            if ($solicitudePersonal->horas_reportables == null) {
                // Actualizamos las horas
                $this->changeHoras($solicitudId, $nh, $nh);
            }
        }

        // Guardamos la bitácora
        (new BitacoraController())->store(
            'Cambio de estatus de la relación de personal con la solicitud (solicitud_usuarios)',
            'Solicitud con ID: ' . $solicitudId . ' y Estatus: ' . ($solicitudP->estatus == 1 ? 'Autorizado' : 'No Autorizado'),
            'A Solicitud con ID: ' . $solicitudId . ' y Estatus: ' . ($estatus == 1 ? 'Autorizado' : 'No Autorizado'),
            auth()->user()->Id_Usuario,
            $solicitudId
        );

        // Verificamos si se guardó correctamente
        if ($response) {
            $this->dispatch('refreshSubrows', ids: [$solicitudIdF], requiredToggle: true);

            $this->dispatch('toast', type: 'success', message: ($estatus === true) ? 'Personal marcado' : 'Personal desmarcado');
        } else {
            $this->dispatch('toast', type: 'error', message: 'Error al asignar el personal');
        }
    }

    /**
     * Función para obtener los personales seleccionados.
     *
     * @return array
     */
    public function getSelectedPersonal(): array
    {
        return collect($this->selectedPersonal)->pluck('personal')->toArray();
    }

    /**
     * Función para actualizar la hora por registro de personal.
     *
     * @param $idSolicitudPersonal
     * @param $horaSolicitud
     * @param $hora
     */
    public function changeHoras($idSolicitudPersonal, $hora, $horaSolicitud): void
    {
        try {
            // Validar que sea menor igual a 24
            if ($hora > 24) {
                $this->dispatch('toast', type: 'error', message: 'La hora no puede ser mayor a 24');
                return;
            }

            // Obtenemos la solicitud por el id
            $solicitudPersonal = (new SolicitudController())->getSolicitudPersonal($idSolicitudPersonal);

            // Validar que la hora no sea mayor a la hora de la solicitud
            if ($hora > $horaSolicitud) {
                $this->dispatch('toast', type: 'error', message: 'La hora no puede ser mayor a la hora de la solicitud');

                // Obtenemos la solicitud por el id
                $solicitud = (new SolicitudController())->show($solicitudPersonal->solicitud_id);

                $this->dispatch('refreshSubrows', ids: [$solicitud->id], requiredToggle: true);
                return;
            }

            // Guardamos la bitácora
            (new BitacoraController())->store(
                'Actualización de Horas (solicitud_usuarios)',
                'Solicitud con ID: ' . $idSolicitudPersonal . ' y Horas: ' . $solicitudPersonal->horas_reportables,
                'A Solicitud con ID: ' . $idSolicitudPersonal . ' y Horas: ' . $hora,
                auth()->user()->Id_Usuario,
                $idSolicitudPersonal
            );

            // Guardamos la relación de personal con la solicitud
            (new SolicitudController())->updateHorasRelationColaborator($idSolicitudPersonal, $hora);

        } catch (\Exception $e) {
            $this->dispatch('toast', type: 'error', message: 'Error al actualizar las horas');
        }
    }

    /**
     * Función para cambiar los estatus a multiples registros
     *
     * @param $estatus
     * @return void
     */
    public function changeEstatusFinalizado($estatus = 4): void
    {
        // Validamos que se haya seleccionado personal
        if (count($this->solicitudesPorFinalizar) == 0) {
            $this->dispatch('toast', type: 'error', message: 'Selecciona al menos una solicitud');
            return;
        }

        // Guardamos la relación de personal con la solicitud
        $response = (new SolicitudController())->updateStatusMultiples($this->solicitudesPorFinalizar, $estatus);

        // Verificamos si se guardó correctamente
        if ($response) {
            $this->dispatch('refreshSubrows', ids: $this->solicitudesPorFinalizar, requiredToggle: true);

            $this->dispatch('toast', type: 'success', message: 'Solicitudes marcadas como finalizadas');
            $this->reset([
                'solicitudesPorFinalizar',
                'modeAction',
                'tipoAccion',
            ]);
        } else {
            $this->dispatch('toast', type: 'error', message: 'Error al asignar el personal');
        }
    }
}
