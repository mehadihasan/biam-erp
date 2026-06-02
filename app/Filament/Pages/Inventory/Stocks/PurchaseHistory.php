<?php

namespace App\Filament\Pages\Inventory\Stocks;

use App\Filament\Pages\Inventory\BaseInventoryPage;
use App\Models\InventoryItem;
use App\Models\InventoryStockInItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PurchaseHistory extends BaseInventoryPage
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-clock';

    protected static string | \UnitEnum | null $navigationGroup = 'Stocks';

    protected static ?string $title = 'Purchase History';

    protected static ?string $navigationLabel = 'Purchase History';

    protected static ?string $slug = 'inventory/stocks/purchase-history';

    protected static ?int $navigationSort = 72;

    protected string $view = 'filament.pages.inventory.stocks.purchase-history';

    public string $selectedItemId = '';

    public string $fromDate = '';

    public string $toDate = '';

    public function getItems(): Collection
    {
        return InventoryItem::query()
            ->orderBy('name')
            ->get();
    }

    public function selectedItem(): ?InventoryItem
    {
        if ($this->selectedItemId === '') {
            return null;
        }

        return InventoryItem::query()->find($this->selectedItemId);
    }

    public function historyRows(): Collection
    {
        if ($this->selectedItemId === '') {
            return new Collection();
        }

        return $this->historyQuery()
            ->latest('inventory_stock_ins.stock_in_date')
            ->latest('inventory_stock_in_items.id')
            ->get();
    }

    public function summary(): array
    {
        $rows = $this->historyRows();
        $totalQty = (float) $rows->sum('quantity');
        $totalCost = (float) $rows->sum('total');

        return [
            'purchases' => $rows->count(),
            'totalQty' => $totalQty,
            'totalCost' => $totalCost,
            'avgRate' => $totalQty > 0 ? $totalCost / $totalQty : 0,
        ];
    }

    public function exportCsv(): ?StreamedResponse
    {
        $item = $this->selectedItem();

        if (! $item) {
            return null;
        }

        $rows = $this->historyRows();
        $fileName = 'item-purchase-history-' . $item->item_code . '.csv';

        return Response::streamDownload(function () use ($rows): void {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['#', 'Date', 'Ref', 'Supplier', 'Qty', 'Unit', 'Unit Cost', 'Total', 'Reference No.', 'Expiry']);

            foreach ($rows as $index => $row) {
                fputcsv($handle, [
                    $index + 1,
                    $row->stockIn?->stock_in_date?->toDateString() ?: '-',
                    $this->transactionRef($row),
                    $row->stockIn?->supplier?->company_name ?: '-',
                    $this->formatNumber((float) $row->quantity),
                    $row->unit?->name ?: '-',
                    $this->formatNumber((float) $row->unit_cost),
                    $this->formatNumber((float) $row->total),
                    $row->stockIn?->reference_number ?: '-',
                    $row->stockIn?->expiry_warranty_date?->toDateString() ?: '-',
                ]);
            }

            fclose($handle);
        }, $fileName, ['Content-Type' => 'text/csv']);
    }

    public function transactionRef(InventoryStockInItem $row): string
    {
        return 'TXN-' . str_pad((string) $row->inventory_stock_in_id, 5, '0', STR_PAD_LEFT);
    }

    public function formatNumber(float $value): string
    {
        return rtrim(rtrim(number_format($value, 3, '.', ''), '0'), '.');
    }

    private function historyQuery(): Builder
    {
        return InventoryStockInItem::query()
            ->select('inventory_stock_in_items.*')
            ->with(['stockIn.supplier', 'unit'])
            ->join('inventory_stock_ins', 'inventory_stock_ins.id', '=', 'inventory_stock_in_items.inventory_stock_in_id')
            ->where('inventory_stock_in_items.inventory_item_id', $this->selectedItemId)
            ->when($this->fromDate !== '', fn (Builder $query) => $query->whereDate('inventory_stock_ins.stock_in_date', '>=', $this->fromDate))
            ->when($this->toDate !== '', fn (Builder $query) => $query->whereDate('inventory_stock_ins.stock_in_date', '<=', $this->toDate));
    }
}
