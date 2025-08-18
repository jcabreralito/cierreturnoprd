<div class="w-[207px] blue-1 px-4 left-0 py-8 md:block hidden fixed h-full mt-[60px] md:mt-16 z-50" x-show="openLg">
    @php
        $routeMain = (config('app.env') === 'production' ? '/cierreturno' : '');

        $permisosUsuario = (new App\Http\Controllers\PermisoController())->getPermisos();
    @endphp

    <div class="flex-col justify-between items-center flex min-h-[94%]">
        <div class="space-y-4 w-full">
            @if (in_array(7, $permisosUsuario))
            <div class="w-full">
                <a href="{{ $routeMain }}/dashboard" class="{{ request()->is('dashboard') ? 'btn-sidebar-active' : 'btn-sidebar' }} block">Solicitudes</a>
            </div>
            @endif

            @if (in_array(11, $permisosUsuario))
            <div class="w-full">
                <a href="{{ $routeMain }}/relacion" class="{{ request()->is('relacion') ? 'btn-sidebar-active' : 'btn-sidebar' }} block">Relación horas</a>
            </div>
            @endif

            @if (in_array(12, $permisosUsuario))
            <div class="w-full">
                <a href="{{ $routeMain }}/relacion-jornadas" class="{{ request()->is('relacion-jornadas') ? 'btn-sidebar-active' : 'btn-sidebar' }} block">Relación jornadas</a>
            </div>
            @endif

            <div class="w-full">
                <a href="{{ $routeMain }}/indicadores" class="{{ request()->is('indicators') ? 'btn-sidebar-active' : 'btn-sidebar' }} block">Indicadores</a>
            </div>
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
