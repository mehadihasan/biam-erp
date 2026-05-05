<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('BCS Cadre')) — {{ config('app.name', 'BIAM') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet">
    @vite(['resources/css/bcs-cadre.css', 'resources/js/bcs-cadre.js'])
</head>
<body class="bcs-body">
    <div class="bcs-shell">
        <header class="bcs-shell__header">
            <nav class="bcs-topnav" aria-label="{{ __('Quick links') }}">
                <a href="{{ route('home', ['view' => 'guest']) }}" class="bcs-topnav__link">{{ __('Guest login') }}</a>
                <a href="{{ route('home', ['view' => 'staff']) }}" class="bcs-topnav__link">{{ __('Admin login') }}</a>
            </nav>
        </header>
        <main class="bcs-shell__main">
            @yield('content')
        </main>
    </div>
</body>
</html>
