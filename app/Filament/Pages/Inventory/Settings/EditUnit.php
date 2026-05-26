<?php

namespace App\Filament\Pages\Inventory\Settings;

use App\Filament\Pages\Inventory\BaseInventoryPage;
use App\Models\InventoryUnit;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EditUnit extends BaseInventoryPage
{
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $title = 'Edit Unit';

    protected static ?string $slug = 'inventory/units/edit';

    protected string $view = 'filament.pages.inventory.settings.unit-edit';

    public InventoryUnit $unit;

    public static function getNavigationUrl(): string
    {
        return Units::getUrl(panel: 'admin');
    }

    public function mount(): void
    {
        $unitId = (int) request()->query('id', 0);

        if ($unitId <= 0) {
            throw new NotFoundHttpException('Unit not found.');
        }

        $this->unit = InventoryUnit::query()->findOrFail($unitId);
    }

    public static function urlForUnit(int $unitId, string $panel = 'admin'): string
    {
        return static::getUrl(panel: $panel) . '?id=' . $unitId;
    }
}
