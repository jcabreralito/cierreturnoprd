<?php

namespace App\Http\Controllers;

use App\Models\Jornada;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrdController extends Controller
{
    /**
     * Función para realizar la insercion de datos incial
     *
     * @return void
     */
    public function insertData($sem = 13)
    {
        try {
            $numSemana = $sem;

            $semanaDias = DB::table('ETL_MSTR.dbo.etl_CatSemanas')->where('NUMSEMANA', $numSemana)->where('AÑO', 2025)->select(['SEMCOMPLETA', 'FECHA'])->get();

            $fechaInicio = (string) $semanaDias->first()->FECHA . 'T00:00:00';
            $fechaFin = (string) $semanaDias->last()->FECHA . 'T23:59:59';

            $semanaCompleta = $semanaDias->first()->SEMCOMPLETA;

            // Validamos si ya existe la semana
            $semanaExistente = DB::table('jornadas')->where('semana', $semanaCompleta)->first();

            if ($semanaExistente) {
                return response()->json(['message' => 'La semana ya existe. :('], 400);
            }

            // Cremos una jornada
            $jornadaDB = Jornada::create([
                'folio' => DB::table('jornadas')->max('folio') + 1,
                'semana' => $semanaCompleta,
                'estatus' => 2,
                'fecha_inicio_semana' => $fechaInicio,
                'fecha_fin_semana' => $fechaFin,
            ]);

            // Cache de jornadas para evitar consultas repetidas
            $jornadasCache = [];
            foreach (DB::table('cat_jornadas')->select('id', 'jornada')->get() as $j) {
                $jornadasCache[trim($j->jornada)] = $j->id;
            }

            // cache personales
            $personalesCache = [];
            $pd = DB::table('v_Personal')->select('Personal', 'Departamento')
                                        ->leftJoin('departamentos', 'departamentos.departamento', '=', 'v_Personal.Departamento')
                                        ->select('departamentos.id as departamento_id', 'v_Personal.Personal')
                                        ->get();
            foreach ($pd as $d) {
                $personalesCache[trim($d->Personal)] = $d->departamento_id;
            }

            $personalJornada = DB::table('lito.dbo.CalendarioJornada')
                ->whereBetween('FechaI', [$fechaInicio, $fechaFin])
                ->whereIn('Personal', $pd->pluck('Personal')->toArray())
                ->select([DB::raw('trim(personal) personal'), DB::raw('convert(date, FechaI) as fecha'), 'Jornada'])
                ->orderBy('Personal', 'asc')
                ->get();

            $personalSoloNumero = DB::table('lito.dbo.CalendarioJornada')
                ->whereBetween('FechaI', [$fechaInicio, $fechaFin])
                ->whereIn('Personal', $pd->pluck('Personal')->toArray())
                ->select([DB::raw('trim(personal) personal')])
                ->orderBy('Personal', 'asc')
                ->groupBy('personal')
                ->get();

            $data = [];

            foreach ($personalSoloNumero as $persona) {
                // Inicializa el registro con todos los días en null
                $registro = [
                    'personal' => $persona->personal,
                    'semana' => $semanaCompleta,
                    'lunes' => null,
                    'martes' => null,
                    'miercoles' => null,
                    'jueves' => null,
                    'viernes' => null,
                    'sabado' => null,
                    'domingo' => null,
                ];

                // Llena los días correspondientes para este personal
                foreach ($personalJornada as $pj) {
                    if ($pj->personal == $persona->personal) {
                        $diaSemana = $this->getDiaSemana($pj->fecha);
                        $jornadaId = $jornadasCache[trim($pj->Jornada)] ?? null;
                        if ($jornadaId !== null) {
                            $registro[$diaSemana] = $jornadaId;
                        }
                    }
                }

                // Determina el grupo de jornada
                $diasJornada = array_filter([
                    $registro['lunes'], $registro['martes'], $registro['miercoles'],
                    $registro['jueves'], $registro['viernes'], $registro['sabado'], $registro['domingo']
                ]);

                // Elimina valores 13
                $diasJornadaNo13 = array_filter($diasJornada, function ($value) {
                    return $value != 1 && $value != 7 && $value != 8;
                });

                $diasJornadaFinal = array_unique($diasJornadaNo13);

                $gruposJornadas = DB::table('cat_jornadas')
                    ->whereIn('id', $diasJornadaFinal)
                    ->select('tipojornada')
                    ->groupBy('tipojornada')
                    ->get();


                $tipoJornada = $gruposJornadas->pluck('tipojornada')->toArray();

                $tipoJornada = array_unique($tipoJornada);

                if (count($tipoJornada) == 1) {
                    $registro['cat_grupo_jornada_id'] = $tipoJornada[0];
                } else {
                    $registro['cat_grupo_jornada_id'] = 3; // Asignar grupo mixto por defecto
                }

                $jornada = DB::table('jornadas')->where('semana', $semanaCompleta)->first();

                $registro['departamento_id'] = $personalesCache[trim($persona->personal)] ?? null;
                $registro['estatus'] = 2;
                $registro['jornada_id'] = $jornada->id ?? null;

                $configJornada = DB::table('cat_config_jornadas')
                            ->where('lunes', $registro['lunes'])
                            ->where('martes', $registro['martes'])
                            ->where('miercoles', $registro['miercoles'])
                            ->where('jueves', $registro['jueves'])
                            ->where('viernes', $registro['viernes'])
                            ->where('sabado', $registro['sabado'])
                            ->where('domingo', $registro['domingo'])
                            ->where('estatus', 1)
                            ->select('id')
                            ->first();

                $registro['cat_config_jornadas_id'] = $configJornada ? $configJornada->id : null;

                // Calcular las horas totales de los días válidos
                $totalHrs = 0;

                foreach ($diasJornada as $dia) {
                    $catJornada = DB::table('cat_jornadas')
                        ->where('id', $dia)
                        ->first();

                    if ($catJornada) {
                        $totalHrs += $catJornada->diferencia;
                    }
                }

                $registro['hrs_jornada_final'] = $totalHrs;

                $data[] = $registro;
            }

            foreach ($data as $value) {
                DB::table('relacion_jornadas')->insert($value);
            }

            return response()->json(['message' => 'Datos insertados correctamente. Semana ' . $numSemana . ', Lista'], 200);

        } catch (\Exception $e) {
            dd($e->getMessage());
            return response()->json(['message' => 'Error al insertar los datos.'], 500);
        }
    }

    /**
     * Funcion para saber que dia de la semana es por fecha
     *
     * @param string $fecha
     * @return string
     */
    public function getDiaSemana($fecha)
    {
        $dias = ['domingo', 'lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];

        $timestamp = strtotime($fecha);
        $diaSemana = date('w', $timestamp);

        return $dias[$diaSemana];
    }

    /**
     * Función para guardar los grupos de configuracion de la semana
     *
     * @return void
     */
    public function guardarGrupos()
    {
        try {
            $grupos = [
                ['nombre' => 'DIA 1', 'lunes' => 'VW', 'martes' => 'VW', 'miercoles' => 'VW', 'jueves' => 'VW', 'viernes' => 'VW', 'sabado' => 'DESCANSO', 'domingo' => 'DESCANSO'],
                ['nombre' => 'DIA 2', 'lunes' => 'PREPRENSA', 'martes' => 'PREPRENSA', 'miercoles' => 'PREPRENSA', 'jueves' => 'PREPRENSA', 'viernes' => 'PREPRENSA', 'sabado' => 'DESCANSO', 'domingo' => 'DESCANSO'],
                ['nombre' => 'DIA 3', 'lunes' => 'OFICINA 1', 'martes' => 'OFICINA 1', 'miercoles' => 'OFICINA 1', 'jueves' => 'OFICINA 1', 'viernes' => 'OFICINA 1', 'sabado' => 'DESCANSO', 'domingo' => 'DESCANSO'],
                ['nombre' => 'DIA 4', 'lunes' => 'DIURNO SUP', 'martes' => 'DIURNO SUP', 'miercoles' => 'DIURNO SUP', 'jueves' => 'DIURNO SUP', 'viernes' => 'DESCANSO', 'sabado' => 'DESCANSO', 'domingo' => 'DESCANSO'],
                ['nombre' => 'DIA 5', 'lunes' => 'EMBARQUES', 'martes' => 'EMBARQUES', 'miercoles' => 'EMBARQUES', 'jueves' => 'EMBARQUES', 'viernes' => 'EMBARQUES', 'sabado' => 'DESCANSO', 'domingo' => 'DESCANSO'],
                ['nombre' => 'DIA 6', 'lunes' => 'INTENDENCIA', 'martes' => 'INTENDENCIA', 'miercoles' => 'INTENDENCIA', 'jueves' => 'INTENDENCIA', 'viernes' => 'INTENDENCIA', 'sabado' => 'INTEN SAB DIURNO', 'domingo' => 'DESCANSO'],
                ['nombre' => 'DIA 7', 'lunes' => 'INTEN TARDE', 'martes' => 'INTEN TARDE', 'miercoles' => 'INTEN TARDE', 'jueves' => 'INTEN TARDE', 'viernes' => 'INTEN TARDE', 'sabado' => 'INTEN SAB TARDE', 'domingo' => 'DESCANSO'],
                ['nombre' => 'DIA 8', 'lunes' => 'OFICINA 2', 'martes' => 'OFICINA 2', 'miercoles' => 'OFICINA 2', 'jueves' => 'OFICINA 2', 'viernes' => 'OFICINA 3', 'sabado' => 'DESCANSO', 'domingo' => 'DESCANSO'],
                ['nombre' => 'DIA 9', 'lunes' => 'OFICINA 4', 'martes' => 'OFICINA 4', 'miercoles' => 'OFICINA 4', 'jueves' => 'OFICINA 4', 'viernes' => 'OFICINA 4', 'sabado' => 'DESCANSO', 'domingo' => 'DESCANSO'],
                ['nombre' => 'DIA 10', 'lunes' => 'OFICINA 5', 'martes' => 'OFICINA 5', 'miercoles' => 'OFICINA 5', 'jueves' => 'OFICINA 5', 'viernes' => 'OFICINA 5', 'sabado' => 'DESCANSO', 'domingo' => "DESCANSO"],
                ['nombre' => "DIA 11", "lunes" => "DIGITAL DIURNO", "martes" => "DIGITAL DIURNO", "miercoles" => "DIGITAL DIURNO", "jueves" => "DIGITAL DIURNO", "viernes" => "DIGITAL DIURNO", "sabado" => "DESCANSO", "domingo" => "DESCANSO"],
                ['nombre' => "DIA 12", "lunes" => "NOCTURNO", "martes" => "NOCTURNO", "miercoles" => "DESCANSO", "jueves" => "DESCANSO", "viernes" => "DIURNO", "sabado" => "DIGITAL MIXTO", "domingo" => "DESCANSO"],
                ['nombre' => 'DIA 13', 'lunes' => 'NOCTURNO', 'martes' => 'NOCTURNO', 'miercoles' => 'DESCANSO', 'jueves' => 'DESCANSO', 'viernes' => 'DIURNO', 'sabado' => 'FESTIVO L', 'domingo' => 'DESCANSO'],
                ['nombre' => 'DIA 14', 'lunes' => 'VW', 'martes' => 'VW', 'miercoles' => 'VW', 'jueves' => 'FESTIVO C', 'viernes' => 'VW', 'sabado' => 'DESCANSO', 'domingo' => 'DESCANSO'],
                ['nombre' => 'DIURNO', 'lunes' => 'DIURNO', 'martes' => 'DIURNO', 'miercoles' => 'DIURNO', 'jueves' => 'DIURNO', 'viernes' => 'DESCANSO', 'sabado' => 'DESCANSO', 'domingo' => 'DESCANSO'],
                ['nombre' => 'MIXTO', 'lunes' => 'NOCTURNO', 'martes' => 'NOCTURNO', 'miercoles' => 'DESCANSO', 'jueves' => 'DESCANSO', 'viernes' => 'DIURNO', 'sabado' => 'DIURNO', 'domingo' => 'DESCANSO'],
                ['nombre' => 'MIXTO 2', 'lunes' => 'NOCTURNO SUP', 'martes' => 'NOCTURNO SUP', 'miercoles' => 'DESCANSO', 'jueves' => 'DESCANSO', 'viernes' => 'DIURNO SUP', 'sabado' => 'DIURNO SUP', 'domingo' => 'DESCANSO'],
                ['nombre' => 'NOCTURNO', 'lunes' => 'DESCANSO', 'martes' => 'DESCANSO', 'miercoles' => 'NOCTURNO', 'jueves' => 'NOCTURNO', 'viernes' => 'NOCTURNO', 'sabado' => 'NOCTURNO', 'domingo' => 'DESCANSO'],
                ['nombre' => 'NOCTURNO 2', 'lunes' => 'DESCANSO', 'martes' => 'DESCANSO', 'miercoles' => 'NOCTURNO', 'jueves' => 'NOCTURNO SUP', 'viernes' => 'NOCTURNO SUP', 'sabado' => 'NOCTURNO SUP', 'domingo' => 'DESCANSO'],
                ['nombre' => "NOCTURNO 3", "lunes" => "LITOGRAFIA NOCT", "martes" => "LITOGRAFIA NOCT", "miercoles" => "LITOGRAFIA NOCT", "jueves" => "LITOGRAFIA NOCT", "viernes" => "LITOGRAFIA NOCT", "sabado" => "DESCANSO", "domingo" => "DESCANSO"],
            ];

            // Cache de jornadas
            $catJornadasCache = [];
            $catJornadas = DB::table('cat_jornadas')->select('id', 'jornada')->get();
            foreach ($catJornadas as $j) {
                $catJornadasCache[trim($j->jornada)] = $j->id;
            }

            // Días de la semana
            $diasSemana = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'];

            // Construcción de $data
            $data = [];
            foreach ($grupos as $grupo) {
                $item = ['nombre' => $grupo['nombre']];
                $dataI = [];
                foreach ($diasSemana as $dia) {
                    $item[$dia] = $catJornadasCache[$grupo[$dia]] ?? null;
                    if (!in_array($item[$dia], [1, 7, 8])) { // Excluir valores 1, 7 y 8
                        $value = DB::table('cat_jornadas')->where('id', $item[$dia])->first();
                        if ($value) {
                            $dataI[] = $value->tipojornada;
                        }
                    }
                }


                $finalGrupo = array_unique($dataI);
                $fGrupo = count($finalGrupo) == 1 ? $finalGrupo[0] : 3;

                $item['cat_grupo_jornadas_id'] = $fGrupo;
                $data[] = $item;
            }

            foreach ($data as $value) {
                DB::table('cat_config_jornadas')->insert($value);
            }

            return response()->json(['message' => 'Grupos guardados correctamente.'], 200);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al guardar los grupos.'], 500);
        }
    }
}
