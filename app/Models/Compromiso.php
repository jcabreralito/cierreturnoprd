<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compromiso extends Model
{
    protected $table = 'compromisos';
    public $timestamps = false;
    protected $fillable = [
        'compromiso',
        'reporte_id',
        'estatus'
    ];
}
