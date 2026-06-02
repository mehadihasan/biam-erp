<?php

namespace App\Filament\Pages\Inventory\Settings;

use App\Filament\Pages\Inventory\BaseInventoryPage;

class CreateSupplier extends BaseInventoryPage
{
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $title = 'Add New Supplier';

    protected static ?string $slug = 'inventory/settings/suppliers/create';

    protected string $view = 'filament.pages.inventory.settings.supplier-create';

    public static function getNavigationUrl(): string
    {
        return Suppliers::getUrl(panel: 'admin');
    }
}
