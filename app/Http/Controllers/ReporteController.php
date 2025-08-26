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
            $reporte->operador = trim($data['operador']);
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
            return "Lo siento, ocurrió un error al guardar el reporte.";
        }
    }

    /**
     * Función para obtener un reporte específico
     *
     * @param array $data
     * @return mixed
     */
    public function obtenerReporte(array $data)
    {
        return Reporte::where('tipo_reporte', $data['tipo_reporte'])
                        ->where('operador', explode('-', $data['operador'])[0])
                        ->where('turno', $data['turno'])
                        ->where('fecha_cierre', $data['fecha_cierre'])
                        ->first();
    }

    /**
     * Función para obtener todos los cierres realizados
     *
     * @param array $data
     * @return mixed
     */
    public function getReportesRealizados(array $data)
    {
        return Reporte::when($data['fecha_cierre'], function ($query) use ($data) {
                            $query->where('fecha_cierre', $data['fecha_cierre']);
                        })
                        ->when($data['turno'], function ($query) use ($data) {
                            $query->where('turno', $data['turno']);
                        })
                        ->when($data['operador'], function ($query) use ($data) {
                            $query->where('operador', explode('-', $data['operador'])[0]);
                        })
                        ->paginate(50);
    }
}
