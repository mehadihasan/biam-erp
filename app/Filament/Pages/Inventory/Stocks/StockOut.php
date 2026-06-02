<?php

namespace App\Filament\Pages\Inventory\Stocks;

use App\Filament\Pages\Inventory\BaseInventoryPage;
use App\Models\InventoryItem;
use App\Models\InventoryStockOut;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Validator;

class StockOut extends BaseInventoryPage
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-arrow-up-tray';

    protected static string | \UnitEnum | null $navigationGroup = 'Stocks';

    protected static ?string $title = 'Issue Stock';

    protected static ?string $navigationLabel = 'Stock Out';

    protected static ?string $slug = 'inventory/stocks/out';

    protected static ?int $navigationSort = 73;

    protected string $view = 'filament.pages.inventory.stocks.stock-out';

    public string $selectedItemId = '';

    public string $quantityToIssue = '0';

    public string $issuedToDepartment = '';

    public string $purposeReason = '';

    public string $referenceDocument = '';

    public string $transactionDate = '';

    public function mount(): void
    {
        $this->transactionDate = now()->toDateString();
    }

    public function issueStock(): void
    {
        $this->withValidator(function (Validator $validator): void {
            $validator->after(function (Validator $validator): void {
                $item = $this->selectedItem();

                if (! $item) {
                    return;
                }

                if ((float) $this->quantityToIssue > (float) $item->current_stock_quantity) {
                    $validator->errors()->add('quantityToIssue', __('Quantity cannot exceed available stock.'));
                }
            });
        })->validate([
            'selectedItemId' => ['required', 'exists:inventory_items,id'],
            'quantityToIssue' => ['required', 'numeric', 'gt:0'],
            'issuedToDepartment' => ['required', 'string', 'max:255'],
            'purposeReason' => ['required', 'string'],
            'referenceDocument' => ['nullable', 'string', 'max:255'],
            'transactionDate' => ['required', 'date'],
        ], [], [
            'selectedItemId' => 'item',
            'quantityToIssue' => 'quantity to issue',
            'issuedToDepartment' => 'issued to department',
            'purposeReason' => 'purpose / reason',
            'referenceDocument' => 'reference document',
            'transactionDate' => 'transaction date',
        ]);

        DB::transaction(function (): void {
            $item = InventoryItem::query()
                ->lockForUpdate()
                ->findOrFail($this->selectedItemId);
            $stockBefore = (float) $item->current_stock_quantity;
            $quantity = (float) $this->quantityToIssue;
            $stockAfter = $stockBefore - $quantity;

            if ($stockAfter < 0) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'quantityToIssue' => __('Quantity cannot exceed available stock.'),
                ]);
            }

            InventoryStockOut::query()->create([
                'inventory_item_id' => $item->id,
                'quantity' => $quantity,
                'issued_to_department' => $this->issuedToDepartment,
                'purpose_reason' => $this->purposeReason,
                'reference_document' => $this->referenceDocument ?: null,
                'transaction_date' => $this->transactionDate,
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'created_by' => auth()->id(),
            ]);

            $item->current_stock_quantity = $stockAfter;
            $item->save();
        });

        $this->resetForm();

        session()->flash('success', __('Stock issued successfully.'));
    }

    public function getItems(): Collection
    {
        return InventoryItem::query()
            ->with('unit')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function selectedItem(): ?InventoryItem
    {
        if ($this->selectedItemId === '') {
            return null;
        }

        return InventoryItem::query()
            ->with('unit')
            ->find($this->selectedItemId);
    }

    public function quantityAfter(): float
    {
        $item = $this->selectedItem();

        if (! $item) {
            return 0;
        }

        return max((float) $item->current_stock_quantity - max((float) $this->quantityToIssue, 0), 0);
    }

    public function canIssue(): bool
    {
        $item = $this->selectedItem();
        $quantity = (float) $this->quantityToIssue;

        return $item
            && $quantity > 0
            && $quantity <= (float) $item->current_stock_quantity;
    }

    public function formatNumber(float $value): string
    {
        return rtrim(rtrim(number_format($value, 2, '.', ''), '0'), '.');
    }

    private function resetForm(): void
    {
        $this->selectedItemId = '';
        $this->quantityToIssue = '0';
        $this->issuedToDepartment = '';
        $this->purposeReason = '';
        $this->referenceDocument = '';
        $this->transactionDate = now()->toDateString();
        $this->resetValidation();
    }
}
