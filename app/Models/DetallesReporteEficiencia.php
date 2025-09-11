<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetallesReporteEficiencia extends Model
{
    protected $table = 'detalles_reporte_eficiencia';
    public $timestamps = false;
    protected $fillable = [
        'tiempo_ajuste_promedio',
        'num_ajustes',
        'num_ajustes_literatura',
        'tiempo_ajustes',
        'se_debio_realizar_en_ajustes',
        'velocidad_promedio',
        'num_tiros',
        'tiempo_tiros',
        'se_debio_realizar_en_tiros',
        'en',
        'debio_hacerce_en',
        'tiempo_muerto',
        'eficiencia_global',
        'std_ajuste_normal',
        'std_ajuste_literatura',
        'std_velocidad_tiro',
        'reporte_id',
    ];
}
