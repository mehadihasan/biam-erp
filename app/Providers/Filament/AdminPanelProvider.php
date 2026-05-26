<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Hostel\Rooms\RoomDetail;
use App\Filament\Pages\HostelDashboard;
use App\Filament\Pages\InventoryDashboard;
use App\Filament\Pages\ModuleSelector;
use App\Http\Middleware\AuthenticateFilamentOrCadre;
use App\Http\Middleware\SyncAdminModule;
use App\Support\AdminModule;
use Illuminate\Support\HtmlString;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Assets\Js;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\View\PanelsRenderHook;
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
            ->homeUrl(fn (): string => match (AdminModule::current()) {
                AdminModule::HOSTEL => HostelDashboard::getUrl(panel: 'admin'),
                AdminModule::INVENTORY => InventoryDashboard::getUrl(panel: 'admin'),
                default => ModuleSelector::getUrl(panel: 'admin'),
            })
            ->login()
            ->brandName(fn (): string => AdminModule::brandName())
            ->brandLogo(fn (): HtmlString => new HtmlString(
                view('filament.components.admin-brand', [
                    'name' => AdminModule::brandName(),
                ])->render(),
            ))
            ->brandLogoHeight('2rem')
            ->spa()
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->assets([
                Js::make('sidebar-collapse', resource_path('js/filament/admin/sidebar-collapse.js'))
                    ->package('app')
                    ->defer()
                    ->navigateOnce(false),
            ])
            ->renderHook(
                PanelsRenderHook::SIDEBAR_START,
                fn (): string => view('filament.hooks.admin-sidebar-toggle')->render(),
            )
            ->navigationGroups([
                // Hostel module groups
                NavigationGroup::make('User Management')->collapsible()->collapsed(),
                NavigationGroup::make('Room Management')->collapsible()->collapsed(),
                NavigationGroup::make('Booking & Reservation')->collapsible()->collapsed(),
                NavigationGroup::make('Approval Workflow')->collapsible()->collapsed(),
                NavigationGroup::make('Meal Order')->collapsible()->collapsed(),
                NavigationGroup::make('Payment & Billing')->collapsible()->collapsed(),
                NavigationGroup::make('Feedback')->collapsible()->collapsed(),
                NavigationGroup::make('Settings')->collapsible()->collapsed(),
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
                RoomDetail::class,
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
                SyncAdminModule::class,
            ])
            ->authMiddleware([
                AuthenticateFilamentOrCadre::class,
            ]);
    }
}
