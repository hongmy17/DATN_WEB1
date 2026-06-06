<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'AdminHMD - Quản Trị') </title>

    <!-- Bootstrap CSS -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet"> {{-- Nếu có file style riêng của template --}}

    @yield('css')
</head>
<body class="bg-light">

<div class="wrapper d-flex">

    <!-- SIDEBAR -->
    @include('admin.layouts.sidebar')

    <!-- MAIN CONTENT -->
    <div class="flex-grow-1">
        
        <!-- HEADER / NAVBAR -->
        @include('admin.layouts.header')

        <!-- PAGE CONTENT -->
        <div class="content p-4">
            @yield('content')
        </div>

        <!-- FOOTER -->
        @include('admin.layouts.footer')
        
    </div>
</div>

<!-- Scripts -->
<script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
@yield('js')

</body>
</html>