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
    <x-shared-user-menu-styles />
</head>
<body class="bcs-body">
    @php
        $isGuestPortal = session('guest_verified') === true && session('cadre_auth') !== true;
        $portalTitle = $isGuestPortal ? __('Guest') : __('BCS Cadre');
        $portalUserName = $isGuestPortal
            ? session('guest_name', __('Guest'))
            : (session('cadre_name') ?: (session('cadre_reference') ? __('Cadre :reference', ['reference' => session('cadre_reference')]) : __('BCS Cadre')));
        $portalLogoutUrl = $isGuestPortal ? route('guest.logout') : route('cadre.logout');
    @endphp
    <header class="bcs-topbar">
        <div class="bcs-topbar__brand">{{ $portalTitle }}</div>
        <div class="bcs-topbar__actions">
            <x-shared-user-menu
                :name="$portalUserName"
                :logout-url="$portalLogoutUrl"
            />
        </div>
    </header>

    <div class="bcs-shell">
        @include('bcs-cadre.partials.sidebar', ['activeMenu' => $activeMenu ?? 'booking'])
        <main class="bcs-shell__main">
            @yield('content')
        </main>
    </div>
</body>
</html>
