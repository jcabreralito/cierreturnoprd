<?php

namespace App\Http\Controllers;

use App\Models\Reporte;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    /**
     * Función para guardar el reporte
     *
     * @param array $data
     * @return mixed
     */
    public function guardarReporte($data)
    {
        try {
            DB::beginTransaction();
            $reporte = new Reporte();
            $reporte->folio = Reporte::max('folio') + 1; // Genera un nuevo folio
            $reporte->estatus = $data['estatus'];
            $reporte->tipo_reporte = $data['tipo_reporte'];
            $reporte->operador = $data['operador'];
            $reporte->maquina = $data['maquina'];
            $reporte->fecha_cierre = $data['fecha_cierre'];
            $reporte->turno = $data['turno'];
            $reporte->usuario_cerro = $data['usuario_cerro']; // Asigna el usuario que cerró el reporte
            $reporte->usuario_id = auth()->user()->Id_Usuario; // Asigna el ID del usuario autenticado
            $reporte->save();
            DB::commit();
            return $reporte;
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th);
            return "Lo siento, ocurrió un error al guardar el reporte.";
        }
    }
}
