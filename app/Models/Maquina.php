<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maquina extends Model
{
    use HasFactory;

    protected $table = 'maquinas';

    public $timestamps = false;

    protected $fillable = [
        'maquina',
        'departamento_id',
        'default_num_personales',
    ];
}
