<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ProtRH') }}</title>

    <!-- Custom Scripts -->
    @yield('script-header')

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">

    <!-- Custom css, necessary for typehead -->
    @yield('css-header')
</head>
<body>

    <main class="py-2">
        @yield('content')
    </main>

    <footer class="container-fluid text-center py-4">
        <p><strong>Setor de Protocolos SMS</strong> - ramal: 9999 </p>
    </footer>

    <!-- Scripts -->
    <script src="{{ asset('js/jquery-3.3.1.min.js') }}"></script>
    <script src="{{ asset('js/popper.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    @yield('script-footer')
</body>
</html>
