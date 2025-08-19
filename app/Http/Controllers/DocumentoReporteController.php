<?php

namespace App\Http\Controllers;

use App\Models\DocumentoReporte;
use Illuminate\Support\Facades\DB;

class DocumentoReporteController extends Controller
{
    /**
     * Función para registrar los documentos de un reporte
     *
     * @param array $data
     * @return mixed
     */
    public function registrarDocumentos($data)
    {
        try {
            DB::beginTransaction();
            $documento = new DocumentoReporte();
            $documento->archivo = $data['archivo'];
            $documento->reporte_id = $data['reporte_id'];
            $documento->save();
            DB::commit();
            return "Documento registrado exitosamente.";
        } catch (\Throwable $th) {
            DB::rollBack();
            return "Lo siento, ocurrió un error al registrar el documento.";
        }
    }
}
