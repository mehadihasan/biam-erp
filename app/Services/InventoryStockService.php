<?php

namespace App\Services;

use App\Models\InventoryRequisition;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class InventoryStockService
{
    public function availableQuantity(int $itemId): float
    {
        $stockIn = (float) DB::table('inventory_stock_in_items')
            ->where('inventory_item_id', $itemId)
            ->sum('quantity');

        $stockOut = (float) DB::table('inventory_stock_outs')
            ->where('inventory_item_id', $itemId)
            ->sum('quantity');

        return max($stockIn - $stockOut, 0);
    }

    public function availableQuantities(?array $itemIds = null): Collection
    {
        $stockIns = DB::table('inventory_stock_in_items')
            ->select('inventory_item_id', DB::raw('SUM(quantity) as quantity'))
            ->when($itemIds !== null, fn ($query) => $query->whereIn('inventory_item_id', $itemIds))
            ->groupBy('inventory_item_id')
            ->pluck('quantity', 'inventory_item_id');

        $stockOuts = DB::table('inventory_stock_outs')
            ->select('inventory_item_id', DB::raw('SUM(quantity) as quantity'))
            ->when($itemIds !== null, fn ($query) => $query->whereIn('inventory_item_id', $itemIds))
            ->groupBy('inventory_item_id')
            ->pluck('quantity', 'inventory_item_id');

        return $stockIns
            ->mapWithKeys(fn ($quantity, $itemId): array => [
                (int) $itemId => max((float) $quantity - (float) ($stockOuts[$itemId] ?? 0), 0),
            ])
            ->filter(fn (float $quantity): bool => $quantity > 0);
    }

    public function remainingRequisitionItemQuantities(InventoryRequisition $requisition): Collection
    {
        $stockOuts = DB::table('inventory_stock_outs')
            ->select('inventory_requisition_item_id', DB::raw('SUM(quantity) as quantity'))
            ->where('inventory_requisition_id', $requisition->id)
            ->whereNotNull('inventory_requisition_item_id')
            ->groupBy('inventory_requisition_item_id')
            ->pluck('quantity', 'inventory_requisition_item_id');

        return $requisition->items
            ->mapWithKeys(fn ($item): array => [
                $item->id => max((float) $item->quantity - (float) ($stockOuts[$item->id] ?? 0), 0),
            ]);
    }

    public function hasRemainingRequisitionQuantity(InventoryRequisition $requisition): bool
    {
        return $this->remainingRequisitionItemQuantities($requisition)
            ->contains(fn (float $quantity): bool => $quantity > 0);
    }
}
