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
        $passwordOperador = $request->input('passwordOperador');
        $supervisor = $request->input('supervisor');

        if (auth()->user()->tipoUsuarioCierreTurno == 1) {
            $userOperador = User::where('password', $passwordOperador)
                        ->where('estatus', 'ACTIVO')
                        ->first();

            return response()->json([
                'operador' => $userOperador != null ? $userOperador->Login : null,
                'supervisor' => $supervisor,
                'response' => $userOperador != null
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

            return response()->json([
                'operador' => $operador != null ? $operador->Login : null,
                'supervisor' => $supervisor,
                'response' => $operador != null
            ]);
        }
    }

    /**
     * Función para obtener el usuario al que le pertenece una contraseña (supervisor)
     *
     * @param Request $request
     * @return \App\Models\User|null
     */
    public function validateSupervisor(Request $request)
    {
        $passwordSupervisor = $request->input('passwordSupervisor');

        if (auth()->user()->tipoUsuarioCierreTurno == 1) {
            $userSupervisor = User::where('password', $passwordSupervisor)
                        ->where('estatus', 'ACTIVO')
                        ->first();

            return response()->json([
                'supervisor' => $userSupervisor != null ? $userSupervisor->Login : null,
                'response' => $userSupervisor != null
            ]);
        } else {
            $supervisor = auth()->user()->Password == $passwordSupervisor ? auth()->user()->Login : null;

            return response()->json([
                'supervisor' => $supervisor,
                'response' => $supervisor != null
            ]);
        }
    }
}
