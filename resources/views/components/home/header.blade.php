<header class="fixed w-full z-50" @click.away="open = false">
    @php
        $routeMain = (config('app.env') === 'production' ? '/cierreturno' : '');
    @endphp

    <div class="blue-1 w-full mx-auto px-8 py-4 h-[64px] flex justify-between items-center border-b-2">
        {{-- Logotipo --}}
        <div class="flex items-center">
            <div class="flex space-x-4 items-center">
                <img src="{{ asset('assets/img/logo-lito.png') }}" alt="logo-lito" class="hidden md:block">

                <button x-on:click="openLg=!openLg" class="hidden md:block text-white text-xl">
                    <i class="fa-solid fa-bars"></i>
                </button>
            </div>

            {{-- Menu de hamburguesa --}}
            <div class="text-white text-xl md:hidden">
                <button x-on:click="open=!open"><i class="fa-solid fa-bars"></i></button>
            </div>
        </div>

        {{-- Nombre y panel de manejo --}}
        <div class="text-white">
            @if (auth()->user()->Personal != null)
                <p>{{ auth()->user()->Personal . ' - ' . auth()->user()->Nombre }}</p>
            @else
                <p>{{ auth()->user()->Nombre }}</p>
            @endif
        </div>
    </div>

    {{-- Menú lateral --}}
    <div class="fixed md:hidden inset-0 flex" x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform -translate-x-full" x-transition:enter-end="opacity-100 transform translate-x-0"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform translate-x-0"
        x-transition:leave-end="opacity-0 transform -translate-x-full">
        <div class="relative flex-1 flex flex-col max-w-xs w-full blue-1">
            <div class="absolute top-0 right-0 -mr-12 pt-2">
                <button x-on:click="open = false" class="ml-1 blue-1 border-2 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:bg-gray-600">
                    <span class="sr-only">Close sidebar</span>
                    <i class="fa-solid fa-times text-white"></i>
                </button>
            </div>
            <div class="flex-1 pt-5 pb-4 overflow-y-auto w-full h-full">
                <nav class="mt-5 px-2 space-y-1 flex flex-col justify-between min-h-[95%]">
                    <div class="space-y-2">
                        {{--  Inicio  --}}
                        @if(auth()->user()->tipoUsuarioCierreTurno != 4)
                            <a href="{{ $routeMain }}/dashboard" class="item-sub-menu block text-white px-2 py-2 rounded-md text-base font-medium {{ request()->is('dashboard') ? 'bg-white text-blue-1' : '' }}">Cierre turno</a>
                        @endif

                        @if (auth()->user()->tipoUsuarioCierreTurno == 1)
                        <a href="{{ $routeMain }}/re-calculo" class="item-sub-menu block text-white px-2 py-2 rounded-md text-base font-medium {{ request()->is('re-cierres') ? 'bg-white text-blue-1' : '' }}">Re-cálculo</a>
                        <a href="{{ $routeMain }}/historico" class="item-sub-menu block text-white px-2 py-2 rounded-md text-base font-medium {{ request()->is('historico') ? 'bg-white text-blue-1' : '' }}">Historico</a>
                        <a href="{{ $routeMain }}/ranking" class="item-sub-menu block text-white px-2 py-2 rounded-md text-base font-medium {{ request()->is('ranking') ? 'bg-white text-blue-1' : '' }}">Ranking</a>
                        @endif
                    </div>

                    <div class="bottom-0 top-0">
                        <a href="{{ $routeMain }}/logout" class="item-sub-menu block text-white px-2 py-2 rounded-md text-base font-medium">
                            <i class="fa-solid fa-right-from-bracket mr-2"></i>
                            <span>Regresar a Lito Apps</span>
                        </a>
                    </div>
                </nav>
            </div>
        </div>
    </div>
</header>
