<?php

namespace App\Filament\Pages\Inventory\Requisitions;

use App\Filament\Pages\Inventory\BaseInventoryPage;
use App\Models\InventoryItem;
use App\Models\InventoryRequisition;
use App\Models\InventoryUnit;
use App\Services\InventoryStockService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateRequisition extends BaseInventoryPage
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-plus-circle';

    protected static string | \UnitEnum | null $navigationGroup = 'Requisitions';

    protected static ?string $title = 'Add Requisition';

    protected static ?string $navigationLabel = 'Add Requisition';

    protected static ?string $slug = 'inventory/requisitions/create';

    protected static ?int $navigationSort = 62;

    protected string $view = 'filament.pages.inventory.requisitions.create';

    public string $requisitionDate = '';

    public string $requesterName = '';

    public string $department = '';

    public string $purpose = '';

    public string $selectedItemId = '';

    public string $selectedUnitId = '';

    public string $quantity = '1';

    public string $notes = '';

    public array $selectedItems = [];

    public function mount(): void
    {
        $this->requisitionDate = now()->toDateString();
        $this->requesterName = auth()->user()?->name ?? '';
    }

    public static function getNavigationUrl(): string
    {
        return static::getUrl(panel: 'admin');
    }

    public function updatedSelectedItemId(): void
    {
        $item = $this->selectedItem();

        $this->selectedUnitId = $item ? (string) $item->inventory_unit_id : '';
    }

    public function addSelectedItem(): void
    {
        $this->validate([
            'selectedItemId' => ['required', 'exists:inventory_items,id'],
            'selectedUnitId' => ['required', 'exists:inventory_units,id'],
            'quantity' => ['required', 'numeric', 'min:1'],
            'notes' => ['nullable', 'string'],
        ], [], [
            'selectedItemId' => 'item',
            'selectedUnitId' => 'unit',
        ]);

        $item = InventoryItem::query()->with('unit')->findOrFail($this->selectedItemId);
        $unit = InventoryUnit::query()->findOrFail($this->selectedUnitId);
        $quantity = (float) $this->quantity;

        foreach ($this->selectedItems as $index => $selectedItem) {
            if ((int) $selectedItem['inventory_item_id'] === $item->id) {
                $quantity += (float) $selectedItem['quantity'];

                $this->ensureItemQuantityIsAvailable($item->id, $quantity, 'quantity');

                $this->selectedItems[$index]['inventory_unit_id'] = $unit->id;
                $this->selectedItems[$index]['unit_name'] = $unit->name;
                $this->selectedItems[$index]['quantity'] = $this->numberForInput($quantity);
                $this->selectedItems[$index]['notes'] = trim($this->notes) !== '' ? trim($this->notes) : $selectedItem['notes'];

                $this->resetSelectionRow();

                return;
            }
        }

        $this->ensureItemQuantityIsAvailable($item->id, $quantity, 'quantity');

        $this->selectedItems[] = [
            'inventory_item_id' => $item->id,
            'item_name' => $item->name,
            'inventory_unit_id' => $unit->id,
            'unit_name' => $unit->name,
            'quantity' => $this->numberForInput($quantity),
            'notes' => trim($this->notes),
        ];

        $this->resetSelectionRow();
    }

    public function removeSelectedItem(int $index): void
    {
        unset($this->selectedItems[$index]);

        $this->selectedItems = array_values($this->selectedItems);
    }

    public function submitRequisition(): mixed
    {
        $this->validate([
            'requisitionDate' => ['required', 'date'],
            'requesterName' => ['required', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'purpose' => ['nullable', 'string', 'max:255'],
            'selectedItems' => ['required', 'array', 'min:1'],
            'selectedItems.*.inventory_item_id' => ['required', 'exists:inventory_items,id'],
            'selectedItems.*.inventory_unit_id' => ['required', 'exists:inventory_units,id'],
            'selectedItems.*.quantity' => ['required', 'numeric', 'min:1'],
            'selectedItems.*.notes' => ['nullable', 'string'],
        ], [], [
            'requisitionDate' => 'requisition date',
            'requesterName' => 'requester',
            'selectedItems' => 'items',
        ]);

        $this->ensureSelectedItemsAreAvailable();

        DB::transaction(function (): void {
            $requisition = InventoryRequisition::query()->create([
                'ref_no' => $this->generateRefNo(),
                'requisition_date' => $this->requisitionDate,
                'requester_name' => $this->requesterName,
                'department' => $this->department ?: null,
                'purpose' => $this->purpose ?: null,
                'status' => InventoryRequisition::STATUS_PENDING,
            ]);

            foreach ($this->selectedItems as $selectedItem) {
                $requisition->items()->create([
                    'inventory_item_id' => $selectedItem['inventory_item_id'],
                    'inventory_unit_id' => $selectedItem['inventory_unit_id'],
                    'quantity' => (float) $selectedItem['quantity'],
                    'notes' => $selectedItem['notes'] ?: null,
                ]);
            }
        });

        session()->flash('success', __('Requisition submitted successfully.'));

        return redirect()->to(AllRequisitions::getUrl(panel: 'admin'));
    }

    public function getItems(): Collection
    {
        $items = InventoryItem::query()
            ->with('unit')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $availableQuantities = app(InventoryStockService::class)
            ->availableQuantities($items->pluck('id')->all());

        return $items
            ->filter(fn (InventoryItem $item): bool => (float) ($availableQuantities[$item->id] ?? 0) > 0)
            ->each(fn (InventoryItem $item) => $item->setAttribute('available_stock', (float) ($availableQuantities[$item->id] ?? 0)))
            ->values();
    }

    public function getUnits(): Collection
    {
        return InventoryUnit::query()
            ->orderBy('name')
            ->get();
    }

    public function selectedItem(): ?InventoryItem
    {
        if ($this->selectedItemId === '') {
            return null;
        }

        return InventoryItem::query()->with('unit')->find($this->selectedItemId);
    }

    public function formatNumber(float $value): string
    {
        return $this->numberForInput($value);
    }

    private function resetSelectionRow(): void
    {
        $this->selectedItemId = '';
        $this->selectedUnitId = '';
        $this->quantity = '1';
        $this->notes = '';
        $this->resetValidation(['selectedItemId', 'selectedUnitId', 'quantity', 'notes']);
    }

    private function ensureSelectedItemsAreAvailable(): void
    {
        $requestedByItem = collect($this->selectedItems)
            ->groupBy('inventory_item_id')
            ->map(fn ($items): float => $items->sum(fn (array $item): float => (float) $item['quantity']));

        foreach ($requestedByItem as $itemId => $quantity) {
            $this->ensureItemQuantityIsAvailable((int) $itemId, (float) $quantity, 'selectedItems');
        }
    }

    private function ensureItemQuantityIsAvailable(int $itemId, float $quantity, string $field): void
    {
        $availableQuantity = app(InventoryStockService::class)->availableQuantity($itemId);

        if ($quantity <= $availableQuantity) {
            return;
        }

        throw ValidationException::withMessages([
            $field => __('Requested quantity cannot exceed available stock (:available).', [
                'available' => $this->numberForInput($availableQuantity),
            ]),
        ]);
    }

    private function generateRefNo(): string
    {
        do {
            $refNo = 'REQ-' . random_int(10000, 99999);
        } while (InventoryRequisition::query()->where('ref_no', $refNo)->exists());

        return $refNo;
    }

    private function numberForInput(float $value): string
    {
        return rtrim(rtrim(number_format($value, 2, '.', ''), '0'), '.');
    }
}
