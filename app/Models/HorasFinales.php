<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HorasFinales extends Model
{
    use HasFactory;

    protected $table = 'horas_finales';

    protected $primaryKey = 'idHorasFinales';

    public $timestamps = false;

    protected $fillable = [
        'horas_finales',
        'usuario_id',
        'año',
        'nummes',
        'numsemana',
        'numempleado'
    ];
}
