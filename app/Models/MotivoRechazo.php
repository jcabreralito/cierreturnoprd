<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MotivoRechazo extends Model
{
    protected $table = 'motivo_rechazo';
    public $timestamps = false;
    protected $fillable = [
        'motivo',
        'reporte_id',
    ];
}
