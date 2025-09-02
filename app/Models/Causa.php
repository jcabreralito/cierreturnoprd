<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Causa extends Model
{
    protected $table = 'causas';
    public $timestamps = false;
    protected $fillable = [
        'causa',
        'reporte_id',
        'estatus'
    ];
}
