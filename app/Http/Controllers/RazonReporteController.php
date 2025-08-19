<?php

namespace App\Http\Controllers;

use App\Models\RazonReporte;
use Illuminate\Support\Facades\DB;

class RazonReporteController extends Controller
{
    /**
     * Función para registrar las razones de un reporte
     *
     * @param $data
     */
    public function registrarRazon($data)
    {
        try {
            DB::beginTransaction();
            $razonReporte = new RazonReporte();
            $razonReporte->observaciones = $data['observaciones'];
            $razonReporte->acciones_correctivas = $data['acciones_correctivas'];
            $razonReporte->reporte_id = $data['reporte_id'];
            $razonReporte->save();
            DB::commit();
            return "Razón del reporte registrada exitosamente.";
        } catch (\Throwable $th) {
            DB::rollBack();
            return "Lo siento, ocurrió un error al registrar la razón del reporte.";
        }
    }
}
