<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Función para obtener el usuario al que le pertenece una contraseña
     *
     * @param Request $request
     * @return \App\Models\User|null
     */
    public function validateUser(Request $request)
    {
        $passwordSupervisor = $request->input('passwordSupervisor');
        $passwordOperador = $request->input('passwordOperador');

        if (auth()->user()->tipoUsuarioCierreTurno == 1) {
            $user = User::where('password', $passwordSupervisor)
                        ->where('estatus', 'ACTIVO')
                        ->first();

            return response()->json([
                'operador' => $user != null ? $user->Login : null,
                'supervisor' => $user != null ? $user->Login : null,
                'response' => $user != null
            ]);
        } else {
            $operadorNombre = explode('-', $request->input('operador'));
            $operador = User::where('password', $passwordOperador)
                        ->where(function($q) use ($operadorNombre) {
                            $q->where('Nombre', $operadorNombre[1])
                                ->orWhere('Personal', $operadorNombre[0]);
                        })
                        ->where('estatus', 'ACTIVO')
                        ->first();

            $supervisor = User::where('password', $passwordOperador)
                        ->where('Puesto', 'like', '%SUPERVISOR%')
                        ->where('estatus', 'ACTIVO')
                        ->first();

            return response()->json([
                'operador' => $operador != null ? $operador->Login : null,
                'supervisor' => $supervisor != null ? $supervisor->Login : null,
                'response' => $operador != null || $supervisor != null
            ]);
        }

    }
}
