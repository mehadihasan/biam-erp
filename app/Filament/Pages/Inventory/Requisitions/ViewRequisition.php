<?php

namespace App\Filament\Pages\Inventory\Requisitions;

use App\Filament\Pages\Inventory\BaseInventoryPage;
use App\Models\InventoryRequisition;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ViewRequisition extends BaseInventoryPage
{
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $title = 'View Requisition';

    protected static ?string $slug = 'inventory/requisitions/view';

    protected string $view = 'filament.pages.inventory.requisitions.view';

    public InventoryRequisition $requisition;

    public static function getNavigationUrl(): string
    {
        return AllRequisitions::getUrl(panel: 'admin');
    }

    public function mount(): void
    {
        $requisitionId = (int) request()->query('id', 0);

        if ($requisitionId <= 0) {
            throw new NotFoundHttpException('Requisition not found.');
        }

        $this->requisition = InventoryRequisition::query()
            ->with(['items.item', 'items.unit'])
            ->findOrFail($requisitionId);
    }

    public static function urlForRequisition(int $requisitionId, string $panel = 'admin'): string
    {
        return static::getUrl(panel: $panel) . '?id=' . $requisitionId;
    }

    public function formatNumber(float $value): string
    {
        return rtrim(rtrim(number_format($value, 2, '.', ''), '0'), '.');
    }
}
