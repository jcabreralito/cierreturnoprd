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
            $reporte->tipo_reporte_generar = $data['tipo_reporte_generar']; // Tipo de reporte a generar
            $reporte->supervisor_id = $data['supervisor_id']; // Asigna el ID del supervisor
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
    public function cierres(array $data)
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
                        ->when($data['operador'], function ($query) use ($data) {
                            return $query->where('operador', $data['operador']);
                        })
                        ->when($data['supervisor'], function ($query) use ($data) {
                            return $query->where('supervisor_id', $data['supervisor']);
                        })
                        ->when($data['estatus'], function ($query) use ($data) {
                            return $query->where('estatus', $data['estatus']);
                        })
                        ->when(auth()->user()->tipoUsuarioCierreTurno != 1 && auth()->user()->tipoUsuarioCierreTurno != 4 && auth()->user()->tipoUsuarioCierreTurno != 5, function ($query) {
                            if (auth()->user()->tipoUsuarioCierreTurno == 2) {
                                return $query->where('supervisor_id', auth()->user()->Id_Usuario);
                            }

                            if (auth()->user()->tipoUsuarioCierreTurno == 3) {
                                return $query->where('operador', auth()->user()->Personal);
                            }
                        })
                        ->whereIn('estatus', [1, 3]) // Solo los que no han sido recalculados
                        ->orderBy($data['sort'], $data['sort_type'])
                        ->when($data['pagination'] != 'todos', function ($query) use ($data) {
                            return $query->paginate($data['pagination']);
                        }, function ($query) {
                            return $query->get();
                        });
    }

    /**
     * Función para obtener los cierres del historico
     *
     * @param array $data
     * @return mixed
     */
    public function cierresHistorico(array $data)
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
                        ->when($data['operador'], function ($query) use ($data) {
                            return $query->where('operador', trim($data['operador']));
                        })
                        ->when($data['supervisor'], function ($query) use ($data) {
                            return $query->where('supervisor_id', $data['supervisor']);
                        })
                        ->when(auth()->user()->tipoUsuarioCierreTurno != 1 && auth()->user()->tipoUsuarioCierreTurno != 4, function ($query) {
                            if (auth()->user()->tipoUsuarioCierreTurno == 2) {

                                // Marco del real y equintero
                                if (auth()->user()->Id_Usuario == 2152 || auth()->user()->Id_Usuario == 12451) {
                                    return $query->where('departamento_operador', 'OFFSET');
                                } else {
                                    return $query->where('supervisor_id', auth()->user()->Id_Usuario);
                                }
                            }

                            if (auth()->user()->tipoUsuarioCierreTurno == 3) {
                                return $query->where('operador', auth()->user()->Personal);
                            }
                        })
                        ->whereIn('estatus', [2]) // Solo los que ya fueron recalculados
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
        try {
            Reporte::where('id', $id)
                ->update([
                    'fecha_recalculo' => Carbon::now()->format('Y-d-m H:i:s'),
                    'hizoRecalculo' => 1,
                ]);
        } catch (\Exception $e) {
            return "Lo siento, ocurrió un error al actualizar el re-cálculo.";
        }
    }

    /**
     * Función para actualizar la firma del supervisor y el estatus
     *
     * @param array $data
     * @param int $id
     * @return void
     */
    public function actualizarFirmaSupervisor(array $data, int $id)
    {
        try {
            Reporte::where('id', $id)
                ->update($data);
        } catch (\Exception $e) {
            return "Lo siento, ocurrió un error al actualizar la firma del supervisor.";
        }
    }

    /**
     * Función para actualizar el estatus del reporte
     *
     * @param int $estatus
     * @param int $id
     * @return void
     */
    public function actualizarEstatus(int $estatus, int $id)
    {
        try {
            $reporte = Reporte::find($id);
            $reporte->estatus = $estatus;
            $reporte->save();
        } catch (\Exception $e) {
            return "Lo siento, ocurrió un error al actualizar el estatus del reporte.";
        }
    }
}
