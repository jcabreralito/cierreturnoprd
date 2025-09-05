<?php

namespace App\Http\Controllers;

use App\Models\DetalleReporte;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DetalleReporteController extends Controller
{
    /**
     * Función para registrar los detalles de un reporte
     *
     * @param array $data
     * @return mixed
     */
    public function registrarDetalles($data)
    {
        try {
            DB::beginTransaction();
            $detalles = new DetalleReporte();
            $detalles->ajustes_normales = $data['ajustes_normales'];
            $detalles->ajustes_literatura = $data['ajustes_literatura'];
            $detalles->tiros = $data['tiros'];
            $detalles->en = $data['en'];
            $detalles->velocidad_promedio = $data['velocidad_promedio'];
            $detalles->se_debio_hacer_en = $data['se_debio_hacer_en'];
            $detalles->tiempo_reportado = $data['tiempo_reportado'];
            $detalles->tiempo_ajuste = $data['tiempo_ajuste'];
            $detalles->tiempo_tiro = $data['tiempo_tiro'];
            $detalles->tiempo_muerto = $data['tiempo_muerto'];
            $detalles->std_ajuste_normal = $data['std_ajuste_normal'];
            $detalles->std_ajuste_literatura = $data['std_ajuste_literatura'];
            $detalles->std_velocidad_tiro = $data['std_velocidad_tiro'];
            $detalles->eficiencia_global = $data['eficiencia_global'];
            $detalles->reporte_id = $data['reporte_id'];
            $detalles->save();
            DB::commit();
            return "Detalles del reporte registrados exitosamente.";
        } catch (\Throwable $th) {
            DB::rollBack();
            return "Lo siento, ocurrió un error al registrar los detalles del reporte.";
        }
    }

    /**
     * Función para actualizar los detalles de un reporte
     *
     * @param array $data
     * @param int $reporteId
     * @return mixed
     */
    public function actualizarDetalles($data, $reporteId)
    {
        try {
            DB::beginTransaction();
            $detalles = DetalleReporte::where('reporte_id', $reporteId)->first();
            if ($detalles) {
                $detalles->ajustes_normales = $data['ajustes_normales'];
                $detalles->ajustes_literatura = $data['ajustes_literatura'];
                $detalles->tiros = $data['tiros'];
                $detalles->en = $data['en'];
                $detalles->velocidad_promedio = $data['velocidad_promedio'];
                $detalles->se_debio_hacer_en = $data['se_debio_hacer_en'];
                $detalles->tiempo_reportado = $data['tiempo_reportado'];
                $detalles->tiempo_ajuste = $data['tiempo_ajuste'];
                $detalles->tiempo_tiro = $data['tiempo_tiro'];
                $detalles->tiempo_muerto = $data['tiempo_muerto'];
                $detalles->std_ajuste_normal = $data['std_ajuste_normal'];
                $detalles->std_ajuste_literatura = $data['std_ajuste_literatura'];
                $detalles->std_velocidad_tiro = $data['std_velocidad_tiro'];
                $detalles->eficiencia_global = $data['eficiencia_global'];
                $detalles->save();
                DB::commit();
                return "Detalles del reporte actualizados exitosamente.";
            } else {
                return "No se encontraron detalles para el reporte especificado.";
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return "Lo siento, ocurrió un error al actualizar los detalles del reporte.";
        }
    }

    /**
     * Función para obtener el detalle de un reporte específico
     *
     * @param int $reporteId
     * @return mixed
     */
    public function getDetalleReporte($reporteId)
    {
        return DetalleReporte::where('reporte_id', $reporteId)->first();
    }
}
