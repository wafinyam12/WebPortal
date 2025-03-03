<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }} | {{ $title ?? '' }}</title>

    <!-- Fonts -->
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">

    <!-- Favicon -->
    <link href="{{ asset('img/favicon.png') }}" rel="icon" type="image/png">

    @stack('css')
</head>
<body class="bg-gradient-primary min-vh-100 d-flex justify-content-center align-items-center">

@yield('main-content')

<!-- Scripts -->
<script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('vendor/bootstrap/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/sb-admin-2.min.js') }}"></script>
@stack('scripts')
<script>
    @if (session('success'))
         Swal.fire({
             icon: 'success',
             title: 'Success',
             text: '{{ session('success') }}',
             showConfirmButton: false,
             timer: 1500
         });
     @endif
 
     @if (session('error'))
         Swal.fire({
             icon: 'error',
             title: 'Error',
             text: '{{ session('error') }}',
             showConfirmButton: true,
         });
     @endif
 
     @if ($errors->any())
         Swal.fire({
             icon: 'error',
             title: 'Error',
             text: '{{ $errors->first() }}',
             showConfirmButton: true,
         });
     @endif
</script>
</body>
</html>
