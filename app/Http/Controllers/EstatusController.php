<?php

namespace App\Http\Controllers;

use App\Models\Estatus;
use Illuminate\Http\Request;

class EstatusController extends Controller
{
    /**
     * Función para obtener todos los estatus.
     *
     * @return mixed
     */
    public function index()
    {
        try {
            return Estatus::where('activo', 1)->get();
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al obtener los estatus.'], 500);
        }
    }
}
