<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Solicitud extends Model
{
    use HasFactory;

    protected $table = 'solicitudes';

    public $timestamps = false;

    protected $fillable = [
        'id',
        'folio',

        'desde_dia',
        'num_repeticiones',
        'horas',
        'observaciones',

        'departamento_id',
        'maquina_id',
        'motivo_id',
        'estatus_id',
        'user_id',
        'turno_id',
        'num_max_usuarios',
        'tipo',
        'rebasa_max_usuarios',
    ];

    /**
     * Relación con los estatus
     *
     * @return BelongsTo
     */
    public function estatus(): BelongsTo
    {
        return $this->belongsTo(Estatus::class);
    }

    /**
     * Relación con las ops
     *
     * @return BelongsTo
     */
    public function op(): BelongsTo
    {
        return $this->belongsTo('ops_solicitudes', 'solicitud_id', 'id');
    }
}
