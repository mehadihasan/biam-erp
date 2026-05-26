<?php

namespace App\Filament\Pages\Inventory\Settings;

use App\Filament\Pages\Inventory\BaseInventoryPage;

class CreateUnit extends BaseInventoryPage
{
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $title = 'Create Unit';

    protected static ?string $slug = 'inventory/units/create';

    protected string $view = 'filament.pages.inventory.settings.unit-create';

    public static function getNavigationUrl(): string
    {
        return Units::getUrl(panel: 'admin');
    }
}
