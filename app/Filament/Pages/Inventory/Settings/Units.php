<?php

namespace App\Filament\Pages\Inventory\Settings;

use App\Filament\Pages\Inventory\BaseInventoryPage;
use App\Models\InventoryUnit;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class Units extends BaseInventoryPage
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-scale';

    protected static string | \UnitEnum | null $navigationGroup = 'Settings';

    protected static ?string $title = 'Units';

    protected static ?string $navigationLabel = 'Unit';

    protected static ?string $slug = 'inventory/units';

    protected static ?int $navigationSort = 92;

    protected string $view = 'filament.pages.inventory.settings.units';

    public static function getNavigationUrl(): string
    {
        return static::getUrl(panel: 'admin');
    }

    public function getUnits(): LengthAwarePaginator
    {
        return InventoryUnit::query()
            ->latest()
            ->paginate(10);
    }
}
