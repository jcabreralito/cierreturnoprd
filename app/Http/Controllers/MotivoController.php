<?php

namespace App\Http\Controllers;

use App\Models\Motivo;
use Illuminate\Http\Request;

class MotivoController extends Controller
{
    /**
     * Función para obtener todos los motivos
     *
     * @return mixed
     */
    public function index()
    {
        try {
            return Motivo::all();
        } catch (\Exception $e) {
            // Retornamos la respuesta
            return response()->json([
                'message' => 'Error al obtener los motivos',
            ], 500);
        }
    }
}
