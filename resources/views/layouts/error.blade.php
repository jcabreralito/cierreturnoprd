<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <base href="/auditorias-sisol/" />

    <title>Auditorias - Sisol</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet" />
    {{--  <link href="{{ asset('assets/css/fonts.css') }}" rel="stylesheet" />  --}}

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{--  icono  --}}
    <link rel="icon" href="{{ asset('assets/img/sisol.ico') }}" type="image/x-icon" />

    {{--  Font Awesome  --}}
    <script src="https://kit.fontawesome.com/814278b0bf.js" crossorigin="anonymous"></script>
</head>

<body class="antialiased h-full" x-data="{ open: false, openLg: true }">

    <main class="w-full">
        {{ $slot }}
    </main>

    <script src="{{ asset('assets/js/sweetalert2.js') }}"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>
</body>

</html>
