<?php

namespace App\Providers\Filament;

use App\Filament\Pages\HostelDashboard;
use App\Filament\Pages\InventoryDashboard;
use App\Filament\Pages\ModuleSelector;
use Filament\Navigation\NavigationGroup;
use Filament\Support\Assets\Js;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->homeUrl(fn (): string => ModuleSelector::getUrl(panel: 'admin'))
            ->login()
            ->brandName('BHMS')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->assets([
                Js::make('sidebar-collapse')
                    ->defer()
                    ->navigateOnce(false),
            ])
            ->navigationGroups([
                // Group headers are toggles (not links). Items are the links.
                NavigationGroup::make('User Management')->collapsible()->collapsed(),
                NavigationGroup::make('Room Management')->collapsible()->collapsed(),
                NavigationGroup::make('Booking & Reservation')->collapsible()->collapsed(),
            ])
            ->colors([
                'primary' => Color::Amber,
            ])
            ->maxContentWidth(Width::Full)
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                ModuleSelector::class,
                HostelDashboard::class,
                InventoryDashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
