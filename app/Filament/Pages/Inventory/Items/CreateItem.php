<?php

namespace App\Filament\Pages\Inventory\Items;

use App\Filament\Pages\Inventory\BaseInventoryPage;
use App\Models\InventoryCategory;
use App\Models\InventoryUnit;
use Illuminate\Database\Eloquent\Collection;

class CreateItem extends BaseInventoryPage
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-plus-circle';

    protected static string | \UnitEnum | null $navigationGroup = 'Items';

    protected static ?string $title = 'Add New Item';

    protected static ?string $navigationLabel = 'Add New Item';

    protected static ?string $slug = 'inventory/items/create';

    protected static ?int $navigationSort = 82;

    protected string $view = 'filament.pages.inventory.items.create';

    public static function getNavigationUrl(): string
    {
        return static::getUrl(panel: 'admin');
    }

    public function getCategories(): Collection
    {
        return InventoryCategory::query()
            ->orderBy('name')
            ->get();
    }

    public function getUnits(): Collection
    {
        return InventoryUnit::query()
            ->orderBy('name')
            ->get();
    }
}
