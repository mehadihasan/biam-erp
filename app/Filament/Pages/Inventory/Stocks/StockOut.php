<?php

namespace App\Filament\Pages\Inventory\Stocks;

use App\Filament\Pages\Inventory\BaseInventoryPage;
use App\Models\InventoryItem;
use App\Models\InventoryRequisition;
use App\Models\InventoryStockOut;
use App\Services\InventoryStockService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StockOut extends BaseInventoryPage
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-arrow-up-tray';

    protected static string | \UnitEnum | null $navigationGroup = 'Stocks';

    protected static ?string $title = 'Issue Stock';

    protected static ?string $navigationLabel = 'Stock Out';

    protected static ?string $slug = 'inventory/stocks/out';

    protected static ?int $navigationSort = 73;

    protected string $view = 'filament.pages.inventory.stocks.stock-out';

    public string $selectedRequisitionId = '';

    public string $issuedToDepartment = '';

    public string $purposeReason = '';

    public string $referenceDocument = '';

    public string $transactionDate = '';

    public array $stockOutItems = [];

    public function mount(): void
    {
        $this->transactionDate = now()->toDateString();
    }

    public function updatedSelectedRequisitionId(): void
    {
        $this->loadSelectedRequisitionItems();
    }

    public function issueStock(): void
    {
        $this->validateStockOut();

        DB::transaction(function (): void {
            $requisition = InventoryRequisition::query()
                ->with('items')
                ->where('status', InventoryRequisition::STATUS_APPROVED)
                ->lockForUpdate()
                ->findOrFail($this->selectedRequisitionId);
            $remainingQuantities = app(InventoryStockService::class)->remainingRequisitionItemQuantities($requisition);

            foreach ($this->stockOutItems as $stockOutItem) {
                $quantity = (float) ($stockOutItem['quantity_to_issue'] ?? 0);

                if ($quantity <= 0) {
                    continue;
                }

                $remainingQuantity = (float) ($remainingQuantities[$stockOutItem['inventory_requisition_item_id']] ?? 0);

                if ($quantity > $remainingQuantity) {
                    throw ValidationException::withMessages([
                        'stockOutItems' => __('Stock out quantity cannot exceed approved remaining quantity for :item.', [
                            'item' => $stockOutItem['item_name'] ?? __('selected item'),
                        ]),
                    ]);
                }

                $item = InventoryItem::query()
                    ->lockForUpdate()
                    ->findOrFail($stockOutItem['inventory_item_id']);
                $stockBefore = app(InventoryStockService::class)->availableQuantity($item->id);
                $stockAfter = $stockBefore - $quantity;

                if ($stockAfter < 0) {
                    throw ValidationException::withMessages([
                        'stockOutItems' => __('Stock out quantity cannot exceed available stock for :item.', [
                            'item' => $stockOutItem['item_name'] ?? __('selected item'),
                        ]),
                    ]);
                }

                InventoryStockOut::query()->create([
                    'inventory_requisition_id' => $requisition->id,
                    'inventory_requisition_item_id' => $stockOutItem['inventory_requisition_item_id'],
                    'inventory_item_id' => $item->id,
                    'quantity' => $quantity,
                    'issued_to_department' => $this->issuedToDepartment,
                    'purpose_reason' => $this->purposeReason,
                    'reference_document' => $this->referenceDocument ?: $requisition->ref_no,
                    'transaction_date' => $this->transactionDate,
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockAfter,
                    'created_by' => auth()->id(),
                ]);

                $item->current_stock_quantity = max($stockAfter, 0);
                $item->save();
            }
        });

        $this->resetForm();

        session()->flash('success', __('Stock issued successfully.'));
    }

    public function getEligibleRequisitions(): Collection
    {
        return InventoryRequisition::query()
            ->with(['items.item.unit'])
            ->where('status', InventoryRequisition::STATUS_APPROVED)
            ->latest('requisition_date')
            ->get()
            ->filter(fn (InventoryRequisition $requisition): bool => app(InventoryStockService::class)->hasRemainingRequisitionQuantity($requisition))
            ->values();
    }

    public function selectedRequisition(): ?InventoryRequisition
    {
        if ($this->selectedRequisitionId === '') {
            return null;
        }

        return InventoryRequisition::query()
            ->with(['items.item.unit'])
            ->find($this->selectedRequisitionId);
    }

    public function issueTotal(): float
    {
        return collect($this->stockOutItems)
            ->sum(fn (array $item): float => (float) ($item['quantity_to_issue'] ?? 0));
    }

    public function canIssue(): bool
    {
        return $this->selectedRequisitionId !== ''
            && $this->issueTotal() > 0;
    }

    public function formatNumber(float $value): string
    {
        return rtrim(rtrim(number_format($value, 2, '.', ''), '0'), '.');
    }

    private function loadSelectedRequisitionItems(): void
    {
        $this->stockOutItems = [];
        $this->resetValidation();

        $requisition = $this->selectedRequisition();

        if (! $requisition || $requisition->status !== InventoryRequisition::STATUS_APPROVED) {
            $this->issuedToDepartment = '';
            $this->purposeReason = '';
            $this->referenceDocument = '';

            return;
        }

        $remainingQuantities = app(InventoryStockService::class)->remainingRequisitionItemQuantities($requisition);

        $this->issuedToDepartment = $requisition->department ?? '';
        $this->purposeReason = $requisition->purpose ?? '';
        $this->referenceDocument = $requisition->ref_no;
        $this->stockOutItems = $requisition->items
            ->filter(fn ($item): bool => (float) ($remainingQuantities[$item->id] ?? 0) > 0)
            ->map(function ($item) use ($remainingQuantities): array {
                $availableStock = app(InventoryStockService::class)->availableQuantity($item->inventory_item_id);
                $remainingQuantity = (float) ($remainingQuantities[$item->id] ?? 0);

                return [
                    'inventory_requisition_item_id' => $item->id,
                    'inventory_item_id' => $item->inventory_item_id,
                    'item_name' => $item->item?->name ?? 'Item #' . $item->inventory_item_id,
                    'unit_name' => $item->unit?->name ?? '',
                    'approved_quantity' => $this->formatNumber((float) $item->quantity),
                    'remaining_quantity' => $this->formatNumber($remainingQuantity),
                    'available_stock' => $this->formatNumber($availableStock),
                    'quantity_to_issue' => $this->formatNumber(min($remainingQuantity, $availableStock)),
                ];
            })
            ->values()
            ->all();
    }

    private function validateStockOut(): void
    {
        $this->validate([
            'selectedRequisitionId' => ['required', 'exists:inventory_requisitions,id'],
            'issuedToDepartment' => ['required', 'string', 'max:255'],
            'purposeReason' => ['required', 'string'],
            'referenceDocument' => ['nullable', 'string', 'max:255'],
            'transactionDate' => ['required', 'date'],
            'stockOutItems' => ['required', 'array', 'min:1'],
            'stockOutItems.*.inventory_requisition_item_id' => ['required', 'exists:inventory_requisition_items,id'],
            'stockOutItems.*.inventory_item_id' => ['required', 'exists:inventory_items,id'],
            'stockOutItems.*.quantity_to_issue' => ['nullable', 'numeric', 'min:0'],
        ], [], [
            'selectedRequisitionId' => 'requisition',
            'issuedToDepartment' => 'issued to department',
            'purposeReason' => 'purpose / reason',
            'referenceDocument' => 'reference document',
            'transactionDate' => 'transaction date',
            'stockOutItems' => 'approved requisition items',
            'stockOutItems.*.quantity_to_issue' => 'quantity to issue',
        ]);

        $requisition = InventoryRequisition::query()
            ->with('items')
            ->where('status', InventoryRequisition::STATUS_APPROVED)
            ->find($this->selectedRequisitionId);

        if (! $requisition) {
            throw ValidationException::withMessages([
                'selectedRequisitionId' => __('Only approved requisitions can be stocked out.'),
            ]);
        }

        if ($this->issueTotal() <= 0) {
            throw ValidationException::withMessages([
                'stockOutItems' => __('Enter at least one quantity to issue.'),
            ]);
        }

        $remainingQuantities = app(InventoryStockService::class)->remainingRequisitionItemQuantities($requisition);

        foreach ($this->stockOutItems as $index => $stockOutItem) {
            $quantity = (float) ($stockOutItem['quantity_to_issue'] ?? 0);
            $remainingQuantity = (float) ($remainingQuantities[$stockOutItem['inventory_requisition_item_id']] ?? 0);
            $availableStock = app(InventoryStockService::class)->availableQuantity((int) $stockOutItem['inventory_item_id']);

            if ($quantity > $remainingQuantity) {
                throw ValidationException::withMessages([
                    "stockOutItems.$index.quantity_to_issue" => __('Quantity cannot exceed approved remaining quantity (:quantity).', [
                        'quantity' => $this->formatNumber($remainingQuantity),
                    ]),
                ]);
            }

            if ($quantity > $availableStock) {
                throw ValidationException::withMessages([
                    "stockOutItems.$index.quantity_to_issue" => __('Quantity cannot exceed available stock (:quantity).', [
                        'quantity' => $this->formatNumber($availableStock),
                    ]),
                ]);
            }
        }
    }

    private function resetForm(): void
    {
        $this->selectedRequisitionId = '';
        $this->issuedToDepartment = '';
        $this->purposeReason = '';
        $this->referenceDocument = '';
        $this->transactionDate = now()->toDateString();
        $this->stockOutItems = [];
        $this->resetValidation();
    }
}
