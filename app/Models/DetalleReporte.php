<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleReporte extends Model
{
    protected $table = 'detalles_reporte';
    public $timestamps = false;
    protected $fillable = [
        'ajustes_normales',
        'ajustes_literatura',
        'tiros',
        'en',
        'velocidad_promedio',
        'se_debio_hacer_en',
        'tiempo_reportado',
        'tiempo_ajuste',
        'tiempo_tiro',
        'tiempo_muerto',
        'std_ajuste_normal',
        'std_ajuste_literatura',
        'std_velocidad_tiro',
        'eficiencia_global',
        'reporte_id',
    ];
}
