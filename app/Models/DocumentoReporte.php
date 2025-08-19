<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentoReporte extends Model
{
    protected $table = 'reportes_pdf';
    public $timestamps = false;
    protected $fillable = [
        'archivo',
        'reporte_id',
    ];
}
