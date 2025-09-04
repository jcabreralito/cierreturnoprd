<div class="mt-4 grid grid-cols-12 gap-4 border rounded-md p-4 bg-white shadow-md text-slate-600">
    <div class="col-span-6 md:col-span-3 space-y-3">
        <h3 class="font-bold text-lg">Tiempo de ajuste promedio</h3>

        <h4><span class="font-semibold">{{ number_format($reporteActual[0]['TieAjusPro'], 2) }} t/h</span></h4>

        <div class="flex space-x-3">
            <p>No. Ajustes <span class="font-semibold ml-2">{{ number_format($reporteActual[0]['AjustesNormales'], 2) }}</span></p>

            <p>Tiempo <span class="font-semibold ml-2">{{ number_format($reporteActual[0]['TiempoDeAjuste'], 2) }}</span> h</p>
        </div>

        <p>Se debió haber realizado en <span class="font-semibold">{{ number_format($reporteActual[0]['SeDebioHacerEnTiem'], 2) }}</span> h</p>
    </div>

    <div class="col-span-6 md:col-span-3 space-y-3">
        <h3 class="font-bold text-lg">Velocidad Promedio</h3>

        <h4><span class="font-semibold">{{ number_format($reporteActual[0]['VelPromedio'], 2) }} t/h</span></h4>

        <div class="flex space-x-3">
            <p>Tiros <span class="font-semibold ml-2">{{ number_format($reporteActual[0]['CantTiros'], 2) }}</span></p>

            <p>Tiempo <span class="font-semibold ml-2">{{ number_format($reporteActual[0]['TiempoDeTiro'], 2) }}</span> h</p>
        </div>

        <p>Se debió haber realizado en <span class="font-semibold">{{ number_format($reporteActual[0]['SeDebioHacerEnVel'], 2) }}</span> h</p>
    </div>

    <div class="col-span-6 md:col-span-3 space-y-3">
        <p>SE HICIERON <span class="font-semibold">{{ number_format($reporteActual[0]['AjustesNormales'], 0) }}</span> AJUSTES NORMALES, <span class="font-semibold">{{ number_format($reporteActual[0]['AjustesLiteratura'], 0) }}</span> DE LITERATURA Y <span class="font-semibold">{{ number_format($reporteActual[0]['CantTiros'], 0) }}</span> TIROS EN <span class="font-semibold">{{ number_format($reporteActual[0]['EnTiempoTiros'], 2) }}</span> HRS, SE DEBIO DE HABER HECHO EN <span class="font-semibold">{{ number_format($reporteActual[0]['SeDebioHacer'], 2) }}</span> HRS.</p>

        <p>TIEMPO REPORTADO: <span class="font-semibold">{{ number_format($reporteActual[0]['TiempoReportado'], 2) }}</span></p>
    </div>

    <div class="col-span-6 md:col-span-3 space-y-3">
        <h3 class="font-bold text-lg text-center">Eficiencia Global</h3>

        <div>
            @php
                $porcentaje = $reporteActual[0]['GLOBAL'] ?? 0;
                $radio = 50;
                $circunferencia = 2 * pi() * $radio;
                $offset = $circunferencia - ($porcentaje / 100 * $circunferencia);
            @endphp
            <div class="flex flex-col items-center justify-center">
                <svg width="120" height="120" class="mb-2">
                    <circle
                        cx="60"
                        cy="60"
                        r="{{ $radio }}"
                        stroke="#e5e7eb"
                        stroke-width="12"
                        fill="none"
                    />
                    <circle
                        cx="60"
                        cy="60"
                        r="{{ $radio }}"
                        stroke="{{ $color }}"
                        stroke-width="12"
                        fill="none"
                        stroke-dasharray="{{ $circunferencia }}"
                        stroke-dashoffset="{{ $offset }}"
                        stroke-linecap="round"
                        transform="rotate(-90 60 60)"
                    />
                    <text
                        x="60"
                        y="70"
                        text-anchor="middle"
                        font-size="1.5rem"
                        fill="#374151"
                        font-weight="bold"
                    >{{ number_format($porcentaje, 2) }}%</text>
                </svg>
            </div>
        </div>
    </div>
</div>
