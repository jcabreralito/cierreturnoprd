<!DOCTYPE html>
<html>

<head>
    <link rel="shortcut icon" href="/public/img/analisis.png" type="image/x-icon">
    <title>Reporte de Producción</title>
    {{-- <meta charset="UTF-8"> --}}

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="vendor/Bootstrap4/css/bootstrap.min.css">

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
    {{-- <footer>
        <strong class="pagenum"></strong>
    </footer> --}}
    <main>
        <table id="tblReporteImp" class="table-striped table-bordered"
            style="font-size: 8px !important; margin-top: 1.5cm;">
            <thead>
                <tr class="bg-blue-table" style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif">
                    {{-- <th style="text-align: center; width: 5%">Fin Turno</th> --}}
                    <th style="text-align: center; width: 5%">Orden</th>
                    {{-- <th style="text-align: center; width: 5%">Id Act</th> --}}
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
                        {{-- <th style="text-align: center">
                            {{ \Carbon\Carbon::parse($detalle->fechaproduccion)->format('d/m/Y') }}</th> --}}
                        <th style="text-align: center">{{ $detalle->numOrden }}</th>
                        {{-- <th>{{ Str::title($detalle->NombreTrabajo) }}</th> --}}
                        <th>{{ strtoupper($detalle->NombreTrabajo) }}</th>
                        {{-- <th style="text-align: right">{{ $detalle->idAct }}</th> --}}
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
        <table width="100%"
            style="border: solid 1px #EBEBEB; border-radius: 20px; background-color: #EBEBEB; padding: 5px; font-size: 10px !important; margin-top: 10px">
            <tbody>
                <tr>
                    <td style="font-weight: bold; font-size: 20px; vertical-align: middle">
                        <div class="row d-flex mt-1 mb-1">
                            <div class="col-12 text-center">
                                @if ($reporte_eficiencia != null)
                                    @if ($reporte_eficiencia[0]->GLOBAL != null)
                                        @if ($reporte_eficiencia[0]->GLOBAL < 60)
                                            <span
                                                style="color: #F8696B; text-align: right; vertical-align: middle">EFICIENCIA
                                                GLOBAL
                                                {{ number_format($reporte_eficiencia[0]->GLOBAL, 2) }}%
                                            </span>
                                            <img style="text-align: left;" src="img/no-me-gusta.png" height="30px">
                                        @elseif ($reporte_eficiencia[0]->GLOBAL >= 60 && $reporte_eficiencia[0]->GLOBAL < 70)
                                            <span style="color: #FDD17F; text-align: right; vertical-align: middle">EFICIENCIA GLOBAL
                                                {{ number_format($reporte_eficiencia[0]->GLOBAL, 2) }}%
                                                <img src="img/me-gusta-a.png" height="30px" style="text-align: left;">
                                            </span>
                                        @elseif ($reporte_eficiencia[0]->GLOBAL >= 70)
                                            <span style="color: #63BE7B; text-align: right; vertical-align: middle">EFICIENCIA GLOBAL
                                                {{ number_format($reporte_eficiencia[0]->GLOBAL, 2) }}%
                                                <img src="img/me-gusta-v.png" height="30px" style="text-align: left;">
                                            </span>
                                        @endif
                                    @else
                                        @if ($reporte_eficiencia[0]->CONVENCIONAL < 75)
                                            <span style="color: #F8696B; text-align: right; vertical-align: middle">EFICIENCIA CONVENCIONAL
                                                {{ number_format($reporte_eficiencia[0]->CONVENCIONAL, 2) }}%
                                                <img src="img/no-me-gusta.png" height="30px" style="text-align: left;">
                                            </span>
                                        @elseif ($reporte_eficiencia[0]->CONVENCIONAL >= 75 && $reporte_eficiencia[0]->CONVENCIONAL < 90)
                                            <span style="color: #FDD17F; text-align: right; vertical-align: middle">EFICIENCIA CONVENCIONAL
                                                {{ number_format($reporte_eficiencia[0]->CONVENCIONAL, 2) }}%
                                                <img src="img/me-gusta-a.png" height="30px" style="text-align: left;">
                                            </span>
                                        @elseif ($reporte_eficiencia[0]->CONVENCIONAL >= 90)
                                            <span style="color: #63BE7B; text-align: right; vertical-align: middle">EFICIENCIA CONVENCIONAL
                                                {{ number_format($reporte_eficiencia[0]->CONVENCIONAL, 2) }}%
                                                <img src="img/me-gusta-v.png" height="30px" style="text-align: left;">
                                            </span>
                                        @endif
                                    @endif
                                @else
                                    NO HAY DATOS PARA LA EFICIENCIA
                                @endif
                            </div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <table width="100%"
            style="border: solid 1px #EBEBEB; border-radius: 20px; background-color: #EBEBEB; padding: 5px; font-size: 10px !important; margin-top: 10px">
            <tbody>
                <tr>
                    <td colspan="6">SE HICIERON:</td>
                </tr>
                <tr>

                    <td style="text-align: right" class="pr-2">
                        AJUSTES NORMALES:
                    </td>
                    <td style="text-align: left">
                        <b>{{ number_format($reporte_eficiencia[0]->AjustesNormales, 2) }}</b>
                    </td>

                    <td style="text-align: right" class="pr-2">
                        TIROS:
                    </td>
                    <td style="text-align: left">
                        <b>{{ number_format($reporte_eficiencia[0]->CantTiros) }}</b>
                    </td>
                    <td style="text-align: right" class="pr-2">
                        EN
                    </td>
                    <td style="text-align: left">
                        <b>{{ number_format($reporte_eficiencia[0]->EnTiempoTiros, 2) }}</b> HRS
                    </td>

                </tr>
                <tr>
                    <td style="text-align: right" class="pr-2">
                        AJUSTES LITERATURA:
                    </td>
                    <td style="text-align: left">
                        <b>{{ $reporte_eficiencia[0]->AjustesLiteratura }}</b>
                    </td>
                    <td></td>
                    <td></td>
                    <td style="text-align: right" class="pr-2">
                        SE DEBIO DE HABER HECHO EN:
                    </td>
                    <td style="text-align: left">
                        <b>{{ number_format($reporte_eficiencia[0]->SeDebioHacer, 2) }}</b> HRS.
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td style="text-align: right" class="pr-2">
                        TIEMPO REPORTADO:
                    </td>
                    <td style="text-align: left">
                        <b>{{ number_format($reporte_eficiencia[0]->TiempoReportado, 2) }}</b>
                    </td>
                    <td style="text-align: right" class="pr-2">
                        TIEMPO MUERTO:
                    </td>
                    <td style="text-align: left">
                        <b>{{ number_format($reporte_eficiencia[0]->TotalTiempoMuerto, 2) }}</b>
                    </td>
                    <td style="text-align: right" class="pr-2">
                        STD AJUSTE NORMAL:
                    </td>
                    <td style="text-align: left">
                        <b>{{ number_format($reporte_eficiencia[0]->AjusteStd, 2) }}</b>
                    </td>
                </tr>
                <tr>
                    <td style="text-align: right" class="pr-2">
                        TIEMPO DE AJUSTE:
                    </td>
                    <td style="text-align: left">
                        <b>{{ number_format($reporte_eficiencia[0]->TiempoDeAjuste, 2) }}</b>
                    </td>
                    <td style="text-align: right" class="pr-2">
                        {{-- TIEMPO MUERTO AJENO: --}}
                    </td>
                    <td style="text-align: left">
                        {{-- <b>{{ number_format($reporte_eficiencia[0]->TiempoMuertoAjeno, 2) }}</b> --}}
                    </td>
                    <td style="text-align: right" class="pr-2">
                        STD AJUSTE LITERATURA:
                    </td>
                    <td style="text-align: left">
                        <b>{{ number_format($reporte_eficiencia[0]->AjusteVWStd, 2) }}</b>
                    </td>
                </tr>
                <tr>
                    <td style="text-align: right" class="pr-2">
                        TIEMPO DE TIRO:
                    </td>
                    <td style="text-align: left">
                        <b>{{ number_format($reporte_eficiencia[0]->TiempoDeTiro, 2) }}</b>
                    </td>
                    <td style="text-align: right" class="pr-2">
                        {{-- TIEMPO MUERTO SIN TURNO: --}}
                    </td>
                    <td style="text-align: left">
                        {{-- <b>{{ number_format($reporte_eficiencia[0]->TiempoMuertoSinTurno, 2) }}</b> --}}
                    </td>
                    <td style="text-align: right" class="pr-2">
                        STD VELOCIDAD DE TIRO:
                    </td>
                    <td style="text-align: left">
                        <b>{{ number_format($reporte_eficiencia[0]->VelocidadStd, 0) }}</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="3" style="text-align: right" class="pr-2">
                        {{-- TIEMPO SIN REGISTRO: --}}
                    </td>
                    <td colspan="3" style="text-align: left">
                    </td>
                </tr>
            </tbody>
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
