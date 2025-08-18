<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="server" content="{{ env('APP_ENV') != "local" ? '/horasextralito' : env("APP_URL") }}">
    <base href="/horasextra/" />

    <title>Horas extra</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=source-sans-pro:200,200i,300,300i,400,400i,600,600i,700,700i,900,900i" rel="stylesheet" />

    {{--  <link href="{{ asset('assets/css/fonts.css') }}" rel="stylesheet" />  --}}

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{--  icono  --}}
    <link rel="icon" href="{{ asset('assets/img/lito.ico') }}" type="image/x-icon" />

    <!-- Styles -->
    @livewireStyles

    {{--  Font Awesome  --}}
    {{--  <script src="https://kit.fontawesome.com/814278b0bf.js" crossorigin="anonymous"></script>  --}}
    <script src="{{ asset('assets/fontawesome/js/all.min.js') }}"></script>
</head>

<body class="antialiased h-full overflow-x-hidden" x-data="{ open: false, openLg: false }" @resize.window="openLg = window.innerWidth >= 1024">
    {{--  Nav bar  --}}
    @include('components.home.header')

    {{--  Menú  --}}
    <main class="flex h-full">

        {{--  Sidebar --}}
        @include('components.home.sidebar')

        <div class="block w-full mt-[60px] md:mt-16 sm:mx-auto md:mr-0" :class="!openLg ? 'md:w-full' : 'md:w-[89%]'">
            @if (auth()->user()->tipoUsuarioHorasExtra == 7)
            <div class="mx-auto grid place-content-center h-[80%] absolute w-full">
                <div>
                    <div class="mt-16 space-y-6">
                        <h1 class="text-4xl font-bold text-center text-sky-500">Requerimiento de tiempo extra</h1>
                    </div>
                </div>
            </div>
            @else
            {{ $slot }}
            @endif

            @include('asistente.template',["assistantId"=>"asst_V9BBeZN8YrYETUS6jwFr1Szn"])
        </div>
    </main>

    @stack('modals')

    @livewireScripts

    <script src="{{ asset('assets/js/sweetalert2.js') }}"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>
    <script src="{{ asset('assets/js/reporte.js') }}"></script>
    <script src="{{ asset('assets/js/xlsx.full.min.js?n=1') }} " defer></script>
    <script src="{{ asset('assets/js/xlsx.bundle.js?n=1') }} " defer></script>
</body>

</html>
