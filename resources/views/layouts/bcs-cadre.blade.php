<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('BCS Cadre')) - {{ config('app.name', 'BIAM') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet">
    @vite(['resources/css/bcs-cadre.css', 'resources/js/bcs-cadre.js'])
</head>
<body class="bcs-body">
    <header class="bcs-topbar">
        <div class="bcs-topbar__brand">{{ __('BCS Cadre') }}</div>
        <div class="bcs-topbar__avatar" aria-label="{{ __('Profile') }}">M</div>
    </header>

    <div class="bcs-shell">
        @include('bcs-cadre.partials.sidebar', ['activeMenu' => $activeMenu ?? 'booking'])
        <main class="bcs-shell__main">
            @yield('content')
        </main>
    </div>
</body>
</html>
