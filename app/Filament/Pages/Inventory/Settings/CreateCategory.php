<?php

namespace App\Filament\Pages\Inventory\Settings;

use App\Filament\Pages\Inventory\BaseInventoryPage;

class CreateCategory extends BaseInventoryPage
{
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $title = 'Create Category';

    protected static ?string $slug = 'inventory/categories/create';

    protected string $view = 'filament.pages.inventory.settings.category-create';

    public static function getNavigationUrl(): string
    {
        return Categories::getUrl(panel: 'admin');
    }
}
