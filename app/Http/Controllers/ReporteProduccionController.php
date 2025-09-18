<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\PDF;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ReporteProduccionController extends Controller
{
    /**
     * Función para obtener los operadores
     *
     * @return mixed
     */
    public function getOperadores($search = '')
    {
        $search = "'" . '%' . $search . '%' . "'";

        if ($search == '') {
            $autocomplate = DB::select("SELECT * FROM MetricsWeb.dbo.Operadores WHERE CodOperador IS NOT NULL OR CodOperador <> '0' ORDER BY NomeOperador ASC");
        } else {
            $autocomplate = DB::select("SELECT * FROM MetricsWeb.dbo.Operadores WHERE CONCAT(CodOperador,NomeOperador) LIKE " . $search . " ORDER BY NomeOperador ASC");
        }

        return collect($autocomplate)->map(function ($item) {
            return [
                "value" => $item->CodOperador,
                "label" => $item->CodOperador . '-' . $item->NomeOperador,
                "operador" => $item->NomeOperador
            ];
        });
    }

    /**
     * Función para obtener las máquinas
     *
     * @return mixed
     */
    public function getMaquinas()
    {
        $maquinas = DB::select("SELECT *  FROM MetricsWeb.dbo.MaquinasMetrics WHERE maquina NOT LIKE '%SUAJE-01%' AND maquina NOT LIKE '%CORTE_TRI-01%'
                        AND Maquina NOT LIKE '%SUAJE-01%' AND Maquina NOT LIKE '%KOMORI 428%' ORDER BY Maquina ASC");

        return collect($maquinas)->map(function ($item) {
            return [
                "value" => $item->Maquina,
                "label" => $item->Maquina
            ];
        });
    }

    /**
     * Función para obtener los grupos
     *
     * @return mixed
     */
    public function getGrupos()
    {
        $grupos = DB::select("SELECT DISTINCT Grupo FROM ETL_MSTR.dbo.etl_ParamStdMaquinas WHERE Grupo IS NOT NULL ORDER BY Grupo ASC");

        return collect($grupos)->map(function ($item) {
            return [
                "value" => $item->Grupo,
                "label" => $item->Grupo
            ];
        });
    }

    /**
     * Función para obtener el reporte de producción
     *
     * @param $data
     * @return mixed
     */
    public function apiReporte($data)
    {
        $maquinas = [];
        $fecha_desde = Carbon::parse($data['fecha_desde'])->format('d/m/Y');
        $fecha_hasta = Carbon::parse($data['fecha_hasta'])->format('d/m/Y');

        if (!isset($data['filtroSort'])) {
            $data['filtroSort'] = 'ID';
        }

        if (!isset($data['filtroSortType'])) {
            $data['filtroSortType'] = 'asc';
        }

        if (!isset($data['maquina'])) {
            $data['maquina'] = null;
        }

        if ($data['grupo'] != null && $data['grupo'] != '') {
            $maquinas = DB::select("SELECT DISTINCT maquina FROM etl_mstr.dbo.etl_ParamStdMaquinas WHERE Grupo LIKE '%" . $data['grupo'] . "%'");
        }

        return DB::table('v_ListadoActividades')
                                ->when($data['operador'] != null && $data['operador'] != "", function ($query) use ($data) {
                                    $operador = explode('-', $data['operador'])[0];
                                    return $query->where('NumEmpleado', $operador);
                                })
                                ->when($data['maquina'] != null && $data['maquina'] != "", function ($query) use ($data) {
                                    return $query->where('Maquina', $data['maquina']);
                                })
                                ->when($data['turno'] != null && $data['turno'] != 3, function ($query) use ($data) {
                                    return $query->where('Turno', $data['turno']);
                                })
                                ->when($data['grupo'] != null && $data['grupo'] != "", function ($query) use ($data, $maquinas) {
                                    return $query->whereIn('Maquina', collect($maquinas)->pluck('maquina')->toArray());
                                })
                                ->whereBetween('fechaproduccion', [$fecha_desde, $fecha_hasta])
                                ->orderBy($data['filtroSort'], $data['filtroSortType'])
                                ->get();
    }

    /**
     * Función para obtener el reporte de los calculos de eficiencia
     *
     * @param $data
     * @return mixed
     */
    public function getReporte($data)
    {
        $fecha_inicio = Carbon::parse($data['fecha_desde'])->format('d/m/Y');
        $fecha_fin = Carbon::parse($data['fecha_hasta'])->format('d/m/Y');

        if ($data['operador'] == null) {
            $operador = '';
        } else {
            $operador = $data['operador'];
        }

        if ($data['maquina'] == null) {
            $maquina = '';
        } else {
            $maquina = $data['maquina'];
        }

        if ($data['grupo'] == null) {
            $grupo = '';
        } else {
            $grupo = $data['grupo'];
        }

        if ($data['turno'] == 3) {
            $turno = '';
        } else {
            $turno = $data['turno'];
        }

        if ($data['tipo_reporte'] == 'Maquina' || $data['tipo_reporte'] == 'Operador') {
            try {
                $reporte = DB::select('SET NOCOUNT ON; exec MetricsWeb.dbo.GrupoDetalleProceso ?, ?, ?, ?, ?', [
                    $fecha_inicio,
                    $fecha_fin,
                    $operador,
                    $turno,
                    $maquina,
                ]);
            } catch (\Throwable $th) {
                $reporte = [];
            }
        } else if ($data['tipo_reporte'] == 'Maquina' || $data['tipo_reporte'] == 'Grupo') {
            try {
                $reporte = DB::select('SET NOCOUNT ON; exec MetricsWeb.dbo.GrupoDetalleProcesoG ?, ?, ?, ?, ?', [
                    $fecha_inicio,
                    $fecha_fin,
                    $operador,
                    $turno,
                    $grupo,
                ]);
            } catch (\Throwable $th) {
                $reporte = [];
            }
        } else {
            $reporte = [];
        }

        return $reporte;
    }

    /**
     * Función para imprimir el reporte
     *
     * @param Request $request
     * @return PDF
     */
    public function imprimirReporte(Request $request)
    {
        $data = $request->all();
        $fecha_inicio = Carbon::parse($data['fecha_desde'])->format('d/m/Y');
        $fecha_fin = Carbon::parse($data['fecha_hasta'])->format('d/m/Y');

        if ($data['operador'] == null) {
            $operador = '';
        } else {
            $operador = $data['operador'];
        }

        if ($data['maquina'] == null) {
            $maquina = '';
        } else {
            $maquina = $data['maquina'];
        }

        if ($data['grupo'] == null) {
            $grupo = '';
        } else {
            $grupo = $data['grupo'];
        }

        if ($data['turno'] == 3) {
            $turno = '';
        } else {
            $turno = $data['turno'];
        }

        $tipo_reporte = $data['tipo_reporte'];

        if ($tipo_reporte == "Maquina") {
            $reporte = 'M';
        } else if ($tipo_reporte == "Operador") {
            $reporte = 'O';
        } else if ($tipo_reporte == "Grupo") {
            $reporte = 'G';
        }

        if ($reporte == 'M' || $reporte == 'O') {
            try {
                $reporte_eficiencia = DB::select('SET NOCOUNT ON; exec MetricsWeb.dbo.GrupoDetalleProceso ?, ?, ?, ?, ?', [
                    $fecha_inicio,
                    $fecha_fin,
                    $operador,
                    $turno,
                    $maquina,
                ]);
            } catch (\Throwable $th) {
                $reporte_eficiencia = [];
            }
        } else if ($reporte == 'G') {
            try {
                $reporte_eficiencia = DB::select('SET NOCOUNT ON; exec MetricsWeb.dbo.GrupoDetalleProcesoG ?, ?, ?, ?, ?', [
                    $fecha_inicio,
                    $fecha_fin,
                    $operador,
                    $turno,
                    $grupo,
                ]);
            } catch (\Throwable $th) {
                $reporte_eficiencia = [];
            }
        }

        $reporte_detalle = DB::select('SET NOCOUNT ON; execute MetricsWeb.dbo.ListaProcesos ?, ?, ?, ?, ?, ?, ?', [
            $fecha_inicio,
            $fecha_fin,
            $maquina,
            $operador,
            $turno,
            $grupo,
            $reporte,
        ]);

        $maquinas = DB::select("SELECT *  FROM MetricsWeb.dbo.MaquinasMetrics WHERE maquina NOT LIKE '%SUAJE-01%' AND maquina NOT LIKE '%CORTE_TRI-01%'
        AND Maquina NOT LIKE '%SUAJE-01%' AND Maquina NOT LIKE '%KOMORI 428%' ORDER BY Maquina ASC");
        $Grupos = DB::select("SELECT DISTINCT Grupo FROM ETL_MSTR.dbo.etl_ParamStdMaquinas WHERE Grupo IS NOT NULL ORDER BY Grupo ASC");
        $user = "'" . auth()->user()->Login . "'";
        $datos_usuario = DB::select('exec MetricsWeb.dbo.GetPermisosReportesProduccion @Login=' . $user);
        $permiso = $datos_usuario[0]->Permiso;
        $titulo = 'Reporte de Producción';

        // return $reporte_eficiencia;

        $dompdf = App::make("dompdf.wrapper");
        $dompdf->loadView("pdf/reporteMaquinaRp", ['reporte_eficiencia' => $reporte_eficiencia, 'reporte_detalle' => $reporte_detalle, 'titulo' =>  $titulo, 'permiso' => $permiso, 'Grupos' => $Grupos, 'maquinas' => $maquinas, 'tiporeporte' => $reporte, 'fecha_inicio' => $fecha_inicio, 'fecha_fin' => $fecha_fin, 'operador' => $operador, 'maquina' => $maquina, 'grupo' => $grupo, 'reporte' => $reporte])
            // ->save('archivo.pdf')
        ;

        // Convertimos en blob para enviar a la vista
        $pdf = $dompdf->output();
        return response($pdf, 200)->header('Content-Type', 'application/pdf');
    }
}
