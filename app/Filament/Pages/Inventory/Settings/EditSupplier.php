<?php

namespace App\Filament\Pages\Inventory\Settings;

use App\Filament\Pages\Inventory\BaseInventoryPage;
use App\Models\InventorySupplier;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EditSupplier extends BaseInventoryPage
{
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $title = 'Edit Supplier';

    protected static ?string $slug = 'inventory/settings/suppliers/edit';

    protected string $view = 'filament.pages.inventory.settings.supplier-edit';

    public InventorySupplier $supplier;

    public static function getNavigationUrl(): string
    {
        return Suppliers::getUrl(panel: 'admin');
    }

    public function mount(): void
    {
        $supplierId = (int) request()->query('id', 0);

        if ($supplierId <= 0) {
            throw new NotFoundHttpException('Supplier not found.');
        }

        $this->supplier = InventorySupplier::query()->findOrFail($supplierId);
    }

    public static function urlForSupplier(int $supplierId, string $panel = 'admin'): string
    {
        return static::getUrl(panel: $panel) . '?id=' . $supplierId;
    }
}
