<div class="w-[207px] blue-1 px-4 left-0 py-8 md:block hidden fixed h-full mt-[60px] md:mt-16 z-50" x-show="openLg">
    @php
        $routeMain = (config('app.env') === 'production' ? '/cierreturno' : '');
    @endphp

    <div class="flex-col justify-between items-center flex min-h-[94%]">
        <div class="space-y-4 w-full">
            @if(auth()->user()->tipoUsuarioCierreTurno != 4)
            <div class="w-full">
                <a href="{{ $routeMain }}/dashboard" class="{{ request()->is('dashboard') ? 'btn-sidebar-active' : 'btn-sidebar' }} block">Cierre turno</a>
            </div>
            @endif

            @if (auth()->user()->tipoUsuarioCierreTurno == 1)
            <div class="w-full">
                <a href="{{ $routeMain }}/lista-cierres" class="{{ request()->is('lista-cierres') ? 'btn-sidebar-active' : 'btn-sidebar' }} block">Lista de Cierres</a>
            </div>
            <div class="w-full">
                <a href="{{ $routeMain }}/historico" class="{{ request()->is('historico') ? 'btn-sidebar-active' : 'btn-sidebar' }} block">Historico</a>
            </div>
            <div class="w-full">
                <a href="{{ $routeMain }}/ranking" class="{{ request()->is('ranking') ? 'btn-sidebar-active' : 'btn-sidebar' }} block">Ranking</a>
            </div>
            @endif
        </div>

        <div class="w-full end-0 mb-5">
            <div>
                <a href="{{ $routeMain }}/logout" class="btn-logout"><i class="fa-solid fa-right-from-bracket mr-2"></i>
                    Salir a Lito Apps
                </a>
            </div>
        </div>
    </div>
</div>
