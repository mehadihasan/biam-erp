<?php

namespace App\Filament\Pages\Inventory\Settings;

use App\Filament\Pages\Inventory\BaseInventoryPage;
use App\Models\InventoryCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class Categories extends BaseInventoryPage
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-tag';

    protected static string | \UnitEnum | null $navigationGroup = 'Settings';

    protected static ?string $title = 'Categories';

    protected static ?string $navigationLabel = 'Category';

    protected static ?string $slug = 'inventory/categories';

    protected static ?int $navigationSort = 91;

    protected string $view = 'filament.pages.inventory.settings.categories';

    public static function getNavigationUrl(): string
    {
        return static::getUrl(panel: 'admin');
    }

    public function getCategories(): LengthAwarePaginator
    {
        return InventoryCategory::query()
            ->latest()
            ->paginate(10);
    }
}
