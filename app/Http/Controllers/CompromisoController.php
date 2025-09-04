<?php

namespace App\Http\Controllers;

use App\Models\Compromiso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompromisoController extends Controller
{
    /**
     * Función para registrar el compromiso de un reporte
     *
     * @param array $data
     * @return mixed
     */
    public function registrarCompromiso($data)
    {
        try {
            DB::beginTransaction();
            $compromiso = new Compromiso();
            $compromiso->compromiso = $data['compromiso'];
            $compromiso->reporte_id = $data['reporte_id'];
            $compromiso->estatus = 1;
            $compromiso->save();
            DB::commit();
            return "Compromiso registrado exitosamente.";
        } catch (\Throwable $th) {
            dd($th->getMessage());
            DB::rollBack();
            return "Lo siento, ocurrió un error al registrar el compromiso.";
        }
    }

    /**
     * Función para obtener los compromisos de un reporte
     *
     * @param int $reporteId
     * @return mixed
     */
    public function obtenerCompromisos(int $reporteId)
    {
        return Compromiso::where('reporte_id', $reporteId)->get();
    }
}
