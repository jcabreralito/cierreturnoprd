<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CierreTurnoController extends Controller
{
    /**
     * Función para obtener los estatus de los cierres
     *
     * @return array
     */
    public function getEstatus()
    {
        return DB::table('v_Estatus')->where('estatus', 1)->get();
    }

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

        $reporte = DB::select('SET NOCOUNT ON; exec sp_GetEficienciaOperador ?, ?, ?', [
            $operador,
            $turno,
            $fecha_inicio
        ]);

        if ($reporte == null) {
            $reporte = [];
        }

        if ($reporte) {
            $reporte = collect($reporte)->map(function ($item) {
                return [
                    'AjustesNormales' => $item->NumAjustesL,
                    'AjustesLiteratura' => $item->NumAjustesVW,
                    'CantTiros' => $item->CantTiro,
                    'EnTiempoTiros' => $item->SeDebioHacerEnTiem,
                    'SeDebioHacer' => $item->SeDebioHacerEnVel,
                    'TiempoReportado' => $item->TieDisponible,
                    'TiempoDeAjuste' => $item->TieAjuste,
                    'TiempoDeTiro' => $item->TieTiro,
                    'TotalTiempoMuerto' => $item->TMPropio + $item->TMAjeno,
                    'AjusteStd' => $item->StdAjusteL,
                    'AjusteVWStd' => $item->StdAjusteVW,
                    'VelocidadStd' => $item->VelPromedio,
                    'GLOBAL' => ($item->EfiGlobal > 100 ? 100 : ($item->EfiGlobal < 0 ? 0 : $item->EfiGlobal)),
                    'CONVENCIONAL' => $item->EfiGlobal,

                    'TieSinTrab' => $item->TieSinTrab,
                    'VelPromedio' => $item->VelPromedio,
                    'TieAjusPro' => $item->TieAjusPro,
                    'SeDebioHacerEnVel' => $item->SeDebioHacerEnVel,
                    'SeDebioHacerEnTiem' => $item->SeDebioHacerEnTiem,
                ];
            });
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
        $supervisorFirma = User::where('Login', $data['firma_supervisor'])->first()?->Nombre;
        $operadorFirma = User::where('Login', $data['firma_operador'])->first()->Nombre;

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
                $reporte_eficiencia = $this->getReporte([
                    'fecha_cierre' => $data['fecha_cierre'],
                    'operador' => $data['operador'],
                    'maquina' => $data['maquina'],
                    'turno' => $data['turno'],
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
        $dompdf->loadView("pdf/reporteMaquina", ['reporte_eficiencia' => $reporte_eficiencia, 'reporte_detalle' => $reporte_detalle, 'titulo' => $titulo, 'Grupos' => $Grupos, 'maquinas' => $maquinas, 'permiso' => $permiso, 'tiporeporte' => $reporte, 'fecha_inicio' => $fecha_inicio, 'fecha_fin' => $fecha_fin, 'operador' => $nombre_operador, 'maquina' => $maquina, 'grupo' => '', 'reporte' => $reporte, 'supervisorFirma' => $supervisorFirma, 'operadorFirma' => $operadorFirma]);

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
                        'ajustes_normales' => $data['reporteActual'][0]['AjustesNormales'],
                        'ajustes_literatura' => $data['reporteActual'][0]['AjustesLiteratura'],
                        'tiros' => $data['reporteActual'][0]['CantTiros'],
                        'en' => $data['reporteActual'][0]['EnTiempoTiros'],
                        'velocidad_promedio' => $data['reporteActual'][0]['VelPromedio'],
                        'se_debio_hacer_en' => $data['reporteActual'][0]['SeDebioHacer'],
                        'tiempo_reportado' => $data['reporteActual'][0]['TiempoReportado'],
                        'tiempo_ajuste' => $data['reporteActual'][0]['TiempoDeAjuste'],
                        'tiempo_tiro' => $data['reporteActual'][0]['TiempoDeTiro'],
                        'tiempo_muerto' => $data['reporteActual'][0]['TotalTiempoMuerto'],
                        'std_ajuste_normal' => $data['reporteActual'][0]['AjusteStd'],
                        'std_ajuste_literatura' => $data['reporteActual'][0]['AjusteVWStd'],
                        'std_velocidad_tiro' => $data['reporteActual'][0]['VelocidadStd'],
                        'eficiencia_global' => $data['reporteActual'][0]['GLOBAL'],
                        'reporte_id' => $reporte->id,
                    ];

                    $adicional = (new DetalleReporteController())->registrarDetalles($dataAdicional);
                }

                if (!$data['contieneRazones']) {
                    foreach ($data['razones']['causas'] as $causa) {
                        $causa = (new CausaController())->registrarCausa([
                            'causa' => $causa,
                            'reporte_id' => $reporte->id,
                            'estatus' => 1
                        ]);
                    }

                    foreach ($data['razones']['compromisos'] as $compromiso) {
                        $compromiso = (new CompromisoController())->registrarCompromiso([
                            'compromiso' => $compromiso,
                            'reporte_id' => $reporte->id,
                            'estatus' => 1
                        ]);
                    }
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

    /**
     * Función para realizar el re-cálculo de un cierre
     *
     * @param int $id
     * @return void
     */
    public function reCalculo($id)
    {
        try {
            $reporteCT = (new ReporteController())->obtenerReportePorId($id);

            if ($reporteCT) {

                $reporte = $this->getReporte([
                    'fecha_cierre' => $reporteCT->fecha_cierre,
                    'operador' => $reporteCT->operador . '-' . $reporteCT->nombre_operador,
                    'maquina' => $reporteCT->maquina,
                    'turno' => $reporteCT->turno,
                ]);

                $dataAdicional = [
                    'ajustes_normales' => $reporte[0]['AjustesNormales'],
                    'ajustes_literatura' => $reporte[0]['AjustesLiteratura'],
                    'tiros' => $reporte[0]['CantTiros'],
                    'en' => $reporte[0]['EnTiempoTiros'],
                    'velocidad_promedio' => $reporte[0]['VelPromedio'],
                    'se_debio_hacer_en' => $reporte[0]['SeDebioHacer'],
                    'tiempo_reportado' => $reporte[0]['TiempoReportado'],
                    'tiempo_ajuste' => $reporte[0]['TiempoDeAjuste'],
                    'tiempo_tiro' => $reporte[0]['TiempoDeTiro'],
                    'tiempo_muerto' => $reporte[0]['TotalTiempoMuerto'],
                    'std_ajuste_normal' => $reporte[0]['AjusteStd'],
                    'std_ajuste_literatura' => $reporte[0]['AjusteVWStd'],
                    'std_velocidad_tiro' => $reporte[0]['VelocidadStd'],
                    'eficiencia_global' => $reporte[0]['GLOBAL'],
                ];

                $adicional = (new DetalleReporteController())->actualizarDetalles($dataAdicional, $reporteCT->id);

                // Guardamos el archivo PDF
                $pdf = (new CierreTurnoController())->imprimirReporte([
                    'fecha_cierre' => $reporteCT->fecha_cierre,
                    'operador' => $reporteCT->operador . '-' . $reporteCT->nombre_operador,
                    'maquina' => $reporteCT->maquina,
                    'turno' => $reporteCT->turno,
                    'tipo_reporte' => $reporteCT->tipo_reporte,
                    'firma_supervisor' => $reporteCT->firma_supervisor,
                    'firma_operador' => $reporteCT->firma_operador,
                ]);

                $archivo = (new DocumentoReporteController())->actualizarDocumentos($pdf, $reporteCT->id);

                // 3 Guardamos la bitacora
                $bitacora = (new BitacoraController())->registrarBitacora([
                    'cambio_anterior' => null,
                    'cambio_nuevo' => 'Re-cálculo de cierre de turno',
                    'usuario_id' => auth()->user()->Id_Usuario,
                    'reporte_id' => $reporteCT->id,
                ]);

                // Actualizamos la fecha de re-cálculo y el estatus
                (new ReporteController())->actualizarRecalculo($reporteCT->id);
            }
        } catch (\Exception $e) {
            return "Lo siento, ocurrió un error al realizar el re-cálculo del cierre.";
        }
    }

    /**
     * Función para obtener el detalle de eficiencia por id de reporte
     *
     * @param int $id
     * @return mixed
     */
    public function getDataEficiencia($id)
    {
        $reporteCT = (new ReporteController())->obtenerReportePorId($id);

        if ($reporteCT) {
            $reporte = $this->getReporte([
                'fecha_cierre' => $reporteCT->fecha_cierre,
                'operador' => $reporteCT->operador . '-' . $reporteCT->nombre_operador,
                'maquina' => $reporteCT->maquina,
                'turno' => $reporteCT->turno,
            ]);
        } else {
            $reporte = [];
        }

        return $reporte;
    }

    /**
     * Función para obtener los supervisores
     *
     * @param $operador
     * @return mixed
     */
    public function getSupervisores($operador)
    {
        $operador = explode('-', $operador)[0];

        $area = DB::table('Lito.dbo.Personal')
                ->where('personal', $operador)
                ->value('Departamento');

        return DB::table('v_Supervisores')
                ->where('Departamento', $area)
                ->orderBy('Nombre', 'asc')
                ->get();
    }

    /**
     * Función para obtener los supervisores (general)
     *
     * @return mixed
     */
    public function getSupervisoresGeneral()
    {
        return DB::table('v_Supervisores')
                ->get();
    }

    /**
     * Función para finalizar el cierre de turno por parte del supervisor
     *
     * @param $id
     * @param $supervisor
     * @return mixed
     */
    public function finalizarFirmaSupervisor($id, $supervisor)
    {
        try {
            $data = [
                'firma_supervisor' => $supervisor,
                'fecha_firma_supervisor' => Carbon::now()->format('Y-d-m H:i:s'),
                'estatus' => 2,
            ];

            $reporteCT = (new ReporteController())->obtenerReportePorId($id);

            // Guardamos el archivo PDF
            $pdf = (new CierreTurnoController())->imprimirReporte([
                'fecha_cierre' => $reporteCT->fecha_cierre,
                'operador' => $reporteCT->operador . '-' . $reporteCT->nombre_operador,
                'maquina' => $reporteCT->maquina,
                'turno' => $reporteCT->turno,
                'tipo_reporte' => $reporteCT->tipo_reporte,
                'firma_supervisor' => $supervisor,
                'firma_operador' => $reporteCT->firma_operador,
            ]);

            $archivo = (new DocumentoReporteController())->actualizarDocumentos($pdf, $reporteCT->id);

            return (new ReporteController())->actualizarFirmaSupervisor($data, $id);
        } catch (\Exception $e) {
            return "Lo siento, ocurrió un error al finalizar la firma del supervisor.";
        }
    }

    /**
     * Función para rechazar el cierre de turno por parte del supervisor
     *
     * @param int $id
     * @param string $motivoRechazo
     * @return mixed
     */
    public function rechazarCierreTurno($id, $motivoRechazo)
    {
        try {
            (new ReporteController())->actualizarEstatus(3, $id);

            // Guardamos el motivo de rechazo
            (new MotivoRechazoController())->registrarMotivoRechazo($motivoRechazo, $id);

            // Guardamos la bitacora
            (new BitacoraController())->registrarBitacora([
                'cambio_anterior' => null,
                'cambio_nuevo' => 'Rechazo de cierre de turno. Motivo: ' . $motivoRechazo,
                'usuario_id' => auth()->user()->Id_Usuario,
                'reporte_id' => $id,
            ]);
        } catch (\Exception $e) {
            return "Lo siento, ocurrió un error al rechazar el cierre de turno.";
        }
    }

    /**
     * Función para corregir el cierre
     *
     * @param int $id
     * @param array $observaciones
     * @param array $acciones_correctivas
     * @return void
     */
    public function corregirCierre($id, $observaciones, $acciones_correctivas)
    {
        try {
            // Actualizamos el estatus del reporte a Abierto (1)
            (new ReporteController())->actualizarEstatus(1, $id);

            // Eliminamos las causas y compromisos anteriores
            DB::table('Causas')->where('reporte_id', $id)->delete();
            DB::table('Compromisos')->where('reporte_id', $id)->delete();

            // Registramos las nuevas causas
            foreach ($observaciones as $causa) {
                (new CausaController())->registrarCausa([
                    'causa' => $causa,
                    'reporte_id' => $id,
                    'estatus' => 1
                ]);
            }

            // Registramos los nuevos compromisos
            foreach ($acciones_correctivas as $compromiso) {
                (new CompromisoController())->registrarCompromiso([
                    'compromiso' => $compromiso,
                    'reporte_id' => $id,
                    'estatus' => 1
                ]);
            }

            // Guardamos la bitacora
            (new BitacoraController())->registrarBitacora([
                'cambio_anterior' => null,
                'cambio_nuevo' => 'Corrección de cierre de turno',
                'usuario_id' => auth()->user()->Id_Usuario,
                'reporte_id' => $id,
            ]);
        } catch (\Exception $e) {
            return "Lo siento, ocurrió un error al corregir el cierre de turno.";
        }
    }
}
