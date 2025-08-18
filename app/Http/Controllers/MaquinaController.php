<?php

namespace App\Http\Controllers;

use App\Models\Maquina;
use Illuminate\Http\Request;

class MaquinaController extends Controller
{
    /**
     * Función para obtener todas las máquinas.
     *
     * @param $data
     * @return mixed
     */
    public function index($data = [])
    {
        try {
            return Maquina::where('departamento_id', $data['departamento_id'])->get();
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al obtener las máquinas.'], 500);
        }
    }

    /**
     * Función para obtener el número maximo de usuarios por maquina
     *
     * @param $maquinaId
     * @return mixed
     */
    public function getMaxPersonalMaquina($maquinaId)
    {
        try {
            $maquina = Maquina::find($maquinaId);
            return ($maquina != null) ? $maquina->default_num_personales : 1;
        } catch (\Exception $e) {
            // Retornamos la respuesta
            return 0;
        }
    }
}
