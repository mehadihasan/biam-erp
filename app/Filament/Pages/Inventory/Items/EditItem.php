<?php

namespace App\Filament\Pages\Inventory\Items;

use App\Filament\Pages\Inventory\BaseInventoryPage;
use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use App\Models\InventoryUnit;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EditItem extends BaseInventoryPage
{
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $title = 'Edit Item';

    protected static ?string $slug = 'inventory/items/edit';

    protected string $view = 'filament.pages.inventory.items.edit';

    public InventoryItem $item;

    public static function getNavigationUrl(): string
    {
        return AllItems::getUrl(panel: 'admin');
    }

    public function mount(): void
    {
        $itemId = (int) request()->query('id', 0);

        if ($itemId <= 0) {
            throw new NotFoundHttpException('Item not found.');
        }

        $this->item = InventoryItem::query()->findOrFail($itemId);
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

    public static function urlForItem(int $itemId, string $panel = 'admin'): string
    {
        return static::getUrl(panel: $panel) . '?id=' . $itemId;
    }
}
