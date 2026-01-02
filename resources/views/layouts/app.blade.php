<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title')@if(Auth::check() &&
        (Auth::user()->role == 'student' ||
            Auth::user()->role == 'teacher' ||
            Auth::user()->role == 'admin' ||
            Auth::user()->role == 'accountant' ||
            false))
            - {{ Auth::user()->school->name }}
        @endif
    </title>

    <link rel="stylesheet" href="{{ url('css/loader.css') }}">
    <link rel="shortcut icon" href="{{ asset('images/logo.ico') }}">
    <script src="{{ url('js/vendors.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.1/bootstrap3-typeahead.min.js"></script>
    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
    <script src="{{ url('js/application.js') }}"></script>
    @yield('after_scripts')
</head>

<body tyle="min-height: 100vh; display: flex; flex-direction: column;">
    @include('components.loader')
    <div id="app">
        @include('components.navbar-top')
        @yield('content')
    </div>

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons&style=normal&weight=400" rel="stylesheet">
    <link rel="stylesheet" href="{{ url('css/vendors.css') }}" id="bootswatch-print-id">
    <link rel="stylesheet" href="{{ url('css/application.css') }}">
    <script src="{{ asset('js/typeahead.js') }}"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap.min.css">
    <!-- Buttons DataTables -->
    <link rel="stylesheet" type="text/css"
        href="https://cdn.datatables.net/v/bs/jszip-2.5.0/dt-1.10.20/b-1.6.1/b-html5-1.6.1/datatables.min.css" />
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script type="text/javascript"
        src="https://cdn.datatables.net/v/bs/jszip-2.5.0/dt-1.10.20/b-1.6.1/b-html5-1.6.1/datatables.min.js"></script>
    @yield('jsFiles')
    <footer class="text-center" style="padding: 10px 0; color: #888888; font-size: 0.95em;">
        TCTIMS &mdash; 2.4.{{ date('Y') }}
    </footer>
</body>

</html>
