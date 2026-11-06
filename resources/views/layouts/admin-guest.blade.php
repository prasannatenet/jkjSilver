<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $appSetting->site_name ?? config('app.name', 'Admin') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css"
        rel="stylesheet">

    <!-- Admin Styles -->
    <link href="{{ asset('backend/css/style.css') }}?v={{ time() }}" rel="stylesheet">

    @stack('styles')
</head>

<body>
    <div class="min-vh-100 d-flex align-items-center justify-content-center bg-light py-5">
        <div class="col-11 col-sm-9 col-md-7 col-lg-5 col-xl-4">
            <div class="text-center mb-4">
                <h2 class="fw-bold mb-1">{{ $appSetting->site_name ?? config('app.name', 'Admin') }}</h2>
                <p class="text-muted mb-0">Admin Panel</p>
            </div>

            <div class="bg-white shadow-sm rounded-3 p-4 p-md-5">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
</body>

</html>