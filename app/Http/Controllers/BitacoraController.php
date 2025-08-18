<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use Illuminate\Http\Request;

class BitacoraController extends Controller
{
    /**
     * Función para realizar el registro de la bitácora
     *
     * @param $section
     * @param $prevChange
     * @param $nextChange
     * @param $userId
     * @param $solicitudId
     * @return mixed
     */
    public function store($section, $prevChange, $nextChange, $userId, $solicitudId)
    {
        try {
            // Realizamos el registro
            $bitacora = new Bitacora();
            $bitacora->seccion = $section;
            $bitacora->cambio_previo = $prevChange;
            $bitacora->cambio_posterior = $nextChange;
            $bitacora->user_id = $userId;
            $bitacora->solicitud_id = $solicitudId;
            $bitacora->save();

            // Retornamos la respuesta
            return $bitacora;
        } catch (\Exception $e) {
            // Retornamos la respuesta
            return [
                'message' => 'Error al registrar la bitácora',
            ];
        }
    }
}
