<div class="mt-4 grid grid-cols-12 gap-4 border rounded-md p-4 bg-white shadow-md text-slate-600 text-sm">
    <div class="col-span-6 md:col-span-3 space-y-3 text-center">
        <h3 class="font-bold text-lg text-center">Tiempo de ajuste promedio</h3>

        <h4><span class="font-semibold text-center text-2xl">{{ number_format($reporteActual->tiempo_ajuste_promedio, 2) }} h</span></h4>

        <div class="flex space-x-3 justify-center w-full text-center">
            <p>No. Ajustes <span class="font-semibold ml-2">{{ number_format($reporteActual->num_ajustes, 2) }}</span></p>

            <p>Tiempo <span class="font-semibold ml-2">{{ number_format($reporteActual->tiempo_ajustes, 2) }}</span> h</p>
        </div>

        <p>Se debió haber realizado en <span class="font-semibold">{{ number_format($reporteActual->se_debio_realizar_en_ajustes, 2) }}</span> h</p>
    </div>

    <div class="col-span-6 md:col-span-3 space-y-3 text-center">
        <h3 class="font-bold text-lg">Velocidad Promedio</h3>

        <h4><span class="font-semibold text-center text-2xl">{{ number_format($reporteActual->velocidad_promedio, 2) }} t/h</span></h4>

        <div class="flex space-x-3 justify-center w-full text-center">
            <p>Tiros <span class="font-semibold ml-2">{{ number_format($reporteActual->num_tiros, 0) }}</span></p>

            <p>Tiempo <span class="font-semibold ml-2">{{ number_format($reporteActual->tiempo_tiros, 2) }}</span> h</p>
        </div>

        <p>Se debió haber realizado en <span class="font-semibold">{{ number_format($reporteActual->se_debio_realizar_en_tiros, 2) }}</span> h</p>
    </div>

    <div class="col-span-6 md:col-span-3 space-y-3 w-full text-center">
        <p>SE HICIERON <span class="font-semibold">{{ number_format($reporteActual->num_ajustes, 0) }}</span> AJUSTES NORMALES
            @if ($reporteActual->tipo_reporte == 1)
            , <span class="font-semibold">{{ number_format($reporteActual->num_ajustes_literatura, 0) }}</span> DE LITERATURA
            @endif

            Y <span class="font-semibold">{{ number_format($reporteActual->num_tiros, 0) }}</span> TIROS EN <span class="font-semibold">{{ number_format($reporteActual->en, 2) }}</span> HRS, SE DEBIO DE HABER HECHO EN <span class="font-semibold">{{ number_format($reporteActual->debio_hacerce_en, 2) }}</span> HRS.</p>

        {{--  <p>TIEMPO TOTAL REPORTADO: <span class="font-semibold">{{ number_format($reporteActual->tiempo_reportado, 2) }}</span></p>  --}}

        @if ($reporteActual->tipo_reporte == 1)
        <div class="mt-2">
            <p>STD AJUSTE NORMAL: <span class="font-semibold">{{ number_format($reporteActual->std_ajuste_normal, 2) }}</span></p>
            <p>STD AJUSTE LITERATURA: <span class="font-semibold">{{ number_format($reporteActual->std_ajuste_literatura, 2) }}</span></p>
            <p>STD VELOCIDAD DE TIRO: <span class="font-semibold">{{ number_format($reporteActual->std_velocidad_tiro, 0) }}</span></p>
        </div>
        @endif
    </div>

    <div class="col-span-6 md:col-span-3 space-y-3">
        <h3 class="font-bold text-lg text-center">Eficiencia Global</h3>

        <div>
            @php
                $porcentaje = $reporteActual->eficiencia_global ?? 0;
                $radio = 50;
                $circunferencia = 2 * pi() * $radio;
                $offset = ($porcentaje > 100 ? $circunferencia - (100 / 100 * $circunferencia) : $circunferencia - ($porcentaje / 100 * $circunferencia));
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
