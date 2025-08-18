<?php

namespace App\Http\Controllers;

use App\Models\Turno;
use Illuminate\Http\Request;

class TurnoController extends Controller
{
    public function index()
    {
        try {
            return Turno::all();
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al obtener los turnos.'], 500);
        }
    }
}
