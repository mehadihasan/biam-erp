@props([
    'position' => null,
])

@php
    $user = filament()->auth()->user();
    $name = $user ? filament()->getUserName($user) : __('User');
@endphp

{{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::USER_MENU_BEFORE) }}

@once
    <x-shared-user-menu-styles />
@endonce

<x-shared-user-menu
    :name="$name"
    :logout-url="filament()->getLogoutUrl()"
/>

{{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::USER_MENU_AFTER) }}
