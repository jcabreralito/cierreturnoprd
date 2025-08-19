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
        $user = User::where('password', $request->input('password'))->first();

        return response()->json([
            'user' => $user ? $user->Login : null,
            'response' => $user != null
        ]);
    }
}
