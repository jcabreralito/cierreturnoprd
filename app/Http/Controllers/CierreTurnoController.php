<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\v_ListadoActividades;
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
     * Función para obtener el listado de actividades
     *
     * @param array $data
     * @return mixed
     */
    public function getListadoActividades($data)
    {
        $fecha_cierre = Carbon::parse($data['fecha_cierre'])->format('d/m/Y');

        if (!isset($data['filtroSort'])) {
            $data['filtroSort'] = 'ID';
        }

        if (!isset($data['filtroSortType'])) {
            $data['filtroSortType'] = 'asc';
        }

        if (!isset($data['maquina'])) {
            $data['maquina'] = null;
        }

        return DB::table('v_ListadoActividades')
                        ->when($data['fecha_cierre'] != null, function ($query) use ($fecha_cierre) {
                            // date format d/m/Y
                            return $query->where('FechaProduccion', $fecha_cierre);
                        })
                        ->when($data['operador'] != null, function ($query) use ($data) {
                            $operador = explode('-', $data['operador'])[0];
                            return $query->where('NumEmpleado', $operador);
                        })
                        ->when($data['maquina'] != null, function ($query) use ($data) {
                            return $query->where('Maquina', $data['maquina']);
                        })
                        ->when($data['turno'] != null && $data['turno'] != 3, function ($query) use ($data) {
                            return $query->where('Turno', $data['turno']);
                        })
                        ->orderBy($data['filtroSort'], $data['filtroSortType'])
                        ->get();
    }

    /**
     * Función para obtener el detalle de eficiencia
     *
     * @param $data
     * @param int $tipo 1 = Normal, 2 = Pegadora
     * @return mixed
     */
    public function getReporte($data)
    {
        $fecha_cierre = Carbon::parse($data['fecha_cierre'])->format('d/m/Y');

        $tipo = $data['tipo_reporte_generar'];

        if ($tipo == 1) {
            $reporte = DB::select('SET NOCOUNT ON; exec sp_GetEficienciaOperador ?, ?, ?, ?', [
                explode('-', $data['operador'])[0],
                $data['turno'],
                $fecha_cierre,
                $data['maquina']
            ]);
        } else {
            $reporte = DB::select('SET NOCOUNT ON; exec sp_GetEficienciaOperadorPegadora ?, ?, ?, ?', [
                explode('-', $data['operador'])[0],
                $data['turno'],
                $fecha_cierre,
                $data['maquina']
            ]);
        }

        if ($reporte == null) {
            $reporte = [];
        }

        if ($reporte) {
            $reporte = collect($reporte)->map(function ($item) use ($tipo) {
                return [
                    'AjustesNormales' => $item->NumAjustesL,
                    'AjustesLiteratura' => $item->NumAjustesVW,
                    'CantTiros' => $item->CantTiro,
                    'EnTiempoTiros' => $item->SeDebioHacerEnTiem,
                    'SeDebioHacer' => $item->SeDebioHacerEnVel,
                    'TiempoReportado' => $item->TiempoTotal,
                    'TiempoDeAjuste' => $item->TieAjuste,
                    'TiempoDeTiro' => $item->TieTiro,
                    'AjusteStd' => $item->StdAjusteL,
                    'AjusteVWStd' => $item->StdAjusteVW,
                    'VelocidadStd' => $item->StdTiro,
                    'GLOBAL' => $item->EfiGlobal,
                    'CONVENCIONAL' => $item->EfiGlobal,

                    'TieSinTrab' => $item->TieSinTrab,
                    'VelPromedio' => $item->VelPromedio,
                    'TieAjusPro' => $item->TieAjusPro,
                    'SeDebioHacerEnVel' => $item->SeDebioHacerEnVel,
                    'SeDebioHacerEnTiem' => $item->SeDebioHacerEnTiem,
                    'TotalTiempoMuerto' => $item->TiempoMuerto,
                    'Tipo' => $tipo,
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
        $nombre_operador = $data['operador'];
        $supervisorFirma = User::where('Login', $data['firma_supervisor'])->first()?->Nombre;
        $operadorFirma = User::where('Login', $data['firma_operador'])->first()->Nombre;

        $fecha_inicio = Carbon::parse($data['fecha_cierre'])->format('d/m/Y');
        $fecha_fin = Carbon::parse($data['fecha_cierre'])->format('d/m/Y');
        $maquina = $data['maquina'];

        $reporte_eficiencia = $this->getReporte([
                                'fecha_cierre' => $data['fecha_cierre'],
                                'operador' => $data['operador'],
                                'maquina' => $data['maquina'],
                                'turno' => $data['turno'],
                                'tipo_reporte_generar' => $data['tipo_reporte_generar'],
                            ]);
        $reporte_detalle = $this->getListadoActividades($data);

        $titulo = 'Cierre de turno';

        $dompdf = App::make("dompdf.wrapper");
        $dompdf->loadView("pdf/reporteMaquina", ['reporte_eficiencia' => $reporte_eficiencia, 'reporte_detalle' => $reporte_detalle, 'titulo' => $titulo, 'fecha_inicio' => $fecha_inicio, 'fecha_fin' => $fecha_fin, 'operador' => $nombre_operador, 'maquina' => $maquina, 'grupo' => '', 'supervisorFirma' => $supervisorFirma, 'operadorFirma' => $operadorFirma]);

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

                    $dataDetalles = (new DetallesReporteEficienciaController())->storeDetallesReporteEficiencia([
                        'tiempo_ajuste_promedio' => $data['reporteActual'][0]['TieAjusPro'],
                        'num_ajustes' => $data['reporteActual'][0]['AjustesNormales'],
                        'num_ajustes_literatura' => $data['reporteActual'][0]['AjustesLiteratura'],
                        'tiempo_ajustes' => $data['reporteActual'][0]['TiempoDeAjuste'],
                        'se_debio_realizar_en_ajustes' => $data['reporteActual'][0]['SeDebioHacerEnTiem'],
                        'velocidad_promedio' => $data['reporteActual'][0]['VelPromedio'],
                        'num_tiros' => $data['reporteActual'][0]['CantTiros'],
                        'tiempo_tiros' => $data['reporteActual'][0]['TiempoDeTiro'],
                        'se_debio_realizar_en_tiros' => $data['reporteActual'][0]['SeDebioHacerEnVel'],
                        'en' => $data['reporteActual'][0]['TiempoReportado'],
                        'debio_hacerce_en' => $data['reporteActual'][0]['SeDebioHacerEnTiem'] + $data['reporteActual'][0]['SeDebioHacerEnVel'],
                        'tiempo_muerto' => $data['reporteActual'][0]['TotalTiempoMuerto'],
                        'eficiencia_global' => $data['reporteActual'][0]['GLOBAL'],
                        'std_ajuste_normal' => $data['reporteActual'][0]['AjusteStd'],
                        'std_ajuste_literatura' => $data['reporteActual'][0]['AjusteVWStd'],
                        'std_velocidad_tiro' => $data['reporteActual'][0]['VelocidadStd'],
                        'tipo_reporte' => $data['reporteActual'][0]['Tipo'],
                        'reporte_id' => $reporte->id,
                    ]);
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
                    'tipo_reporte_generar' => $reporteCT->tipo_reporte_generar,
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

                $dataDetalles = (new DetallesReporteEficienciaController())->updateDetallesReporteEficiencia([
                    'tiempo_ajuste_promedio' => $reporte[0]['TieAjusPro'],
                    'num_ajustes' => $reporte[0]['AjustesNormales'],
                    'num_ajustes_literatura' => $reporte[0]['AjustesLiteratura'],
                    'tiempo_ajustes' => $reporte[0]['TiempoDeAjuste'],
                    'se_debio_realizar_en_ajustes' => $reporte[0]['SeDebioHacerEnTiem'],
                    'velocidad_promedio' => $reporte[0]['VelPromedio'],
                    'num_tiros' => $reporte[0]['CantTiros'],
                    'tiempo_tiros' => $reporte[0]['TiempoDeTiro'],
                    'se_debio_realizar_en_tiros' => $reporte[0]['SeDebioHacerEnVel'],
                    'en' => $reporte[0]['TiempoReportado'],
                    'debio_hacerce_en' => $reporte[0]['SeDebioHacerEnTiem'] + $reporte[0]['SeDebioHacerEnVel'],
                    'tiempo_muerto' => $reporte[0]['TotalTiempoMuerto'],
                    'eficiencia_global' => $reporte[0]['GLOBAL'],
                    'std_ajuste_normal' => $reporte[0]['AjusteStd'],
                    'std_ajuste_literatura' => $reporte[0]['AjusteVWStd'],
                    'std_velocidad_tiro' => $reporte[0]['VelocidadStd'],
                    'tipo_reporte' => $reporte[0]['Tipo'],
                    'reporte_id' => $reporteCT->id,
                ]);

                // Guardamos el archivo PDF
                $pdf = (new CierreTurnoController())->imprimirReporte([
                    'fecha_cierre' => $reporteCT->fecha_cierre,
                    'operador' => $reporteCT->operador . '-' . $reporteCT->nombre_operador,
                    'maquina' => $reporteCT->maquina,
                    'turno' => $reporteCT->turno,
                    'tipo_reporte' => $reporteCT->tipo_reporte,
                    'firma_supervisor' => $reporteCT->firma_supervisor,
                    'firma_operador' => $reporteCT->firma_operador,
                    'tipo_reporte_generar' => $reporteCT->tipo_reporte_generar,
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
                'tipo_reporte_generar' => $reporteCT->tipo_reporte_generar
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
                ->where('Personal', intval($operador))
                ->value('Departamento');

        if ($area == 'ACABADOS ESPECIALES' || $area == 'ACABADO LITOGRAFIA') {
            return DB::table('v_Supervisores')
                    ->whereIn('Departamento', ['ACABADOS ESPECIALES', 'ACABADO LITOGRAFIA'])
                    ->orderBy('Nombre', 'asc')
                    ->get();
        } else {
            return DB::table('v_Supervisores')
                    ->where('Departamento', $area)
                    ->orderBy('Nombre', 'asc')
                    ->get();
        }
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
                'tipo_reporte_generar' => $reporteCT->tipo_reporte_generar,
            ]);

            $archivo = (new DocumentoReporteController())->actualizarDocumentos($pdf, $reporteCT->id);

            (new BitacoraController())->registrarBitacora([
                'cambio_anterior' => null,
                'cambio_nuevo' => 'Firma de cierre de turno por parte del supervisor',
                'usuario_id' => auth()->user()->Id_Usuario,
                'reporte_id' => $reporteCT->id,
            ]);

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

    /**
     * Función para generar el reporte pdf sin guardar el cierre de turno
     *
     * @param array $data
     * @return mixed
     */
    public function generarPDF(array $data)
    {
        // Antes de generar el PDF, validamos si ya existe un reporte guardado
        if ($data['reporte_id'] != null) {
            $reporteCT = (new DocumentoReporteController())->obtenerPdf($data['reporte_id']);

            if ($reporteCT) {
                return $reporteCT->archivo;
            }
        } else {
            $reporte_eficiencia = $this->getReporte([
                                'fecha_cierre' => $data['fecha_cierre'],
                                'operador' => $data['operador'],
                                'maquina' => $data['maquina'],
                                'turno' => $data['turno'],
                                'tipo_reporte_generar' => $data['tipo_reporte_generar'],
                            ]);

            $reporte_detalle = $this->getListadoActividades($data);
            $nombre_operador = $data['operador'];
            $fecha_inicio = Carbon::parse($data['fecha_cierre'])->format('d/m/Y');
            $fecha_fin = Carbon::parse($data['fecha_cierre'])->format('d/m/Y');
            $maquina = $data['maquina'];
            $tipo_reporte = $data['tipo_reporte'];
            $titulo = 'Cierre de turno';

            $dompdf = App::make("dompdf.wrapper");
            $dompdf->loadView("pdf/reporteMaquina", [
                'reporte_eficiencia' => $reporte_eficiencia,
                'reporte_detalle' => $reporte_detalle,
                'titulo' => $titulo,
                'fecha_inicio' => $fecha_inicio,
                'fecha_fin' => $fecha_fin,
                'operador' => $nombre_operador,
                'maquina' => $maquina,
                'grupo' => '',
                'reporte' => $tipo_reporte,
                'supervisorFirma' => '',
                'operadorFirma' => '',
            ]);

            // Guardar el PDF en storage/app/public/pdfarchivo.pdf
            $output = $dompdf->output();
            $filename = 'pdfarchivo-'. date('YmdHis').'.pdf';
            Storage::disk('public')->put($filename, $output);

            // Obtener el contenido y convertirlo a base64
            $pdfContent = Storage::disk('public')->get($filename);

            // Al guardarlo lo eliminamos
            Storage::disk('public')->delete($filename);

            return base64_encode($pdfContent);
        }
    }
}
