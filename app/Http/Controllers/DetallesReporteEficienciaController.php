<?php

namespace App\Http\Controllers;

use App\Models\DetallesReporteEficiencia;
use Illuminate\Http\Request;

class DetallesReporteEficienciaController extends Controller
{
    /**
     * Función para almacenar los detalles del reporte de eficiencia.
     *
     * @param $data
     * @return void
     */
    public function storeDetallesReporteEficiencia($data)
    {
        try {
            $detalles = new DetallesReporteEficiencia();

            $detalles->tiempo_ajuste_promedio = $data['tiempo_ajuste_promedio'];
            $detalles->num_ajustes = $data['num_ajustes'];
            $detalles->num_ajustes_literatura = $data['num_ajustes_literatura'];
            $detalles->tiempo_ajustes = $data['tiempo_ajustes'];
            $detalles->se_debio_realizar_en_ajustes = $data['se_debio_realizar_en_ajustes'];
            $detalles->velocidad_promedio = $data['velocidad_promedio'];
            $detalles->num_tiros = $data['num_tiros'];
            $detalles->tiempo_tiros = $data['tiempo_tiros'];
            $detalles->se_debio_realizar_en_tiros = $data['se_debio_realizar_en_tiros'];
            $detalles->en = $data['en'];
            $detalles->debio_hacerce_en = $data['debio_hacerce_en'];
            $detalles->tiempo_muerto = $data['tiempo_muerto'];
            $detalles->eficiencia_global = $data['eficiencia_global'];
            $detalles->std_ajuste_normal = $data['std_ajuste_normal'];
            $detalles->std_ajuste_literatura = $data['std_ajuste_literatura'];
            $detalles->std_velocidad_tiro = $data['std_velocidad_tiro'];
            $detalles->reporte_id = $data['reporte_id'];

            $detalles->save();
        } catch (\Exception $e) {
            return 'Lo sentimos, ha ocurrido un error';
        }
    }

    /**
     * Función para actualizar los detalles del reporte de eficiencia.
     *
     * @param $data
     * @return mixed
     */
    public function updateDetallesReporteEficiencia($data)
    {
        try {
            $detalles = DetallesReporteEficiencia::where('reporte_id', $data['reporte_id'])->first();

            if ($detalles) {
                $detalles->tiempo_ajuste_promedio = $data['tiempo_ajuste_promedio'];
                $detalles->num_ajustes = $data['num_ajustes'];
                $detalles->num_ajustes_literatura = $data['num_ajustes_literatura'];
                $detalles->tiempo_ajustes = $data['tiempo_ajustes'];
                $detalles->se_debio_realizar_en_ajustes = $data['se_debio_realizar_en_ajustes'];
                $detalles->velocidad_promedio = $data['velocidad_promedio'];
                $detalles->num_tiros = $data['num_tiros'];
                $detalles->tiempo_tiros = $data['tiempo_tiros'];
                $detalles->se_debio_realizar_en_tiros = $data['se_debio_realizar_en_tiros'];
                $detalles->en = $data['en'];
                $detalles->debio_hacerce_en = $data['debio_hacerce_en'];
                $detalles->tiempo_muerto = $data['tiempo_muerto'];
                $detalles->eficiencia_global = $data['eficiencia_global'];
                $detalles->std_ajuste_normal = $data['std_ajuste_normal'];
                $detalles->std_ajuste_literatura = $data['std_ajuste_literatura'];
                $detalles->std_velocidad_tiro = $data['std_velocidad_tiro'];
                $detalles->reporte_id = $data['reporte_id'];

                $detalles->save();
            }
        } catch (\Exception $e) {
            return 'Lo sentimos, ha ocurrido un error';
        }
    }
}
