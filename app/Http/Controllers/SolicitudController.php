<?php

namespace App\Http\Controllers;

use App\Models\Solicitud;
use App\Models\v_HorasExtras;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SolicitudController extends Controller
{
    /**
     * Función para obtener todas las solicitudes
     *
     * @param $data
     * @return mixed
     */
    public function index($data)
    {
        try {
            // Obtenemos los departamentos por usuario
            $departamentos = (new DepartamentoController())->getDepartamentosByUser(auth()->user()->Id_Usuario);
            $departamentos = $departamentos->pluck('id')->toArray();

            return v_HorasExtras::filters($data, $departamentos)
                ->orderBy($data['filtroSort'], $data['filtroSortType'])
                ->when($data['paginationF'] != 'todos', function ($query) use ($data) {
                    return $query->paginate($data['paginationF'], pageName: 'solicitudes');
                }, function ($query) {
                    return $query->get();
                });
        } catch (\Exception $e) {
            // Retornamos la respuesta
            return response()->json([
                'message' => 'Error al obtener las solicitudes',
            ], 500);
        }
    }

    /**
     * Función para obtener todas las solicitudes por personal
     *
     * @param $data
     * @return mixed
     */
    public function indexPp($data)
    {
        try {
            // Obtenemos los departamentos por usuario
            $departamentos = (new DepartamentoController())->getDepartamentosByUser(auth()->user()->Id_Usuario);
            $departamentos = $departamentos->pluck('id')->toArray();

            $solicitudes = v_HorasExtras::filters($data, $departamentos)
                                        ->get()->pluck('idSolicitud')->toArray();

            return DB::table('solicitud_usuarios as su')
                        ->leftJoin('v_Personal as p', 'p.Personal', '=', 'su.personal')
                        ->leftJoin('solicitudes as s', 's.id', '=', 'su.solicitud_id')
                        ->selectRaw("
                            su.personal,
                            p.nombre,
                            ISNULL(SUM(s.horas), 0) as numHorasSolicitudes,
                            ISNULL(SUM(su.horas_reportables), 0) as numHorasReales
                        ")
                        ->when($data['filtroPersonal'], function ($query) use ($data) {
                            return $query->where('su.personal', $data['filtroPersonal']);
                        })
                        ->when($data['filtroPersonalNombre'], function ($query) use ($data) {
                            return $query->where('p.nombre', 'like', '%' . $data['filtroPersonalNombre'] . '%');
                        })
                        ->whereIntegerInRaw('su.solicitud_id', $solicitudes)
                        ->groupBy('su.personal', 'p.nombre')
                        ->when($data['paginationF'] != 'todos', function ($query) use ($data) {
                            return $query->paginate($data['paginationF'], pageName: 'pesonal-solicitudes');
                        }, function ($query) {
                            return $query->get();
                        });
        } catch (\Exception $e) {
            // Retornamos la respuesta
            return "Error al obtener las solicitudes";
        }
    }

    /**
     * Función para obtener todas las solicitudes por personal sin en cuenta las solicitudes
     *
     * @param $data
     * @return mixed
     */
    public function indexPpNotSolicitudes($data)
    {
        try {
            $departamentos = [];

            if (auth()->user()->tipoUsuarioHorasExtra == 3) {
                if (in_array(auth()->user()->Id_Usuario, [12436, 12460])) {
                    $departamentos = (new DepartamentoController())->index(2);
                } else {
                    $departamentos = (new DepartamentoController())->index();
                }
            } else {
                $departamentos = (new DepartamentoController())->getDepartamentosByUser(auth()->user()->Id_Usuario);
            }

            $departamentos = $departamentos->pluck('id')->toArray();

            return DB::table('v_RelacionJornadas')
                        ->when($data['filtroPersonal'], function ($query) use ($data) {
                            return $query->where('personal', $data['filtroPersonal']);
                        })
                        ->when($data['filtroPersonalNombre'], function ($query) use ($data) {
                            return $query->where('nombre', 'like', '%' . $data['filtroPersonalNombre'] . '%');
                        })
                        ->when((auth()->user()->tipoUsuarioHorasExtra == 3 && !in_array(auth()->user()->Id_Usuario, [12436, 12460])), function ($query) use ($departamentos) {
                            return $query->whereIn('departamento_id', $departamentos);
                        })
                        ->when($data['filtroDepartamento'], function ($query) use ($data) {
                            return $query->where('departamento_id', $data['filtroDepartamento']);
                        })
                        ->when($data['filtroSemana'], function ($query) use ($data) {
                            return $query->where('semana', $data['filtroSemana']);
                        })
                        ->when($data['filtroJornada'], function ($query) use ($data) {
                            return $query->where(function ($query) use ($data) {
                                    $query->where('lunes', $data['filtroJornada'])
                                ->orWhere('martes', $data['filtroJornada'])
                                ->orWhere('miercoles', $data['filtroJornada'])
                                ->orWhere('jueves', $data['filtroJornada'])
                                ->orWhere('viernes', $data['filtroJornada'])
                                ->orWhere('sabado', $data['filtroJornada'])
                                ->orWhere('domingo', $data['filtroJornada']);
                            });
                        })
                        ->when($data['filtroGrupoJornada'], function ($query) use ($data) {
                            return $query->where('cgjId', $data['filtroGrupoJornada']);
                        })
                        ->when($data['filtroConfJornada'], function ($query) use ($data) {
                            return $query->where('config_id', $data['filtroConfJornada']);
                        })
                        ->when($data['filtroExcedente'], function ($query) use ($data) {
                            return $query->where('esExcedente', $data['filtroExcedente']);
                        })
                        ->when(auth()->user()->tipoUsuarioHorasExtra == 3 && in_array(auth()->user()->Id_Usuario, [12436, 12460]), function ($query) use ($departamentos) {
                            return $query->whereIn('departamento_id', $departamentos);
                        })
                        ->orderBy($data['filtroSort'], $data['filtroSortType'])
                        ->when($data['paginationF'] != 'todos', function ($query) use ($data) {
                            return $query->paginate($data['paginationF'], pageName: 'personal-jornadas');
                        }, function ($query) {
                            return $query->get();
                        });
        } catch (\Exception $e) {
            // Retornamos la respuesta
            return "Error al obtener las solicitudes";
        }
    }

    /**
     * Función para obtener las semanas
     *
     * @return mixed
     */
    public function getSemanas()
    {
        try {
            // Obtenemos el numero de semana actual
            $date = Carbon::now();
            $semana = DB::table('ETL_MSTR.dbo.etl_CatSemanas')
                ->where('FECHA', $date)
                ->first();

            $semanaActual = $semana->NUMSEMANA;

            $semanas = DB::table('v_Semanas')->select([
                'AÑO','MES','SEMANA', 'NUMSEMANA'
            ])
            ->where('AÑO', '>=', 2025)
            ->where(function($q) use ($semanaActual) {
                return $q->where('NUMSEMANA', '<=', $semanaActual)
                    ->where('AÑO', '<=', Carbon::now()->year);
            })
            ->get();

            // Eliminar las semanas repetidas
            return $semanas->unique('SEMANA');
        } catch (\Exception $e) {
            // Retornamos la respuesta
            return 'Error al obtener las semanas';
        }
    }

    /**
     * Función para obtener la semana actual
     *
     * @return mixed
     */
    public function getSemanaActualNormal()
    {
        try {
            $date = Carbon::now();
            $semana = DB::table('ETL_MSTR.dbo.etl_CatSemanas')
                ->where('FECHA', $date)
                ->first();
            return $semana->SEMCOMPLETA;
        } catch (\Exception $e) {
            // Retornamos la respuesta
            return 0;
        }
    }

    /**
     * Función para obtener la semana actual -1
     *
     * @return mixed
     */
    public function getSemanaActualMenosUna()
    {
        try {
            $date = Carbon::now();
            $semana = DB::table('ETL_MSTR.dbo.etl_CatSemanas')
                ->where('FECHA', $date)
                ->first();
            return $semana->SEMCOMPLETA;
        } catch (\Exception $e) {
            // Retornamos la respuesta
            return 0;
        }
    }

    /**
     * Función para obtener la semana actual pasada
     *
     * @return mixed
     */
    public function getSemanaPasada()
    {
        try {
            $date = Carbon::now()->subWeek();
            $semana = DB::table('ETL_MSTR.dbo.etl_CatSemanas')
                ->where('FECHA', $date)
                ->first();
            return $semana->SEMCOMPLETA;
        } catch (\Exception $e) {
            // Retornamos la respuesta
            return 0;
        }
    }

    /**
     * Función para obtener la semana actual
     *
     * @return mixed
     */
    public function getSemanaActual()
    {
        try {
            $date = Carbon::now();
            $semana = DB::table('ETL_MSTR.dbo.etl_CatSemanas')
                ->where('FECHA', $date)
                ->first();
            return $semana->SEMCOMPLETA;
        } catch (\Exception $e) {
            // Retornamos la respuesta
            return 0;
        }
    }

    /**
     * Función para obtener una solicitud por id
     *
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        try {
            return Solicitud::find($id);
        } catch (\Exception $e) {
            // Retornamos la respuesta
            return response()->json([
                'message' => 'Error al obtener la solicitud',
            ], 500);
        }
    }

    /**
     * Función para obtener una solicitud por id en la vista de solicitud
     *
     * @param $id
     * @return mixed
     */
    public function showV($id)
    {
        try {
            return DB::table('v_HorasExtras')
                        ->where('idSolicitud', $id)
                        ->first();
        } catch (\Exception $e) {
            // Retornamos la respuesta
            return [
                'message' => 'Error al obtener la solicitud',
            ];
        }
    }

    /**
     * Función para crear una solicitud
     *
     * @param $data
     * @return mixed
     */
    public function store($data)
    {
        try {
            // Realizamos el registro
            $solicitud = new Solicitud();
            $solicitud->observaciones = $data['observaciones'];
            $solicitud->departamento_id = $data['departamento_id'];
            $solicitud->maquina_id = $data['maquina_id'];
            $solicitud->turno_id = $data['turno_id'];
            $solicitud->desde_dia = $data['desde_dia'];
            $solicitud->num_repeticiones = $data['num_repeticiones'];
            $solicitud->horas = $data['horas'];
            $solicitud->motivo_id = $data['motivo_id'];
            $solicitud->num_max_usuarios = $data['num_max_usuarios'];
            $solicitud->estatus_id = (auth()->user()->tipoUsuarioHorasExtra == 3) ? 6 : 1;
            $solicitud->user_id = auth()->user()->Id_Usuario;
            $solicitud->folio = $this->getLastFolio() + 1;
            $solicitud->tipo = $data['tipo'];
            $solicitud->rebasa_max_usuarios = $this->validateMaxUsuarios($data['num_max_usuarios'], $data['maquina_id']);
            $solicitud->save();

            // Guardamos las ops
            $this->storeOps($data['ops'], $solicitud->id);

            // Retornamos la respuesta
            return $solicitud;
        } catch (\Exception $e) {
            // Retornamos la respuesta
            return response()->json([
                'message' => 'Error al registrar la solicitud',
            ], 500);
        }
    }

    /**
     * Función para validar si la solicitud rebasa el numero maximo de usuarios permitidos
     *
     * @param $numMax
     * @param $maquinaId
     * @return mixed
     */
    public function validateMaxUsuarios($numMax, $maquinaId)
    {
        try {
            return $numMax > (new MaquinaController())->getMaxPersonalMaquina($maquinaId) ? 1 : 2;
        } catch (\Exception $e) {
            // Retornamos la respuesta
            return 1;
        }
    }

    /**
     * Función para guardar las ops de una solicitud
     *
     * @param $ops
     * @param $solicitudId
     * @return mixed
     */
    public function storeOps($ops, $solicitudId)
    {
        try {
            $insertData = [];

            foreach ($ops as $op) {
                $insertData[] = [
                    'solicitud_id' => $solicitudId,
                    'op' => $op,
                ];
            }

            DB::table('ops_solicitudes')->insert($insertData);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Función para relacionar el personal con multiples solicitudes
     *
     * @param $solictudes
     * @param $personal
     * @return mixed
     */
    public function storeAsignColaborador($data)
    {
        try {
            $insertData = [];

            // Obtenemos las solicitudes por medio del folio
            $solicitudes = Solicitud::whereIn('id', $data['ids'])->get()->pluck('id')->toArray();

            // Eliminamos los personales duplicados
            $data['personal'] = array_unique($data['personal']);

            // Recorremos las solicitudes y el personal para construir el array de inserción
            foreach ($solicitudes as $solicitud) {
                foreach ($data['personal'] as $personal) {
                    $insertData[] = [
                        'solicitud_id' => $solicitud,
                        'personal' => strval(trim($personal)),
                        'user_id' => auth()->user()->Id_Usuario,
                    ];
                }

                // Generamos la bitácora
                (new BitacoraController())->store('Asignación de Colaborador', null, 'Colaborador asignado: ' .  strval(trim($personal)), auth()->user()->Id_Usuario, $solicitud);
            }

            // Realizamos la inserción en una sola operación
            DB::table('solicitud_usuarios')->insert($insertData);

            // Retornamos la respuesta
            return true;
        } catch (\Exception $e) {
            // Retornamos la respuesta
            return false;
        }
    }

    /**
     * Función para actualizar los personales de una solicitud
     *
     * @param $data
     * @return mixed
     */
    public function updateAsignColaborador($data)
    {
        try {
            // Obtenemos los ids de las solicitudes por medio del folio
            $solicitudes = Solicitud::whereIn('id', $data['folios'])->get()->pluck('id')->toArray();

            // Obtenemos los ids de las relaciones de personal con la solicitud que no se encuentran en el array de personales
            $personalesNoEnNuevos = DB::table('solicitud_usuarios')
                ->whereIn('solicitud_id', $solicitudes)
                ->whereNotIn('personal', $data['personal'])
                ->select('personal')
                ->get()->pluck('personal')->toArray();

            foreach ($solicitudes as $solicitud) {
                foreach ($personalesNoEnNuevos as $personal) {
                    // Generamos la bitácora
                    (new BitacoraController())->store('Eliminación de Colaborador', null, 'Colaborador eliminado: ' .  strval(trim($personal)), auth()->user()->Id_Usuario, $solicitud);
                }
            }

            // Eliminamos los personales de la solicitud
            DB::table('solicitud_usuarios')
                ->whereIn('solicitud_id', $solicitudes)
                ->whereIn('personal', $personalesNoEnNuevos)
                ->delete();

            // Realizamos la inserción
            $insertData = [];

            // Recorremos las solicitudes y el personal para construir el array de inserción
            foreach ($solicitudes as $solicitud) {
                foreach ($data['personal'] as $personal) {

                    // Verificamos si ya existe la relación
                    $exist = DB::table('solicitud_usuarios')
                        ->where('solicitud_id', $solicitud)
                        ->where('personal', $personal)
                        ->first();

                    if ($exist == null) {
                        $insertData[] = [
                            'solicitud_id' => $solicitud,
                            'personal' => strval(trim($personal)),
                            'user_id' => auth()->user()->Id_Usuario,
                        ];

                        // Generamos la bitácora
                        (new BitacoraController())->store('Asignación de Colaborador', null, 'Colaborador asignado: ' .  strval(trim($personal)), auth()->user()->Id_Usuario, $solicitud);
                    }
                }
            }

            // Realizamos la inserción en una sola operación
            DB::table('solicitud_usuarios')->insert($insertData);

            // Retornamos la respuesta
            return true;
        } catch (\Exception $e) {
            // Retornamos la respuesta
            return false;
        }
    }

    /**
     * Función para actualizar una solicitud
     *
     * @param $data
     * @param $id
     * @return mixed
     */
    public function update($data, $id)
    {
        try {
            // Realizamos el registro
            $solicitud = Solicitud::find($id);

            $solicitud->observaciones = $data['observaciones'];
            $solicitud->departamento_id = $data['departamento_id'];
            $solicitud->maquina_id = $data['maquina_id'];
            $solicitud->horas = $data['horas'];
            $solicitud->motivo_id = $data['motivo_id'];
            $solicitud->num_max_usuarios = $data['num_max_usuarios'];
            $solicitud->rebasa_max_usuarios = $this->validateMaxUsuarios($data['num_max_usuarios'], $data['maquina_id']);
            $solicitud->save();

            // Eliminamos las ops anteriores
            DB::table('ops_solicitudes')->where('solicitud_id', $id)->delete();

            // Guardamos las ops
            $this->storeOps($data['ops'], $id);

            // Retornamos la respuesta
            return $solicitud;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Función para obtener el ultimo folio de la solicitud
     *
     * @return mixed
     */
    public function getLastFolio()
    {
        try {
            return Solicitud::select('folio')->orderBy('id', 'desc')->first()->folio;
        } catch (\Exception $e) {
            // Retornamos la respuesta
            return 0;
        }
    }

    /**
     * Función para actualizar el estado de la solicitud
     *
     * @param $id
     * @param $estado
     * @return mixed
     */
    public function updateEstatus($id, $estado)
    {
        try {
            // Realizamos la actualización
            $solicitud = Solicitud::find($id);
            $solicitud->estatus_id = $estado;
            $solicitud->save();

            // Retornamos la respuesta
            return $solicitud;
        } catch (\Exception $e) {
            // Retornamos la respuesta
            return response()->json([
                'message' => 'Error al actualizar el estado de la solicitud',
            ], 500);
        }
    }

    /**
     * Funcion para obtener el nombre de un trabajo por Op
     *
     * @param $op
     * @return mixed
     */
    public function getNombreTrabajo($op)
    {
        try {
            // Verificamos si el valor de $op es 'l999999' o 'L999999'
            if (strtolower($op) === 'l999999') {
                return (object) [
                    'NumOrdem' => 'l999999',
                    'Descricao' => 'Trabajo sin OP'
                ];
            }

            // Si no es 'l999999', realizamos la consulta a la base de datos
            return DB::table('MetricsWeb.dbo.OrdensProducao')
                ->where('NumOrdem', $op)
                ->select('NumOrdem', 'Descricao')
                ->first();
        } catch (\Exception $e) {
            // Retornamos la respuesta
            return response()->json([
                'message' => 'Error al obtener el trabajo',
            ], 500);
        }
    }

    /**
     * Función para obtener el personal
     *
     * @param $data
     * @return mixed
     */
    public function getPersonal($data)
    {
        try {
            return DB::table('v_colaboradores')
                ->whereNotIn('Personal', $data['personales'])
                ->where(function($q) use ($data) {
                    $q->where('nombreCompleto', 'like', '%' . $data['search'] . '%')
                        ->orWhere('Personal', 'like', '%' . $data['search'] . '%');
                })
                ->where('Estatus', 'ALTA')
                ->select('Personal', 'Nombre', 'ApellidoMaterno', 'ApellidoPaterno', 'nombreCompleto')
                ->get();
        } catch (\Exception $e) {
            // Retornamos la respuesta
            return response()->json([
                'message' => 'Error al obtener la persona',
            ], 500);
        }
    }

    /**
     * Función para obtener el personal asignado a una solicitud
     *
     * @param $idSolicitud
     * @return mixed
     */
    public function getPersonalAsignado($idSolicitud)
    {
        try {
            $personales = DB::table('solicitud_usuarios')
            ->where('solicitud_id', $idSolicitud)
            ->select('personal')
            ->get()->pluck('personal')->toArray();

            $vC = DB::table('v_colaboradores')
            ->whereIn('Personal', $personales)
            ->select('Personal', 'Nombre', 'ApellidoMaterno', 'ApellidoPaterno', 'nombreCompleto')
            ->get();

            $data = [];

            foreach ($vC as $v) {
                $data[] = [
                    'personal' => $v->Personal,
                    'nombre' => $v->nombreCompleto,
                ];
            }

            return $data;

        } catch (\Exception $e) {
            // Retornamos la respuesta
            return response()->json([
                'message' => 'Error al obtener la persona',
            ], 500);
        }
    }

    /**
     * Función para obtener el personal asignado a una solicitud
     *
     * @param $id
     * @return mixed
     */
    public function showPersonal($id)
    {
        try {
            return DB::table('v_ColaboradoresSolicitudes')
                ->where('solicitud_id', $id)
                ->get();
        } catch (\Exception $e) {
            // Retornamos la respuesta
            return response()->json([
                'message' => 'Error al obtener la persona',
            ], 500);
        }
    }

    /**
     * Función para obtener las solicitudes de un personal
     *
     * @param Request $request
     * @return mixed
     */
    public function showSolicitudesPersonal(Request $request)
    {
        try {
            $solicitudes = DB::table('solicitud_usuarios')
                ->where('personal', $request->id)
                ->select('solicitud_id')
                ->get()->pluck('solicitud_id')->toArray();

            // Obtenemos los departamentos por usuario
            $departamentos = (new DepartamentoController())->getDepartamentosByUser(auth()->user()->Id_Usuario);
            $departamentos = $departamentos->pluck('id')->toArray();

            // Obtenemos las solicitudes
            return DB::table('v_HorasExtras as he')
                ->whereIntegerInRaw('idSolicitud', $solicitudes)
                ->when($request->filtroFolio, function ($query) use ($request) {
                    return $query->where('he.folio', $request->filtroFolio);
                })
                ->when($request->filtroDepartamento, function ($query) use ($request) {
                    return $query->where('he.departamento_id', $request->filtroDepartamento);
                })
                ->when($request->filtroMaquina, function ($query) use ($request) {
                    return $query->where('he.maquina_id', $request->filtroMaquina);
                })
                ->when($request->filtroEstatus, function ($query) use ($request) {
                    return $query->where('he.estatus_id', $request->filtroEstatus);
                })
                ->when($request->filtroMotivo, function ($query) use ($request) {
                    return $query->where('he.motivo_id', $request->filtroMotivo);
                })
                ->when($request->filtroTurno, function ($query) use ($request) {
                    return $query->where('he.turno_id', $request->filtroTurno);
                })
                ->when($request->filtroFecha, function ($query) use ($request) {
                    return $query->where('he.desde_dia', $request->filtroFecha);
                })
                ->when($request->filtroOp, function ($query) use ($request) {
                    // buscamos la op en la tabla de ops_solicitudes
                    return $query->whereIn('he.idSolicitud', function ($query) use ($request) {
                        $query->select('solicitud_id')
                            ->from('ops_solicitudes')
                            ->where('op', $request->filtroOp);
                    });
                })
                ->when($request->filtroObservaciones, function ($query) use ($request) {
                    return $query->where('he.observaciones', 'like', '%' . $request->filtroObservaciones . '%');
                })
                ->when($request->filtroSemana, function ($query) use ($request) {
                    return $query->whereIn('he.desde_dia', function ($query) use ($request) {
                        $query->select('FECHA')
                            ->from('ETL_MSTR.dbo.etl_CatSemanas')
                            ->where('SEMCOMPLETA', $request->filtroSemana);
                    });
                })
                ->when($request->filtroPersonalNombre, function ($query) use ($request) {
                    return $query->whereIn('he.idSolicitud', function ($query) use ($request) {
                        $query->select('solicitud_id')
                            ->from('solicitud_usuarios')
                            ->whereIn('personal', function ($query) use ($request) {
                                $query->select('Personal')
                                    ->from('v_Personal')
                                    ->where('nombre', 'like', '%' . $request->filtroPersonalNombre . '%');
                            });
                    });
                })
                ->when(auth()->user()->tipoUsuarioHorasExtra == 3 && !in_array(auth()->user()->Id_Usuario, [12436, 12460]), function ($query) use ($departamentos) {
                    return $query->whereIn('he.departamento_id', $departamentos);
                })
                ->leftJoin('solicitud_usuarios as su', 'su.solicitud_id', '=', 'he.idSolicitud')
                ->selectRaw("he.*, isNull(sum(su.horas_reportables), 0) as horasReales, he.semana")
                ->where('su.personal', $request->id)
                ->groupBy('he.idSolicitud',
                            'he.folio',
                            'he.desde_dia',
                            'he.num_repeticiones',
                            'he.horas',
                            'he.observaciones',
                            'he.departamento_id',
                            'he.maquina_id',
                            'he.motivo_id',
                            'he.estatus_id',
                            'he.turno_id',
                            'he.user_id',
                            'he.num_max_usuarios',
                            'he.rebasa_max_usuarios',
                            'he.fechaCreacionSolicitud',
                            'he.tipo',
                            'he.departamento',
                            'he.encargado_id',
                            'he.encargado2_id',
                            'he.maquina',
                            'he.motivo',
                            'he.estatus',
                            'he.usuario',
                            'he.turno',
                            'he.totalPersonalesRelacionados',
                            'he.totalOpsRelacionados',
                            'he.totalPersonalesAutorizados',
                            'he.maxHorasExtrasPersonal',
                            'he.semana',
                            'he.num_semana',
                            'he.cerrada'
                )
                ->get();
        } catch (\Exception $e) {
            return "Error al obtener las solicitudes";
        }
    }

    /**
     * Función para obtener el listado de Ops de una solicitud
     *
     * @param $id
     * @return mixed
     */
    public function getOps($id)
    {
        try {
            $ops = DB::table('ops_solicitudes')
            ->where('solicitud_id', $id)
            ->select('op')
            ->get()->pluck('op')->toArray();

            // Buscar los trabajos
            $data = [];

            foreach ($ops as $op) {
                $data[] = $this->getNombreTrabajo($op);
            }

            return collect($data);
        } catch (\Exception $e) {
            // Retornamos la respuesta
            return response()->json([
                'message' => 'Error al obtener las ops',
            ], 500);
        }
    }

    /**
     * Función para actualizar el estado de la relacion de personal con la solicitud
     *
     * @param $id,
     * @param $valor
     * @return mixed
     */
    public function updateStatusRelationColaborator($id, $valor)
    {
        try {
            // Realizamos la actualización
            DB::table('solicitud_usuarios')
                ->where('id', $id)
                ->update([
                    'estatus' => ($valor === true ? 1 : 2),
                    'user_id' => auth()->user()->Id_Usuario,
                    'updated_at' => DB::raw('GETDATE()'),
                ]);

            // Generamos la bitácora
            (new BitacoraController())->store('Actualización de Estatus (solicitud_usuarios)', null, 'Estatus actualizado: ' .  ($valor === true ? 'Activo' : 'Inactivo'), auth()->user()->Id_Usuario, $id);

            // Retornamos la respuesta
            return true;
        } catch (\Exception $e) {
            // Retornamos la respuesta
            return false;
        }
    }

    /**
     * Función para obtener el primer personal
     *
     * @param $data
     */
    public function searchPersonal($search = '', $personales = [])
    {
        try {
            return DB::table('v_colaboradores')
                ->whereNotIn('Personal', array_values($personales))
                ->where(function($q) use ($search) {
                    $q->where('nombreCompleto', 'like', '%' . $search . '%')
                        ->orWhere('Personal', 'like', '%' . $search . '%');
                })
                ->where('Estatus', 'ALTA')
                ->select('Personal', 'Nombre', 'ApellidoMaterno', 'ApellidoPaterno', 'nombreCompleto')
                ->first();
        } catch (\Exception $e) {
            // Retornamos la respuesta
            return response()->json([
                'message' => 'Error al obtener la persona',
            ], 500);
        }
    }

    /**
     * Función para obtener numero maximo de usuarios por solicitud
     *
     * @param $ids
     * @return mixed
     */
    public function getMaxPersonal($ids)
    {
        try {
            return DB::table('solicitudes')
                ->whereIn('id', $ids)
                ->select('num_max_usuarios')
                ->orderBy('num_max_usuarios', 'desc')
                ->first()->num_max_usuarios;
        } catch (\Exception $e) {
            // Retornamos la respuesta
            return 0;
        }
    }

    /**
     * Función para obtener los folios de las solicitudes
     *
     * @return mixed
     */
    public function getFolios()
    {
        try {
            return Solicitud::select('folio')->get()->pluck('folio')->toArray();
        } catch (\Exception $e) {
            // Retornamos la respuesta
            return [];
        }
    }

    /**
     * Función para obtener las solicitudes por folio y validar si todos tienen el mismo numero maximo de usuarios permitidos
     *
     * @param $folios
     * @return mixed
     */
    public function validateMaxPersonal($folios)
    {
        try {
            $numMax = Solicitud::whereIn('id', $folios)
                ->select('num_max_usuarios')
                ->orderBy('num_max_usuarios', 'desc')
                ->first()->num_max_usuarios;

            $solicitudes = Solicitud::whereIn('id', $folios)
                ->select('num_max_usuarios')
                ->get();

            $valid = true;

            foreach ($solicitudes as $solicitud) {
                if ($solicitud->num_max_usuarios != $numMax) {
                    $valid = false;
                    break;
                }
            }

            return $valid;
        } catch (\Exception $e) {
            // Retornamos la respuesta
            return false;
        }
    }

    /**
     * Actualizamos las horas de la solicitud - personal
     *
     * @param $idSol
     * @param $horas
     * @return mixed
     */
    public function updateHorasRelationColaborator($idSol, $horas)
    {
        try {
            // Realizamos la actualización
            DB::table('solicitud_usuarios')
                ->where('id', $idSol)
                ->update([
                    'horas_reportables' => $horas,
                    'updated_at' => DB::raw('GETDATE()'),
                ]);

            // Retornamos la respuesta
            return true;
        } catch (\Exception $e) {
            // Retornamos la respuesta
            return false;
        }
    }

    /**
     * Función para obtener una solicitud de relación por id
     *
     * @param $id
     * @return mixed
     */
    public function getSolicitudPersonal($id)
    {
        try {
            return DB::table('solicitud_usuarios')
                ->where('id', $id)
                ->first();
        } catch (\Exception $e) {
            // Retornamos la respuesta
            return response()->json([
                'message' => 'Error al obtener la solicitud',
            ], 500);
        }
    }

    /**
     * Función para obtener el folio de una solicitud por id de solicitud personal
     *
     * @param $id
     * @return mixed
     */
    public function getFolioBySolicitudPersonal($id)
    {
        try {
            return DB::table('solicitud_usuarios')
            ->where('id', $id)
            ->select('solicitud_id')
            ->first()->solicitud_id;
        } catch (\Exception $e) {
            // Retornamos la respuesta
            return 0;
        }
    }

    /**
     * Función para obtener los folios de las solicitudes por id
     *
     * @param $ids
     * @return mixed
     */
    public function getFoliosBySolicitud($ids)
    {
        try {
            return Solicitud::whereIn('id', $ids)->select('folio')
                ->get()->pluck('folio')->toArray();
        } catch (\Exception $e) {
            // Retornamos la respuesta
            return [];
        }
    }

    /**
     * Función para obtener el id de la solicitud por folio
     *
     * @param $folio
     * @return mixed
     */
    public function getIdByFolio($folio)
    {
        try {
            return Solicitud::where('folio', $folio)->select('id')->first()->id;
        } catch (\Exception $e) {
            // Retornamos la respuesta
            return 0;
        }
    }

    /**
     * Función para marcar multiples solicitudes como finalizadas
     *
     * @param $ids
     * @return mixed
     */
    public function updateStatusMultiples($ids, $estatus)
    {
        try {

            if ($estatus == 4) {
                $solicitudes = DB::table('solicitudes')
                    ->whereIn('id', $ids)->get();

                foreach ($solicitudes as $solicitud) {

                    if ($solicitud->estatus_id == 6) {
                        $estatusNew = 4;
                    } elseif($solicitud->estatus_id == 1) {
                        $estatusNew = 3;
                    }

                    DB::table('solicitudes')
                        ->where('id', $solicitud->id)
                        ->update([
                            'estatus_id' => $estatusNew,
                        ]);
                }
            } else {
                // Realizamos la actualización
                DB::table('solicitudes')
                    ->whereIn('id', $ids)
                    ->update([
                        'estatus_id' => $estatus,
                    ]);
            }


            // Retornamos la respuesta
            return true;
        } catch (\Exception $e) {
            // Retornamos la respuesta
            return false;
        }
    }

    /**
     * Función para eliminar una solicitud
     *
     * @param $id
     * @return mixed
     */
    public function destroy($id)
    {
        try {
            // Realizamos la eliminación
            $solicitud = Solicitud::find($id);

            // Verificamos si la solicitud existe
            if ($solicitud == null) {
                return false;
            }

            // Verificamos si la solicitud tiene personal asignado
            $personal = DB::table('solicitud_usuarios')
                ->where('solicitud_id', $id)
                ->first();

            if ($personal != null) {
                // Eliminamos la relación de personal con la solicitud
                DB::table('solicitud_usuarios')
                    ->where('solicitud_id', $id)
                    ->delete();
            }

            // Verificamos si la solicitud tiene ops asignadas
            $ops = DB::table('ops_solicitudes')
                ->where('solicitud_id', $id)
                ->first();

            if ($ops != null) {
                // Eliminamos la relación de ops con la solicitud
                DB::table('ops_solicitudes')
                    ->where('solicitud_id', $id)
                    ->delete();
            }

            // Eliminamos la solicitud
            $solicitud->delete();

            // Retornamos la respuesta
            return true;
        } catch (\Exception $e) {
            // Retornamos la respuesta
            return false;
        }
    }

    /**
     * Función para validar si el personal ya fue asignado a una solicitud del mismo dia
     *
     * @param $solicitudes
     * @param $personal
     * @return bool
     */
    public function validatePersonal($solicitudes, $personal)
    {
        try {
            // Buscamos el personal
            $exist = DB::table('solicitud_usuarios')
                ->where('personal', $personal)
                ->get()->pluck('solicitud_id')->toArray();

            // Obtenemos la fecha de la solicitud
            $solicitudesMismoDia = DB::table('solicitudes')
                ->whereIn('id', $exist)
                ->select('desde_dia')
                ->get()->pluck('desde_dia')->toArray();

            // Buscamos las solicitudes a las que se relaciona el personal
            $solicitudes = DB::table('solicitudes')
                ->whereIn('id', $solicitudes)
                ->select('desde_dia')
                ->get()->pluck('desde_dia')->toArray();

            // Verificamos si el personal ya fue asignado a una solicitud del mismo dia
            foreach ($solicitudesMismoDia as $key => $value) {
                if (in_array($value, $solicitudes)) {
                    return true;
                }
            }

            // Retornamos la respuesta
            return false;
        } catch (\Exception $e) {
            // Retornamos la respuesta
            return false;
        }
    }

    /**
     * Función para validar si la fecha de la solicitud que se va registrar este disonible y no ocupada por otra solicitud en el mismo dia
     *
     * @param $maquina
     * @param $fecha
     * @param $turno
     * @return bool
     */
    public function verificaFecha($maquina, $fecha, $turno)
    {
        try {
            // Buscamos la fecha
            $exist = DB::table('solicitudes')
                        ->where('maquina_id', $maquina)
                        ->where('desde_dia', $fecha)
                        ->where('turno_id', $turno)
                        ->first();

            // Verificamos si la fecha ya fue asignada a una solicitud
            if ($exist == null) {
                return true;
            }

            // Retornamos la respuesta
            return false;
        } catch (\Exception $e) {
            // Retornamos la respuesta
            return false;
        }
    }
}
