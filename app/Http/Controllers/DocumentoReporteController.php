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

    /**
     * Función para actualizar los documentos de un reporte
     *
     * @param array $data
     * @param int $reporteId
     * @return mixed
     */
    public function actualizarDocumentos($data, $reporteId)
    {
        try {
            DB::beginTransaction();
            $documento = DocumentoReporte::where('reporte_id', $reporteId)->first();
            if ($documento) {
                $documento->archivo = $data['archivo'];
                $documento->save();
                DB::commit();
                return "Documento actualizado exitosamente.";
            } else {
                return "No se encontró el documento para actualizar.";
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return "Lo siento, ocurrió un error al actualizar el documento.";
        }
    }

    /**
     * Función para obtener los documentos de un reporte
     *
     * @param int $reporteId
     * @return mixed
     */
    public function obtenerPdf($reporteId)
    {
        return DocumentoReporte::where('reporte_id', $reporteId)->first();
    }
}
