<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $table = 'ControlLogin.dbo.cat_Usuarios';
    protected $primaryKey = 'Id_Usuario';
}
