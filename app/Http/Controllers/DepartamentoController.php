<?php

namespace App\Http\Controllers;

use App\Models\Departamento;
use Illuminate\Http\Request;

class DepartamentoController extends Controller
{
    /**
     * Función para obtener todos los departamentos.
     *
     * @return mixed
     */
    public function index($tipo = null)
    {
        try {
            if (in_array(auth()->user()->tipoUsuarioHorasExtra, [1, 2, 4]) || in_array(auth()->user()->Id_Usuario, [12436, 12460])) {
                if (in_array(auth()->user()->Id_Usuario, [12436, 12460])) {
                    return Departamento::where('tipo', 2)
                    ->orderBy('departamento', 'asc')
                    ->get();
                } else {
                    if ($tipo == null) {
                        return Departamento::orderBy('departamento', 'asc')->get();
                    } else {
                        return Departamento::where('tipo', $tipo)
                        ->orderBy('departamento', 'asc')
                        ->get();
                    }
                }
            } else {
                return Departamento::where('encargado_id', auth()->user()->Id_Usuario)
                                    ->orWhere('encargado2_id', auth()->user()->Id_Usuario)
                                    ->orderBy('departamento', 'asc')
                                    ->get();
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al obtener los departamentos.'], 500);
        }
    }

    /**
     * Función para obtener un departamento por el id de usuario
     *
     * @param int $userId
     * @return mixed
     */
    public function getDepartamentosByUser($userId)
    {
        try {
            return Departamento::where('encargado_id', $userId)
                                ->orWhere('encargado2_id', $userId)
                                ->orderBy('departamento', 'asc')
                                ->get();
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al obtener los departamentos.'], 500);
        }
    }
}
