<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class RazonReporte extends Model
{
    protected $table = 'razones_reporte';
    public $timestamps = false;
    protected $fillable = [
        'observaciones',
        'acciones_correctivas',
        'reporte_id',
    ];
}
