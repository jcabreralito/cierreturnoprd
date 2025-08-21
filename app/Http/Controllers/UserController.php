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

        if (auth()->user()->tipoUsuarioCierreTurno == 1) {
            $user = User::where('password', $request->input('password'))
                        ->where('estatus', 'ACTIVO')
                        ->first();
        } else {
            $operadorNombre = explode('-', $request->input('operador'));
            $user = User::where('password', $request->input('password'))->where(function($q) use ($operadorNombre) {
                        $q->where('Nombre', $operadorNombre[1])
                            ->orWhere('Personal', $operadorNombre[0]);
                    })
                    ->where('estatus', 'ACTIVO')
                    ->first();
        }

        return response()->json([
            'user' => $user != null ? $user->Login : null,
            'response' => $user != null
        ]);
    }
}
