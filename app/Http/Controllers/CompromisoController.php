<?php

namespace App\Http\Controllers;

use App\Models\Compromiso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompromisoController extends Controller
{
    /**
     * Función para registrar el compromiso de un reporte
     *
     * @param array $data
     * @return mixed
     */
    public function registrarCompromiso($data)
    {
        try {
            DB::beginTransaction();
            $compromiso = new Compromiso();
            $compromiso->compromiso = $data['compromiso'];
            $compromiso->reporte_id = $data['reporte_id'];
            $compromiso->estatus = 1;
            $compromiso->save();
            DB::commit();
            return "Documento registrado exitosamente.";
        } catch (\Throwable $th) {
            DB::rollBack();
            return "Lo siento, ocurrió un error al registrar el documento.";
        }
    }
}
