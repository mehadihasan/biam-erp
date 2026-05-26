@php
    $livewire ??= null;
    $renderHookScopes = $livewire?->getRenderHookScopes();
@endphp

<x-filament-panels::layout.base :livewire="$livewire">
    <div class="bhms-module-selector-layout">
        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::SIMPLE_LAYOUT_START, scopes: $renderHookScopes) }}

        <header class="bhms-module-selector-topbar">
            <div class="bhms-module-selector-topbar__brand">
                <img
                    src="{{ asset('images/biam-logo.png') }}"
                    alt="BIAM"
                    class="bhms-module-selector-topbar__logo"
                >
                <span class="bhms-module-selector-topbar__title">BIAM</span>
            </div>

            <div class="bhms-module-selector-topbar__actions">
                @if (filament()->hasUserMenu())
                    @livewire(Filament\Livewire\SimpleUserMenu::class)
                @endif
            </div>
        </header>

        <main class="bhms-module-selector-main">
            {{ $slot }}
        </main>

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::SIMPLE_LAYOUT_END, scopes: $renderHookScopes) }}
    </div>
</x-filament-panels::layout.base>
