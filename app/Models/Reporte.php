<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Reporte extends Model
{
    protected $table = 'reportes';
    public $timestamps = false;
    protected $fillable = [
        'folio',
        'estatus',
        'tipo_reporte',
        'operador',
        'maquina',
        'fecha_cierre',
        'turno',
        'firma_supervisor',
        'firma_operador',
        'usuario_id',
    ];
}
