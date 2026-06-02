<?php

namespace App\Filament\Pages\Inventory\Stocks;

use App\Filament\Pages\Inventory\BaseInventoryPage;
use App\Models\InventoryItem;
use App\Models\InventoryStockIn;
use App\Models\InventorySupplier;
use App\Models\InventoryUnit;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\WithFileUploads;

class StockInPurchase extends BaseInventoryPage
{
    use WithFileUploads;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-arrow-down-tray';

    protected static string | \UnitEnum | null $navigationGroup = 'Stocks';

    protected static ?string $title = 'Record Stock In';

    protected static ?string $navigationLabel = 'Stock In / Purchase';

    protected static ?string $slug = 'inventory/stocks/in';

    protected static ?int $navigationSort = 71;

    protected string $view = 'filament.pages.inventory.stocks.stock-in-purchase';

    public string $selectedItemId = '';

    public string $selectedUnitId = '';

    public string $quantity = '1';

    public string $unitCost = '0';

    public string $stockInDate = '';

    public string $expiryWarrantyDate = '';

    public string $referenceNumber = '';

    public string $supplierId = '';

    public string $purposeNotes = '';

    public $attachment = null;

    public array $selectedItems = [];

    public function mount(): void
    {
        $this->stockInDate = now()->toDateString();
    }

    public function updatedSelectedItemId(): void
    {
        $item = $this->selectedItem();

        if (! $item) {
            $this->selectedUnitId = '';
            $this->unitCost = '0';

            return;
        }

        $this->selectedUnitId = (string) $item->inventory_unit_id;
        $this->unitCost = (string) $item->unit_cost;
    }

    public function addSelectedItem(): void
    {
        $this->validate([
            'selectedItemId' => ['required', 'exists:inventory_items,id'],
            'selectedUnitId' => ['required', 'exists:inventory_units,id'],
            'quantity' => ['required', 'numeric', 'min:1'],
            'unitCost' => ['required', 'numeric', 'min:0'],
        ], [], [
            'selectedItemId' => 'item',
            'selectedUnitId' => 'unit type',
            'unitCost' => 'unit cost',
        ]);

        $item = InventoryItem::query()
            ->with('unit')
            ->findOrFail($this->selectedItemId);
        $unit = InventoryUnit::query()->findOrFail($this->selectedUnitId);
        $quantity = (float) $this->quantity;
        $unitCost = (float) $this->unitCost;

        foreach ($this->selectedItems as $index => $selectedItem) {
            if ((int) $selectedItem['inventory_item_id'] === $item->id) {
                $quantity += (float) $selectedItem['quantity'];

                $this->selectedItems[$index]['inventory_unit_id'] = $unit->id;
                $this->selectedItems[$index]['unit_name'] = $unit->name;
                $this->selectedItems[$index]['quantity'] = $this->numberForInput($quantity);
                $this->selectedItems[$index]['unit_cost'] = $this->numberForInput($unitCost);
                $this->selectedItems[$index]['total'] = $this->numberForInput($quantity * $unitCost);

                $this->resetSelectionRow();

                return;
            }
        }

        $this->selectedItems[] = [
            'inventory_item_id' => $item->id,
            'item_code' => $item->item_code,
            'item_name' => $item->name,
            'inventory_unit_id' => $unit->id,
            'unit_name' => $unit->name,
            'quantity' => $this->numberForInput($quantity),
            'unit_cost' => $this->numberForInput($unitCost),
            'total' => $this->numberForInput($quantity * $unitCost),
        ];

        $this->resetSelectionRow();
    }

    public function removeSelectedItem(int $index): void
    {
        unset($this->selectedItems[$index]);

        $this->selectedItems = array_values($this->selectedItems);
    }

    public function recordStockIn(): mixed
    {
        $this->validate([
            'stockInDate' => ['required', 'date'],
            'expiryWarrantyDate' => ['nullable', 'date', 'after_or_equal:stockInDate'],
            'referenceNumber' => ['nullable', 'string', 'max:255'],
            'supplierId' => ['nullable', 'exists:inventory_suppliers,id'],
            'purposeNotes' => ['nullable', 'string'],
            'attachment' => ['nullable', 'file', 'max:5120'],
            'selectedItems' => ['required', 'array', 'min:1'],
            'selectedItems.*.inventory_item_id' => ['required', 'exists:inventory_items,id'],
            'selectedItems.*.inventory_unit_id' => ['required', 'exists:inventory_units,id'],
            'selectedItems.*.quantity' => ['required', 'numeric', 'min:1'],
            'selectedItems.*.unit_cost' => ['required', 'numeric', 'min:0'],
        ], [], [
            'stockInDate' => 'stock-in date',
            'expiryWarrantyDate' => 'expiry / warranty date',
            'referenceNumber' => 'reference number',
            'supplierId' => 'supplier',
            'purposeNotes' => 'purpose / notes',
            'selectedItems' => 'items',
        ]);

        $attachmentPath = $this->attachment?->store('inventory/stock-ins', 'public');

        DB::transaction(function () use ($attachmentPath): void {
            $stockIn = InventoryStockIn::query()->create([
                'stock_in_date' => $this->stockInDate,
                'expiry_warranty_date' => $this->expiryWarrantyDate ?: null,
                'reference_number' => $this->referenceNumber ?: null,
                'inventory_supplier_id' => $this->supplierId ?: null,
                'purpose_notes' => $this->purposeNotes ?: null,
                'attachment_path' => $attachmentPath,
                'grand_total' => $this->grandTotal(),
                'created_by' => auth()->id(),
            ]);

            foreach ($this->selectedItems as $selectedItem) {
                $quantity = (float) $selectedItem['quantity'];
                $unitCost = (float) $selectedItem['unit_cost'];
                $total = $quantity * $unitCost;

                $stockIn->items()->create([
                    'inventory_item_id' => $selectedItem['inventory_item_id'],
                    'inventory_unit_id' => $selectedItem['inventory_unit_id'],
                    'quantity' => $quantity,
                    'unit_cost' => $unitCost,
                    'total' => $total,
                ]);

                $item = InventoryItem::query()
                    ->lockForUpdate()
                    ->findOrFail($selectedItem['inventory_item_id']);

                $item->current_stock_quantity = (float) $item->current_stock_quantity + $quantity;
                $item->unit_cost = $unitCost;
                $item->save();
            }
        });

        $this->resetForm();

        session()->flash('success', __('Stock in recorded successfully.'));

        return null;
    }

    public function getItems(): Collection
    {
        return InventoryItem::query()
            ->with('unit')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function getUnits(): Collection
    {
        return InventoryUnit::query()
            ->orderBy('name')
            ->get();
    }

    public function getSuppliers(): Collection
    {
        return InventorySupplier::query()
            ->where('is_active', true)
            ->orderBy('company_name')
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

    public function grandTotal(): float
    {
        return array_sum(array_map(
            fn (array $selectedItem): float => (float) $selectedItem['total'],
            $this->selectedItems,
        ));
    }

    private function resetSelectionRow(): void
    {
        $this->selectedItemId = '';
        $this->selectedUnitId = '';
        $this->quantity = '1';
        $this->unitCost = '0';
        $this->resetValidation(['selectedItemId', 'selectedUnitId', 'quantity', 'unitCost']);
    }

    private function resetForm(): void
    {
        $this->resetSelectionRow();
        $this->stockInDate = now()->toDateString();
        $this->expiryWarrantyDate = '';
        $this->referenceNumber = '';
        $this->supplierId = '';
        $this->purposeNotes = '';
        $this->attachment = null;
        $this->selectedItems = [];
    }

    private function numberForInput(float $value): string
    {
        return rtrim(rtrim(number_format($value, 2, '.', ''), '0'), '.');
    }
}
