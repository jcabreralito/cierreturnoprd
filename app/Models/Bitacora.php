<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bitacora extends Model
{
    use HasFactory;

    protected $table = 'bitacora';

    public $timestamps = false;

    protected $fillable = [
        'seccion',
        'cambio_previo',
        'cambio_posterior',
        'solicitud_id',
        'user_id',
    ];
}
