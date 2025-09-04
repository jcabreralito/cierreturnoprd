<?php

namespace App\Http\Controllers;

use App\Models\Reporte;
use App\Models\v_Reportes;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    /**
     * Función para guardar el reporte
     *
     * @param array $data
     * @return mixed
     */
    public function guardarReporte($data)
    {
        try {
            DB::beginTransaction();
            $reporte = new Reporte();
            $reporte->folio = Reporte::max('folio') + 1; // Genera un nuevo folio
            $reporte->estatus = $data['estatus'];
            $reporte->tipo_reporte = $data['tipo_reporte'];
            $reporte->operador = trim($data['operador']);
            $reporte->maquina = $data['maquina'];
            $reporte->fecha_cierre = $data['fecha_cierre'];
            $reporte->fecha_firma_operador = Carbon::now()->format('Y-d-m H:i:s');
            $reporte->turno = $data['turno'];
            $reporte->firma_supervisor = $data['firma_supervisor']; // Asigna el usuario que cerró el reporte
            $reporte->firma_operador = $data['firma_operador']; // Asigna el usuario que cerró el reporte
            $reporte->usuario_id = auth()->user()->Id_Usuario; // Asigna el ID del usuario autenticado
            $reporte->save();
            DB::commit();
            return $reporte;
        } catch (\Throwable $th) {
            DB::rollBack();
            return "Lo siento, ocurrió un error al guardar el reporte.";
        }
    }

    /**
     * Función para obtener un reporte específico
     *
     * @param array $data
     * @return mixed
     */
    public function obtenerReporte(array $data)
    {
        return Reporte::where('tipo_reporte', $data['tipo_reporte'])
                        ->where('operador', explode('-', $data['operador'])[0])
                        ->where('turno', $data['turno'])
                        ->where('fecha_cierre', $data['fecha_cierre'])
                        ->first();
    }

    /**
     * Función para obtener un reporte por id
     *
     * @param $id
     * @return mixed
     */
    public function obtenerReportePorId($id)
    {
        return v_Reportes::find($id);
    }

    /**
     * Función para obtener todos los cierres realizados
     *
     * @param array $data
     * @return mixed
     */
    public function getReportesRealizados(array $data)
    {
        return v_Reportes::when($data['folio'], function ($query) use ($data) {
                            return $query->where('folio', 'like', '%' . $data['folio'] . '%');
                        })
                        ->when($data['fecha_cierre_operador'], function ($query) use ($data) {
                            return $query->whereDate('fecha_firma_operador', $data['fecha_cierre_operador']);
                        })
                        ->when($data['fecha_cierre_supervisor'], function ($query) use ($data) {
                            return $query->whereDate('fecha_firma_supervisor', $data['fecha_cierre_supervisor']);
                        })
                        ->orderBy($data['sort'], $data['sort_type'])
                        ->when($data['pagination'] != 'todos', function ($query) use ($data) {
                            return $query->paginate($data['pagination']);
                        }, function ($query) {
                            return $query->get();
                        });
    }

    /**
     * Función para obtener los cierres realizados por el usuario autenticado
     *
     * @param array $data
     * @return mixed
     */
    public function misCierres(array $data)
    {
        return v_Reportes::when($data['folio'], function ($query) use ($data) {
                            return $query->where('folio', 'like', '%' . $data['folio'] . '%');
                        })
                        ->when($data['fecha_cierre_operador'], function ($query) use ($data) {
                            return $query->whereDate('fecha_firma_operador', $data['fecha_cierre_operador']);
                        })
                        ->when($data['fecha_cierre_supervisor'], function ($query) use ($data) {
                            return $query->whereDate('fecha_firma_supervisor', $data['fecha_cierre_supervisor']);
                        })
                        ->when(auth()->user()->tipoUsuarioCierreTurno != 1 && auth()->user()->tipoUsuarioCierreTurno != 4, function ($query) {
                            return $query->where('usuario_id', auth()->user()->Id_Usuario);
                        })
                        ->orderBy($data['sort'], $data['sort_type'])
                        ->when($data['pagination'] != 'todos', function ($query) use ($data) {
                            return $query->paginate($data['pagination']);
                        }, function ($query) {
                            return $query->get();
                        });
    }

    /**
     * Función para obtener todos los reportes para re-cálculo
     *
     * @param array $data
     * @return mixed
     */
    public function reCierre(array $data)
    {
        return v_Reportes::when($data['folio'], function ($query) use ($data) {
                            return $query->where('folio', 'like', '%' . $data['folio'] . '%');
                        })
                        ->when($data['fecha_cierre_operador'], function ($query) use ($data) {
                            return $query->whereDate('fecha_firma_operador', $data['fecha_cierre_operador']);
                        })
                        ->when($data['fecha_cierre_supervisor'], function ($query) use ($data) {
                            return $query->whereDate('fecha_firma_supervisor', $data['fecha_cierre_supervisor']);
                        })
                        ->orderBy($data['sort'], $data['sort_type'])
                        ->when($data['pagination'] != 'todos', function ($query) use ($data) {
                            return $query->paginate($data['pagination']);
                        }, function ($query) {
                            return $query->get();
                        });
    }

    /**
     * Función para obtener todos los reportes por supervisor
     *
     * @return mixed
     */
    public function getReportes()
    {
        return v_Reportes::when(auth()->user()->tipoUsuarioCierreTurno != 1 && auth()->user()->tipoUsuarioCierreTurno != 4, function ($query) {
                            return $query->where('supervisor_id', auth()->user()->Id_Usuario);
                        })
                        ->paginate(50);
    }

    /**
     * Función para actualizar la fecha de re-cálculo y el estatus
     *
     * @param int $id
     * @return void
     */
    public function actualizarRecalculo(int $id)
    {
        Reporte::where('id', $id)
                ->update([
                    'fecha_recalculo' => Carbon::now()->format('Y-d-m H:i:s'),
                    'estatus' => 4,
                ]);
    }
}
