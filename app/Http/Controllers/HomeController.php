<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Función para redirigir al index de la vista principal
     *
     * @return mixed
     */
    public function index(Request $request)
    {
        // Desencriptamos la variable y redirigimos de nueva cuenta
        $userB64 = base64_decode($request->usuario);

        // separamos el usuario del personal
        $user = explode('-', $userB64)[0];
        $personal = explode('-', $userB64)[1];

        // La almacenamos en el usuario
        $user = User::where('login', $user)->where('estatus', 'ACTIVO')
                ->when($personal != null && $personal != '', function ($query) use ($personal) {
                    $query->where('Personal', $personal);
                })
                ->first();

        // Validamos si el usuario no existe para redireccionar a litoapps
        if ($user == null && auth()->user() == null) {
            // redireccionamos a litoapps
            return redirect('https://servicios.litoprocess.com/litoapps/index.php');
        }

        if (auth()->user() == null) {

            // Validamos si el usuario tiene permisos para acceder
            if ($user->tipoUsuarioCierreTurno == null && $user->estatusCierreTurno == null) {
                return redirect('https://servicios.litoprocess.com/litoapps/index.php');
            }

            // Loggeamos al usuario
            Auth::login($user);
        } else {
            if ($user != null) {
                // Validamos si el usuario que se logea es el mismo que el que ya existe
                if (auth()->user()->Login != $user->Login) {

                    // Desloggeamos al usuario
                    $this->logoutUser();

                    // Loggeamos al usuario
                    Auth::login($user);
                }
            }
        }

        return redirect()->route('main');
    }

    /**
     * Función para mostrar la vista principal
     *
     * @return View
     */
    public function main(): View
    {
        return view('welcome');
    }

    /**
     * Función para cerrar la session por completo
     *
     * @return mixed
     */
    public function logout()
    {
        // Desloggeamos al usuario
        $this->logoutUser();

        // validamos si el usuario no existe para redireccionar a litoapps
        if (auth()->user() == null) {
            // redireccionamos a litoapps
            return redirect('https://servicios.litoprocess.com/litoapps/index.php');
        }
    }

    /**
     * Función para cerrar la sesión del usuario
     *
     * @return void
     */
    public function logoutUser(): void
    {
        Auth::logout();

        // Opcional: destruir la sesión completamente
        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }
}
