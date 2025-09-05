<?php

namespace App\Http\Controllers;

use App\Models\MotivoRechazo;
use Illuminate\Http\Request;

class MotivoRechazoController extends Controller
{
    /**
     * Función para registrar el motivo de rechazo
     *
     * @param $motivo
     * @param $reporte_id
     * @return mixed
     */
    public function registrarMotivoRechazo($motivo, $reporte_id)
    {
        try {
            $rechazo = new MotivoRechazo();
            $rechazo->motivo = $motivo;
            $rechazo->reporte_id = $reporte_id;
            $rechazo->save();
            return $rechazo;
        } catch (\Throwable $th) {
            return "Lo siento, ocurrió un error al registrar el motivo de rechazo.";
        }
    }

    /**
     * Función para obtener los motivos de rechazo de un reporte
     *
     * @param $reporte_id
     * @return mixed
     */
    public function getMotivosRechazo($reporte_id)
    {
        return MotivoRechazo::where('reporte_id', $reporte_id)->get();
    }
}
