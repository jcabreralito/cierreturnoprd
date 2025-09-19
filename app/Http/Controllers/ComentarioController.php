<?php

namespace App\Http\Controllers;

use App\Models\Comentario;
use Illuminate\Http\Request;

class ComentarioController extends Controller
{
    /**
     * Función para guardar un comentario en la base de datos
     *
     * @param $id
     * @param $comentario
     * @return mixed
     */
    public function guardarComentario($id, $comentario)
    {
        try {
            $comentarioNuevo = Comentario::updateOrCreate(
                ['reporte_id' => $id],
                [
                    'comentario' => $comentario,
                    'usuario_id' => auth()->user()->Id_Usuario,
                    'reporte_id' => $id
                ]
            );
            return $comentarioNuevo;
        } catch (\Exception $e) {
            return "Lo sentimos, ha ocurrido un error";
        }
    }
}
