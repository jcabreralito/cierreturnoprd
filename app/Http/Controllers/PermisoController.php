<?php

namespace App\Http\Controllers;

use App\Models\Permiso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermisoController extends Controller
{
    /**
     * Función para obtener los permisos de un usuario.
     *
     * @return array
     */
    public function getPermisos(): array
    {
        $permisos = DB::table('roles_permisos')
                        ->select('permiso_id')
                        ->where('rol_id', auth()->user()->tipoUsuarioHorasExtra)
                        ->where('activo', 1)
                        ->get();

        return $permisos->pluck('permiso_id')->toArray();
    }
}
