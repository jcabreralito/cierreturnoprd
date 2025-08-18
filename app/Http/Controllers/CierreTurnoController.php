<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class CierreTurnoController extends Controller
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
     * Función para el listado de actividades
     *
     * @param array $data
     * @return mixed
     */
    public function getActividades($data)
    {
        $fecha_inicio = Carbon::parse($data['fecha_cierre'])->format('d/m/Y');
        $fecha_fin = Carbon::parse($data['fecha_cierre'])->format('d/m/Y');

        if ($data['operador'] == null) {
            $operador = '';
        } else {
            $operador = explode('-', $data['operador'])[0];
        }

        if ($data['maquina'] == null) {
            $maquina = '';
        } else {
            $maquina = $data['maquina'];
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
        }

        try {
            $reporte = DB::select('SET NOCOUNT ON; execute MetricsWeb.dbo.ListaProcesos ?, ?, ?, ?, ?, ?, ?', [
                $fecha_inicio,
                $fecha_fin,
                $maquina,
                $operador,
                $turno,
                '',
                $reporte,
            ]);
        } catch (\Throwable $th) {
            $reporte = [];
        }

        return $reporte;
    }

    /**
     * Función para obtener el detalle de eficiencia
     *
     * @param $data
     * @return mixed
     */
    public function getReporte($data)
    {
        $fecha_inicio = Carbon::parse($data['fecha_cierre'])->format('d/m/Y');
        $fecha_fin = Carbon::parse($data['fecha_cierre'])->format('d/m/Y');

        if ($data['operador'] == null) {
            $operador = '';
        } else {
            $operador = explode('-', $data['operador'])[0];
        }

        if ($data['maquina'] == null) {
            $maquina = '';
        } else {
            $maquina = $data['maquina'];
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
        } else {
            $reporte = [];
        }

        return $reporte;
    }

    /**
     * Función para imprimir el reporte
     *
     * @param $data
     * @return mixed
     */
    public function imprimirReporte($data)
    {
        $fecha_inicio = Carbon::parse($data['fecha_cierre'])->format('d/m/Y');
        $fecha_fin = Carbon::parse($data['fecha_cierre'])->format('d/m/Y');

        if ($data['operador'] == null) {
            $operador = '';
        } else {
            $operador = explode('-', $data['operador'])[0];
        }

        if ($data['maquina'] == null) {
            $maquina = '';
        } else {
            $maquina = $data['maquina'];
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
        }

        $reporte_detalle = DB::select('SET NOCOUNT ON; execute MetricsWeb.dbo.ListaProcesos ?, ?, ?, ?, ?, ?, ?', [
            $fecha_inicio,
            $fecha_fin,
            $maquina,
            $operador,
            $turno,
            '',
            $reporte,
        ]);

        $maquinas = DB::select("SELECT *  FROM MetricsWeb.dbo.MaquinasMetrics WHERE maquina NOT LIKE '%SUAJE-01%' AND maquina NOT LIKE '%CORTE_TRI-01%'
        AND Maquina NOT LIKE '%SUAJE-01%' AND Maquina NOT LIKE '%KOMORI 428%' ORDER BY Maquina ASC");
        $Grupos = DB::select("SELECT DISTINCT Grupo FROM ETL_MSTR.dbo.etl_ParamStdMaquinas WHERE Grupo IS NOT NULL ORDER BY Grupo ASC");
        $user = auth()->user()->Login;
        $datos_usuario = DB::select('exec MetricsWeb.dbo.GetPermisosReportesProduccion @Login=' . $user);
        $permiso = $datos_usuario[0]->Permiso;
        $titulo = 'Cierre de turno';

        $dompdf = App::make("dompdf.wrapper");
        $dompdf->loadView("pdf/reporteMaquina", ['reporte_eficiencia' => $reporte_eficiencia, 'reporte_detalle' => $reporte_detalle, 'titulo' => $titulo, 'Grupos' => $Grupos, 'maquinas' => $maquinas, 'permiso' => $permiso, 'tiporeporte' => $reporte, 'fecha_inicio' => $fecha_inicio, 'fecha_fin' => $fecha_fin, 'operador' => $operador, 'maquina' => $maquina, 'grupo' => '', 'reporte' => $reporte])
        ;

        return $dompdf->stream('Reporte Producción.pdf'); //Para visualizar en el navegador
    }
}
