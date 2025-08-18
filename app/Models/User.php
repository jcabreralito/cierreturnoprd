<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;

    protected $table = 'ControlLogin.dbo.cat_Usuarios';
    protected $primaryKey = 'Id_Usuario';
}
