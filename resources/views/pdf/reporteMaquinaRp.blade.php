<!DOCTYPE html>
<html>

<head>
    <link rel="shortcut icon" href="/public/img/analisis.png" type="image/x-icon">
    <title>Reporte de Producción</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/styles.css">

    <style>
        @page {
            margin: .5cm .5cm;
            font-family: Arial;
        }

        #table-head {
            position: fixed;
            top: -1.5cm;
            left: 0cm;
            right: 0cm;
            height: auto;
            background-color: #fff;
            color: #000;
            text-align: center;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif">
    <header>
        <table id="table-head" width="100%" style="padding-top: 50px">
            <tbody>
                <tr>
                    <td width="25%" colspan="4" style="text-align: left;">
                        @component('sections/logoLito')
                        @endcomponent
                    </td>
                    <td width="75%" colspan="4" style="text-align: left; font-size: 20px !important;">
                        @if ($tiporeporte == 'M')
                            <b>REPORTE DE PROCESOS X MAQUINA</b>
                        @elseif ($tiporeporte == 'O')
                            <b style="margin-left: -80px">REPORTE DE PROCESOS X OPERADOR</b>
                        @else
                            <b>REPORTE DE PROCESOS X GRUPO</b>
                        @endif
                    </td>
                </tr>
                <tr>
                    @if ($tiporeporte == 'O')
                        <td style="font-weight: bold">
                            {{ $operador }}
                        </td>
                        <td colspan="3" style="font-weight: bold">
                            {{ $reporte_detalle[0]->Empleado }}
                        </td>
                    @elseif ($tiporeporte == 'M')
                        <td colspan="4" style="font-weight: bold; text-align: left">
                            {{ $maquina }}
                        </td>
                    @else
                        <td colspan="4" style="font-weight: bold; text-align: left">
                            {{ $grupo }}
                        </td>
                    @endif
                    <td></td>
                    <td></td>
                    <td style="font-weight: bold">Del : &nbsp; {{ $fecha_inicio }}</td>
                    <td style="font-weight: bold">Al : &nbsp; {{ $fecha_fin }}</td>
                </tr>
            </tbody>
        </table>
        <br>
    </header>
    <main>
        <table id="tblReporteImp" class="table-striped table-bordered"
            style="font-size: 8px !important; margin-top: 1.5cm;">
            <thead>
                <tr class="bg-blue-table" style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif">
                    <th style="text-align: center; width: 5%">Orden</th>
                    @if ($reporte == 'M')
                    <th style="text-align: center; width: 19%">Nombre Trabajo</th>
                    <th style="text-align: center; width: 18% !important">Descripción</th>
                    <th style="text-align: center; width: 8%">Operador</th>
                    @elseif ($reporte == 'O' || $reporte == 'G')
                    <th style="text-align: center; width: 15%">Nombre Trabajo</th>
                    <th style="text-align: center; width: 15%">Descripción</th>
                        <th style="text-align: center; width: 15%">Maquina</th>
                    @endif
                    <th style="text-align: center; width: 12%">Proceso</th>
                    <th style="text-align: center; width: 5%">Cantidad</th>
                    <th style="text-align: center; width: 5%">Turno</th>
                    <th style="text-align: center; width: 5%">Tiempo</th>
                    <th style="text-align: center; width: 9%">Hr Inicio</th>
                    <th style="text-align: center; width: 9%">Hr Fin</th>
                    <th style="text-align: center; width: 15%">Notas</th>

                </tr>
                <tr>
                </tr>
            </thead>
            <tbody>
                @foreach ($reporte_detalle as $detalle)
                    <tr style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif">
                        <th style="text-align: center">{{ $detalle->numOrden }}</th>
                        <th>{{ strtoupper($detalle->NombreTrabajo) }}</th>
                        @if ($detalle->observacion == null)
                            <th>
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            </th>
                        @else
                            <th>{{ strtoupper($detalle->observacion) }}</th>
                        @endif
                        @if ($reporte == 'M')
                            <th>{{ strtoupper($detalle->NumEmpleado) }}</th>
                        @elseif ($reporte == 'O' || $reporte == 'G')
                            <th>{{ strtoupper($detalle->Maquina) }}</th>
                        @endif
                        <th>{{ strtoupper($detalle->proceso) }}</th>
                        <th style="text-align: center">{{ number_format($detalle->Cantidad, 0) }}</th>
                        <th style="text-align: center">{{ $detalle->Turno }}</th>
                        <th style="text-align: center">{{ number_format($detalle->Tiempo, 2) }}</th>
                        <th style="text-align: center">
                            {{ \Carbon\Carbon::parse($detalle->HoraInicio)->format('d/m/Y H:i') }}</th>
                        <th style="text-align: center">
                            {{ \Carbon\Carbon::parse($detalle->HoraFin)->format('d/m/Y H:i') }}</th>
                        <th>{{ strtoupper($detalle->Notas) }}</th>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="bg-blue-table">
                    <th colspan="12"></th>
                </tr>
            </tfoot>
        </table>
        <table width="100%" style="border: solid 1px #EBEBEB; border-radius: 20px; background-color: #EBEBEB; padding: 10px; font-size: 8px; margin-top: 10px;">
            <tr>
                <!-- Tiempo de ajuste promedio -->
                <td width="25%" valign="top" style="text-align: center;">
                    <div style="font-weight: bold; font-size: 11px; text-transform: uppercase;">Tiempo de ajuste promedio</div>
                    <div style="font-size: 16px; font-weight: bold; margin: 10px 0;">
                        {{ number_format($reporte_eficiencia[0]['TieAjusPro'], 2) }} h
                    </div>
                    <div style="font-size: 8px; text-transform: uppercase;">
                        <span>No. Ajustes: <b>{{ number_format($reporte_eficiencia[0]['AjustesNormales'] + $reporte_eficiencia[0]['AjustesLiteratura'], 2) }}</b></span><br>
                        <span>Tiempo: <b>{{ number_format($reporte_eficiencia[0]['TiempoDeAjuste'], 2) }}</b> h</span>
                    </div>
                    <div style="margin-top: 8px; font-size: 8px; text-transform: uppercase;">
                        Se debió haber realizado en <b>{{ number_format($reporte_eficiencia[0]['SeDebioHacerEnTiem'], 2) }}</b> h
                    </div>
                </td>

                <!-- Velocidad Promedio -->
                <td width="25%" valign="top" style="text-align: center;">
                    <div style="font-weight: bold; font-size: 11px; text-transform: uppercase;">Velocidad Promedio</div>
                    <div style="font-size: 16px; font-weight: bold; margin: 10px 0;">
                        {{ number_format($reporte_eficiencia[0]['VelPromedio'], 2) }} t/h
                    </div>
                    <div style="font-size: 8px; text-transform: uppercase;">
                        <span>Tiros: <b>{{ number_format($reporte_eficiencia[0]['CantTiros'], 0) }}</b></span><br>
                        <span>Tiempo: <b>{{ number_format($reporte_eficiencia[0]['TiempoDeTiro'], 2) }}</b> h</span>
                    </div>
                    <div style="margin-top: 8px; font-size: 8px; text-transform: uppercase;">
                        Se debió haber realizado en <b>{{ number_format($reporte_eficiencia[0]['SeDebioHacerEnVel'], 2) }}</b> h
                    </div>
                </td>

                <!-- Resumen de ajustes y tiros -->
                <td width="25%" valign="top" style="text-align: center;">
                    <div style="font-size: 8px;">
                        SE HICIERON <b>{{ number_format($reporte_eficiencia[0]['AjustesNormales'], 2) }}</b> AJUSTES NORMALES
                        @if ($reporte_eficiencia[0]['Tipo'] == 1)
                            , <b>{{ number_format($reporte_eficiencia[0]['AjustesLiteratura'], 0) }}</b> DE LITERATURA Y
                        @endif
                        <b>{{ number_format($reporte_eficiencia[0]['CantTiros'], 0) }}</b> TIROS EN <b>{{ number_format($reporte_eficiencia[0]['TiempoReportado'], 2) }}</b> HRS,
                        SE DEBIO DE HABER HECHO EN <b>{{ number_format(round($reporte_eficiencia[0]['SeDebioHacerEnTiem'], 2) + round($reporte_eficiencia[0]['SeDebioHacerEnVel'], 2), 2) }}</b> HRS.
                    </div>
                    @if ($reporte_eficiencia[0]['Tipo'] == 1)
                        <div style="margin-top: 8px; font-size: 8px;">
                            STD AJUSTE NORMAL: <b>{{ number_format($reporte_eficiencia[0]['AjusteStd'], 2) }}</b><br>
                            STD AJUSTE LITERATURA: <b>{{ number_format($reporte_eficiencia[0]['AjusteVWStd'], 2) }}</b><br>
                            STD VELOCIDAD DE TIRO: <b>{{ number_format($reporte_eficiencia[0]['VelocidadStd'], 0) }}</b>
                        </div>
                    @endif
                    <div style="margin-top: 8px; font-size: 8px;">
                        TIEMPO MUERTO: <b>{{ number_format($reporte_eficiencia[0]['TotalTiempoMuerto'], 2) }}</b>
                    </div>
                </td>

                <!-- Eficiencia Global con gráfico circular -->
                <td width="25%" valign="top" style="text-align: center;">
                    <div style="font-weight: bold; font-size: 11px; text-transform: uppercase;">Eficiencia Global</div>
                    @php
                        $porcentaje = $reporte_eficiencia[0]['GLOBAL'] ?? 0;
                        if ($porcentaje == 0) {
                            $color = "#000000";
                        } else if ($porcentaje <= 50) {
                            $color = "#F8696B";
                        } else if ($porcentaje > 50 && $porcentaje < 70) {
                            $color = "#FDD17F";
                        } else if ($porcentaje >= 70) {
                            $color = "#63BE7B";
                        }
                    @endphp
                    <div style="display: inline-block; width: 90px; height: 90px; border-radius: 50%; color: {{ $color }}; line-height: 90px; margin: 12px auto 8px auto;">
                        <span style="font-size: 18px; font-weight: bold; vertical-align: middle; line-height: 90px; display: inline-block;">
                            {{ number_format($porcentaje, 2) }}%
                        </span>
                    </div>
                </td>
            </tr>
        </table>
        <table width="100%"
        style="border: solid 1px #EBEBEB; border-radius: 20px; background-color: #EBEBEB; padding: 5px; font-size: 10px !important; margin-top: 10px">
        <tbody>
                <tr>
                    <td colspan="3" style="text-align: center">
                        <br>
                        <br>
                        <br>
                        <br>
                        <br>

                        ___________________________
                    </td>
                    <td colspan="3" style="text-align: center">
                        <br>
                        <br>
                        <br>
                        <br>
                        <br>

                        ___________________________
                    </td>
                </tr>
                <tr>
                    <td colspan="3" style="text-align: center">
                        Firma Supervisor
                    </td>
                    <td colspan="3" style="text-align: center">
                        Firma Operador
                    </td>
                </tr>
            </tbody>
        </table>
    </main>
    @yield('scripts')
    <script type="text/php">
        if ( isset($reporte_detalle) ) {
            $reporte_detalle->page_script('
                $font = $fontMetrics->get_font("'Segoe UI', Tahoma, Geneva, Verdana, sans-serif", "normal");
                $reporte_detalle->text(270, 730, "Pagina $PAGE_NUM de $PAGE_COUNT", $font, 10);
            ');
        }
    </script>
</body>

</html>
