<?php

namespace App\Http\Controllers;

use App\Models\Jornada;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JornadasController extends Controller
{
    /**
     * Función para obtener la ultima jornada iniciada
     *
     * 1 = jornada iniciada, 2 = jornada finalizada
     * @return mixed
     */
    public function obtenerJornadaIniciada()
    {
        try {
            return Jornada::where('estatus', 1)
                    ->where('usuario_id', auth()->user()->Id_Usuario)
                    ->first();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Función para obtener el catalogo de jornadas
     *
     * @return mixed
     */
    public function obtenerCatJornadas()
    {
        try {
            return DB::table('cat_jornadas')
                    ->where('estatus', 1)
                    ->select('id', 'jornada', 'horario')
                    ->orderBy('jornada', 'asc')
                    ->get();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Función para obtener el catalogo de configuración de jornadas
     *
     * @return mixed
     */
    public function obtenerCatConfigJornadas()
    {
        try {
            return DB::table('cat_config_jornadas')
                    ->where('estatus', 1)
                    ->get();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Función para obtener el catalogo de configuración de jornadas
     *
     * @return mixed
     */
    public function obtenerCatConfigJornadasTbl()
    {
        try {
            return DB::table('cat_config_jornadas')
                    ->whereNotIn('id', [21])
                    ->where('estatus', 1)
                    ->get();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Función para obtener el catalogo de grupo de jornadas
     *
     * @return mixed
     */
    public function obtenerCatGpoJornadas()
    {
        try {
            return DB::table('cat_grupo_jornadas')
                    ->select('id', 'grupo')
                    ->orderBy('grupo', 'asc')
                    ->get();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Función para validar si la jornada no excede el límite de horas por ley
     *
     * @param $jornadaRelId
     * @param $valor
     * @param $dia
     * @return array
     */
    public function validarHorasPorLey($jornadaRelId, $valor, $dia, $previousValue)
    {
        try {
            // Verificamos si al actualizar la jornada se esta excediendo el límite de horas por ley
            $verificarSemana = DB::table('v_RelacionJornadas')
                ->where('rjId', $jornadaRelId)
                ->first();

            if ($verificarSemana) {

                if ($verificarSemana->hrs_jornada_final > $verificarSemana->defaultHrs) {
                    $this->actualizarJornada($jornadaRelId, $previousValue, $dia);
                    $this->verificarSiFinalizoSemana($jornadaRelId);
                    return [
                        'response' => false,
                        'message' => 'La jornada no puede ser actualizada, ya que se excede el límite de horas por ley.'
                    ];
                } else {
                    return [
                        'response' => true,
                    ];
                }
            }
        } catch (\Exception $e) {
            return [
                'response' => false,
                'message' => 'Error al validar las horas por ley'
            ];
        }
    }

    /**
     * Función para actualizar la jornada
     *
     * @return mixed
     */
    public function actualizarJornada($id, $valor, $dia)
    {
        try {
            $previousValue = DB::table('relacion_jornadas')
                ->where('id', $id)
                ->value($dia);

            DB::table('relacion_jornadas')
                ->where('id', $id)
                ->update([$dia => ($valor == "" ? null : $valor)]);

            // Generamos el registro de la bitácora
            $bitacora = new BitacoraController();
            $bitacora->store(
                'Jornadas',
                'Cambio de id jornada: ' . $previousValue . ' del dia:' . $dia,
                'Cambio de id jornada ' . $valor . ' del dia:' . $dia,
                auth()->user()->Id_Usuario,
                $id
            );

            return [
                'response' => true,
                'previo' => $previousValue,
            ];
        } catch (\Exception $e) {
            return [
                'response' => false,
                'message' => 'Error al actualizar la jornada'
            ];
        }
    }

    /**
     * Función para verificar si ya se finalizo la semana
     *
     * @param $jornadaId
     * @return mixed
     */
    public function verificarSiFinalizoSemana($jornadaId)
    {
        try {
            $jornada = DB::table('v_RelacionJornadas')
                ->where('rjId', $jornadaId)
                ->first();

            if ($jornada) {
                // Verificamos si del lunes a domingo ya se asignaron jornadas
                $dias = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'];
                $totalHrs = 0;

                // Filtrar los días válidos (que no sean null ni tengan valor 13)
                $diasValidos = collect($dias)->every(fn($dia) => $jornada->$dia !== null);

                // Verificar si hay al menos un día válido con jornada asignada
                if ($diasValidos) {

                    // Filtrar los días válidos que no sean 13
                    $diasValidos = collect($dias)->filter(fn($dia) => $jornada->$dia !== null && !in_array($jornada->$dia, [1,7,8]));

                    // Calcular las horas totales de los días válidos
                    $totalHrs = $diasValidos->reduce(function ($carry, $dia) use ($jornada) {
                        $catJornada = DB::table('cat_jornadas')
                            ->where('id', $jornada->$dia)
                            ->first();

                        return $catJornada ? $carry + $catJornada->diferencia : $carry;
                    }, 0);

                    // Verificar si todos los días válidos tienen el mismo tipo
                    $tiposUnicos = $diasValidos
                        ->map(fn($dia) => $jornada->{'tipo' . ucfirst($dia)}) // Obtener los tipos de los días válidos
                        ->filter(fn($tipo) => $tipo !== null) // Ignorar valores null
                        ->unique();

                    // Determinar el grupo de jornada
                    $grupoJornadaId = ($tiposUnicos->count() === 1)
                        ? ($tiposUnicos->first() == 1 ? 1 : ($tiposUnicos->first() == 2 ? 2 : 3))
                        : 3;

                    // Actualizar la tabla
                    DB::table('relacion_jornadas')
                        ->where('id', $jornadaId)
                        ->update([
                            'cat_grupo_jornada_id' => $grupoJornadaId,
                            'hrs_jornada_final' => $totalHrs,
                        ]);

                    if ($jornada->cat_config_jornadas_id != null) {

                        // Validamos si la jornada al ser modificada coincide con alguna configuración de jornada
                        $configJornada = DB::table('cat_config_jornadas')
                            ->where('lunes', $jornada->lunes)
                            ->where('martes', $jornada->martes)
                            ->where('miercoles', $jornada->miercoles)
                            ->where('jueves', $jornada->jueves)
                            ->where('viernes', $jornada->viernes)
                            ->where('sabado', $jornada->sabado)
                            ->where('domingo', $jornada->domingo)
                            ->where('estatus', 1)
                            ->select('id')
                            ->first();

                        DB::table('relacion_jornadas')
                            ->where('id', $jornadaId)
                            ->update([
                                'cat_config_jornadas_id' => ($configJornada != null ? $configJornada->id : -1),
                            ]);
                    } else {
                        DB::table('relacion_jornadas')
                            ->where('id', $jornadaId)
                            ->update([
                                'cat_config_jornadas_id' => null,
                            ]);
                    }

                    return true;
                } else {
                    // Si no hay días válidos, actualizar el estatus a 1
                    DB::table('relacion_jornadas')
                        ->where('id', $jornadaId)
                        ->update([
                            'estatus' => 1,
                            'cat_grupo_jornada_id' => null,
                            'hrs_jornada_final' => null,
                            'cat_config_jornadas_id' => null,
                        ]);
                    return true;
                }
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Función para colocar la configuración de la jornada
     *
     * @param $jornadaId
     * @param $valor
     * @return void
     */
    public function actualizarConfigJornada($jornadaId, $valor)
    {
        try {
            $jonada = DB::table('relacion_jornadas')
                ->where('id', $jornadaId)
                ->first();

            $configJornada = DB::table('cat_config_jornadas')
                ->where('id', $valor)
                ->first();

            if ($configJornada != null && $jonada != null) {
                // Calculamos el total de horas
                $totalHrs = 0;

                // Cache de jornadas
                $jornadasCache = [];
                foreach (DB::table('cat_jornadas')->select('id', 'diferencia')->get() as $j) {
                    $jornadasCache[$j->id] = $j->diferencia;
                }

                // IDs de los días
                $dias = [
                    $configJornada->lunes,
                    $configJornada->martes,
                    $configJornada->miercoles,
                    $configJornada->jueves,
                    $configJornada->viernes,
                    $configJornada->sabado,
                    $configJornada->domingo,
                ];

                // Sumar solo los que existen en el cache y no son null
                $totalHrs = array_reduce($dias, function($carry, $id) use ($jornadasCache) {
                    return $carry + (isset($jornadasCache[$id]) ? $jornadasCache[$id] : 0);
                }, 0);

                $data = [
                    'lunes' => $configJornada->lunes,
                    'martes' => $configJornada->martes,
                    'miercoles' => $configJornada->miercoles,
                    'jueves' => $configJornada->jueves,
                    'viernes' => $configJornada->viernes,
                    'sabado' => $configJornada->sabado,
                    'domingo' => $configJornada->domingo,
                    'cat_grupo_jornada_id' => $configJornada->cat_grupo_jornadas_id,
                    'hrs_jornada_final' => $totalHrs,
                    'cat_config_jornadas_id' => $valor,
                ];

                // Actualizamos la jornada
                DB::table('relacion_jornadas')
                    ->where('id', $jornadaId)
                    ->update($data);

                return [
                    'response' => true,
                    'data' => $data,
                ];
            } elseif ($configJornada == null) {
                // Si no existe la jornada, actualizamos el estatus a 1
                DB::table('relacion_jornadas')
                    ->where('id', $jornadaId)
                    ->update([
                        'lunes' => null,
                        'martes' => null,
                        'miercoles' => null,
                        'jueves' => null,
                        'viernes' => null,
                        'sabado' => null,
                        'domingo' => null,
                        'cat_grupo_jornada_id' => null,
                        'hrs_jornada_final' => null,
                        'cat_config_jornadas_id' => null,
                        'estatus' => 1,
                    ]);

                return [
                    'response' => true,
                    'data' => [],
                ];
            }

            return [
                'response' => false,
                'message' => 'No se pudo actualizar la configuración de la jornada.',
            ];
        } catch (\Exception $e) {
            // Manejo de excepciones
            return [
                'response' => false,
                'message' => 'No se pudo actualizar la configuración de la jornada.',
            ];
        }
    }

    /**
     * Función para verificar si ya se registro información de jornada para la semana actual
     *
     * @return mixed
     */
    public function verificarSiExistenRegistrosJornadas($semanaActual)
    {
        try {
            $count = DB::table('relacion_jornadas')
                        ->where('semana', $semanaActual)
                        ->where('estatus', 1)
                        ->count();

            return $count == 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Función para generar el reporte de jornadas
     *
     * @param Request $request
     * @return mixed
     */
    public function generateReportHrs(Request $request)
    {
        try {
            if ($request->semanaActual) {
                $consulta = DB::select('exec sp_obtenerPersonalXSemana ?', [$request->semanaActual]);

                return response()->json([
                    'response' => true,
                    'data' => $consulta,
                ]);
            }

            return response()->json([
                'response' => false,
                'message' => 'No se ha proporcionado una semana válida.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'response' => false,
                'message' => 'Error al generar el reporte de jornadas',
            ]);
        }
    }

    /**
     * Función para obtener el listado de configuraciones de jornadas
     *
     * @return mixed
     */
    public function getCatalogoConfiguraciones()
    {
        try {
            return DB::table('v_CatConfig')
                ->get();
        } catch (\Exception $e) {
            return null;
        }
    }
}
