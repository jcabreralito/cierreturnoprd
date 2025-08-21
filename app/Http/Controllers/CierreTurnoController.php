<?php

namespace App\Http\Controllers;

use App\Models\DetalleReporte;
use Carbon\Carbon;
use Dom\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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

        $nombre_operador = $data['operador'];

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
        $dompdf->loadView("pdf/reporteMaquina", ['reporte_eficiencia' => $reporte_eficiencia, 'reporte_detalle' => $reporte_detalle, 'titulo' => $titulo, 'Grupos' => $Grupos, 'maquinas' => $maquinas, 'permiso' => $permiso, 'tiporeporte' => $reporte, 'fecha_inicio' => $fecha_inicio, 'fecha_fin' => $fecha_fin, 'operador' => $nombre_operador, 'maquina' => $maquina, 'grupo' => '', 'reporte' => $reporte]);

        // Guardar el PDF en storage/app/public/pdfarchivo.pdf
        $output = $dompdf->output();
        $filename = 'pdfarchivo-'. date('YmdHis').'.pdf';
        Storage::disk('public')->put($filename, $output);

        // Obtener el contenido y convertirlo a base64
        $pdfContent = Storage::disk('public')->get($filename);

        $base64 = base64_encode($pdfContent);

        // Al guardarlo lo eliminamos
        Storage::disk('public')->delete($filename);

        // Puedes retornar el base64, guardarlo en la base de datos, etc.
        return [
            'archivo' => $base64
        ];
    }

    /**
     * Función para realizar el cierre de turno
     *
     * @param array $data
     */
    public function cerrarTurno(array $data)
    {
        try {
            // 1 Realizar registro de cierre de turno
            $reporte = (new ReporteController())->guardarReporte($data['reporte']);

            if ($reporte) {
                if (count($data['reporteActual']) > 0) {
                    // 2 Guardar adicional del reporte
                    $dataAdicional = [
                        'ajustes_normales' => $data['reporteActual'][0]->AjustesNormales,
                        'ajustes_literatura' => $data['reporteActual'][0]->AjustesLiteratura,
                        'tiros' => $data['reporteActual'][0]->CantTiros,
                        'en' => $data['reporteActual'][0]->EnTiempoTiros,
                        'se_debio_hacer_en' => $data['reporteActual'][0]->SeDebioHacer,
                        'tiempo_reportado' => $data['reporteActual'][0]->TiempoReportado,
                        'tiempo_ajuste' => $data['reporteActual'][0]->TiempoDeAjuste,
                        'tiempo_tiro' => $data['reporteActual'][0]->TiempoDeTiro,
                        'tiempo_muerto' => $data['reporteActual'][0]->TotalTiempoMuerto,
                        'std_ajuste_normal' => $data['reporteActual'][0]->AjusteStd,
                        'std_ajuste_literatura' => $data['reporteActual'][0]->AjusteVWStd,
                        'std_velocidad_tiro' => $data['reporteActual'][0]->VelocidadStd,
                        'reporte_id' => $reporte->id,
                    ];

                    $adicional = (new DetalleReporteController())->registrarDetalles($dataAdicional);
                }

                if (!$data['contieneRazones']) {
                    // 3 Guardar razones del cierre
                    $razones = (new RazonReporteController())->registrarRazon([
                            'observaciones' => $data['razones']['observaciones'],
                            'acciones_correctivas' => $data['razones']['acciones_correctivas'],
                            'reporte_id' => $reporte->id,
                        ]);
                }

                // 4 Guardamos el archivo PDF
                $pdf = (new CierreTurnoController())->imprimirReporte($data['reporte']);
                $archivo = (new DocumentoReporteController())->registrarDocumentos([
                        'archivo' => $pdf['archivo'],
                        'reporte_id' => $reporte->id,
                    ]);

                // 5 Guardamos la bitacora
                $bitacora = (new BitacoraController())->registrarBitacora([
                    'cambio_anterior' => null,
                    'cambio_nuevo' => 'Registro de cierre de turno',
                    'usuario_id' => auth()->user()->Id_Usuario,
                    'reporte_id' => $reporte->id,
                ]);
            }
        } catch (\Exception $e) {
            return "Lo siento, ocurrió un error al realizar el cierre de turno.";
        }
    }

    /**
     * Función para validar si ya se realizó el cierre de turno de una consulta
     *
     * @param array $data
     * @return bool
     */
    public function yaRealizoCierre(array $data): bool
    {
        $reporte = (new ReporteController())->obtenerReporte($data);

        return $reporte != null;
    }
}
