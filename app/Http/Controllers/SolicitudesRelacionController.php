<?php

namespace App\Http\Controllers;

use App\Models\HorasFinales;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SolicitudesRelacionController extends Controller
{
    /**
     * Función para obtener las solicitudes relacionadas a la solicitud
     *
     * @return mixed
     */
    public function getSolicitudesRelacion($data)
    {
        try {
            // Obtenemos las solicitudes relacionadas
            return DB::table('v_HorasFinalesETL')
                        ->when($data['filtroAnio'], function ($query) use ($data) {
                            return $query->where('año', $data['filtroAnio']);
                        })
                        ->when($data['filtroNumSemana'], function ($query) use ($data) {
                            return $query->where('numsemana', $data['filtroNumSemana']);
                        })
                        ->when($data['filtroDepartamento'], function ($query) use ($data) {
                            return $query->where('departamento', $data['filtroDepartamento']);
                        })
                        ->when($data['filtroPersonal'], function ($query) use ($data) {
                            return $query->where('numempleado', $data['filtroPersonal']);
                        })
                        ->when($data['filtroEmpleado'], function ($query) use ($data) {
                            return $query->where('nombrempleado', 'like', '%'.$data['filtroEmpleado'].'%');
                        })
                        ->when($data['filtroGrupoJornada'], function ($query) use ($data) {
                            return $query->where('gpoJornada', $data['filtroGrupoJornada']);
                        })
                        ->orderBy($data['filtroSort'], $data['filtroSortType'])
                        ->when($data['paginationF'] != 'todos', function ($query) use ($data) {
                            return $query->paginate($data['paginationF'], pageName: 'solicitudes-relacion');
                        }, function ($query) {
                            return $query->get();
                        });
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Función para obtener los departamentos
     *
     * @return mixed
     */
    public function getDepartamentos()
    {
        try {
            // Obtenemos los departamentos
            return DB::table('ETL_MSTR.dbo.etl_TEResumen')
                        ->select('departamento')
                        ->distinct()
                        ->orderBy('departamento')
                        ->get();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Función para obtener los departamentos
     *
     * @return mixed
     */
    public function getGrupoJornada()
    {
        try {
            // Obtenemos los departamentos
            return DB::table('ETL_MSTR.dbo.etl_TEResumen')
                        ->distinct()
                        ->select('gpoJornada')
                        ->orderBy('gpoJornada')
                        ->whereNotNull('gpoJornada')
                        ->get();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Función para agregar las horas finales
     *
     * @param string $anio
     * @param string $numsemana
     * @param string $numempleado
     * @param string $valor
     * @param string $motivo
     * @return bool
     */
    public function addHrsFinales($anio, $numsemana, $numempleado, $valor, $motivo): bool
    {
        try {
            // Validamos si existen horas finales si no las creamos
            $horasFinales = HorasFinales::where('año', $anio)
                                        ->where('numsemana', $numsemana)
                                        ->where('numempleado', $numempleado)
                                        ->first();

            if ($horasFinales == null) {
                // Agregamos las horas finales
                $hrsFinales = HorasFinales::create([
                    'horas_finales' => $valor,
                    'año' => date('Y'),
                    'numsemana' => $numsemana,
                    'numempleado' => $numempleado,
                    'usuario_id' => auth()->user()->Id_Usuario,
                ]);
            } else {
                // Actualizamos las horas finales
                $horasFinales->horas_finales = $valor;
                $horasFinales->usuario_id = auth()->user()->Id_Usuario;
                $horasFinales->save();
            }

            // Guardamos el registro en la bitácora
            (new BitacoraController())->store(
                ($horasFinales == null ? 'Agregar' : 'Actualizar') . ' Horas Finales',
                $horasFinales != null ? $horasFinales->horas_finales : 0,
                $valor,
                auth()->user()->Id_Usuario,
                $horasFinales != null ? $horasFinales->idHorasFinales : $hrsFinales->idHorasFinales
            );

            // Guardamos el registro motivo
            DB::table('motivos_cambio_horas_finales')->insert([
                'motivo' => $motivo,
                'usuario_id' => auth()->user()->Id_Usuario,
                'horas_finales_id' => $horasFinales != null ? $horasFinales->idHorasFinales : $hrsFinales->idHorasFinales,
            ]);

            // Guardamos el registro en la bitácora
            (new BitacoraController())->store(
                'Agregar Motivo Horas Finales',
                null,
                'Se agregó el motivo: ' . $motivo,
                auth()->user()->Id_Usuario,
                $horasFinales != null ? $horasFinales->idHorasFinales : $hrsFinales->idHorasFinales
            );

            return true;

        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Función para obtener las horas finales
     *
     * @param string $anio
     * @param string $numsemana
     * @param string $numempleado
     * @return mixed
     */
    public function getHorasFinales($anio, $numsemana, $numempleado)
    {
        try {
            // Obtenemos las horas finales
            return HorasFinales::where('año', $anio)
                                ->where('numsemana', $numsemana)
                                ->where('numempleado', $numempleado)
                                ->first();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Función para obtener las horas finales de la vista
     *
     * @param string $anio
     * @param string $numsemana
     * @param string $numempleado
     * @return mixed
     */
    public function getVHorasFinales($anio, $numsemana, $numempleado)
    {
        try {
            // Obtenemos las horas finales
            return DB::table('v_HorasFinalesETL')->where('año', $anio)
                                ->where('numsemana', $numsemana)
                                ->where('numempleado', $numempleado)
                                ->first();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Función para obtener el ultimo comentario del cambio de horas finales
     *
     * @param string $horas_finales_id
     * @return array
     */
    public function getlastcomment($horas_finales_id)
    {
        try {
            // Obtenemos el ultimo comentario
            return response()->json([
                'hf' => DB::table('motivos_cambio_horas_finales')
                ->where('horas_finales_id', $horas_finales_id)
                ->orderBy('created_at', 'desc')
                ->first(),
                'response' => true,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'hf' => null,
                'response' => false,
            ]);
        }
    }
}
