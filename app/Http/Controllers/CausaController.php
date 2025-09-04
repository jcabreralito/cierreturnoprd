<?php

namespace App\Http\Controllers;

use App\Models\Causa;
use Illuminate\Support\Facades\DB;

class CausaController extends Controller
{
    /**
     * Función para registrar la causa de un reporte
     *
     * @param array $data
     * @return mixed
     */
    public function registrarCausa($data)
    {
        try {
            DB::beginTransaction();
            $causa = new Causa();
            $causa->causa = $data['causa'];
            $causa->reporte_id = $data['reporte_id'];
            $causa->estatus = 1;
            $causa->save();
            DB::commit();
            return "Causa registrada exitosamente.";
        } catch (\Throwable $th) {
            DB::rollBack();
            return "Lo siento, ocurrió un error al registrar la causa.";
        }
    }

    /**
     * Función para obtener las causas de un reporte
     *
     * @param int $reporteId
     * @return mixed
     */
    public function obtenerCausas(int $reporteId)
    {
        return Causa::where('reporte_id', $reporteId)->get();
    }
}
