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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EditRequisition extends BaseInventoryPage
{
    private const FINALIZED_MESSAGE = 'This requisition has already been finalized and cannot be modified.';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $title = 'Edit Requisition';

    protected static ?string $slug = 'inventory/requisitions/edit';

    protected string $view = 'filament.pages.inventory.requisitions.edit';

    public InventoryRequisition $requisition;

    public string $requisitionDate = '';

    public string $requesterName = '';

    public string $department = '';

    public string $purpose = '';

    public string $selectedItemId = '';

    public string $selectedUnitId = '';

    public string $quantity = '1';

    public string $notes = '';

    public array $selectedItems = [];

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

        if ($this->requisition->status !== InventoryRequisition::STATUS_PENDING) {
            session()->flash('error', __(self::FINALIZED_MESSAGE));
            $this->redirect(AllRequisitions::getUrl(panel: 'admin'), navigate: true);

            return;
        }

        $this->requisitionDate = $this->requisition->requisition_date?->toDateString() ?? now()->toDateString();
        $this->requesterName = $this->requisition->requester_name;
        $this->department = $this->requisition->department ?? '';
        $this->purpose = $this->requisition->purpose ?? '';
        $this->selectedItems = $this->requisition->items
            ->map(fn ($item): array => [
                'inventory_item_id' => $item->inventory_item_id,
                'item_name' => $item->item?->name ?? 'Item #' . $item->inventory_item_id,
                'inventory_unit_id' => $item->inventory_unit_id,
                'unit_name' => $item->unit?->name ?? 'Unit #' . $item->inventory_unit_id,
                'quantity' => $this->numberForInput((float) $item->quantity),
                'notes' => $item->notes ?? '',
            ])
            ->values()
            ->all();
    }

    public static function urlForRequisition(int $requisitionId, string $panel = 'admin'): string
    {
        return static::getUrl(panel: $panel) . '?id=' . $requisitionId;
    }

    public function updatedSelectedItemId(): void
    {
        $item = $this->selectedItem();

        $this->selectedUnitId = $item ? (string) $item->inventory_unit_id : '';
    }

    public function addSelectedItem(): void
    {
        if (! $this->ensurePending()) {
            return;
        }

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
        if (! $this->ensurePending()) {
            return;
        }

        unset($this->selectedItems[$index]);

        $this->selectedItems = array_values($this->selectedItems);
    }

    public function refreshSelectedItemRow(int $index): void
    {
        if (! $this->ensurePending()) {
            return;
        }

        if (! isset($this->selectedItems[$index])) {
            return;
        }

        $item = InventoryItem::query()
            ->with('unit')
            ->find($this->selectedItems[$index]['inventory_item_id'] ?? null);

        if (! $item) {
            return;
        }

        $this->selectedItems[$index]['item_name'] = $item->name;
        $this->selectedItems[$index]['inventory_unit_id'] = $item->inventory_unit_id;
        $this->selectedItems[$index]['unit_name'] = $item->unit?->name ?? '';
    }

    public function refreshSelectedUnitRow(int $index): void
    {
        if (! $this->ensurePending()) {
            return;
        }

        if (! isset($this->selectedItems[$index])) {
            return;
        }

        $unit = InventoryUnit::query()->find($this->selectedItems[$index]['inventory_unit_id'] ?? null);

        if (! $unit) {
            return;
        }

        $this->selectedItems[$index]['unit_name'] = $unit->name;
    }

    public function updateRequisition(): mixed
    {
        if (! $this->ensurePending()) {
            return null;
        }

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
            $this->requisition->update([
                'requisition_date' => $this->requisitionDate,
                'requester_name' => $this->requesterName,
                'department' => $this->department ?: null,
                'purpose' => $this->purpose ?: null,
            ]);

            $this->requisition->items()->delete();

            foreach ($this->selectedItems as $selectedItem) {
                $this->requisition->items()->create([
                    'inventory_item_id' => $selectedItem['inventory_item_id'],
                    'inventory_unit_id' => $selectedItem['inventory_unit_id'],
                    'quantity' => (float) $selectedItem['quantity'],
                    'notes' => $selectedItem['notes'] ?: null,
                ]);
            }
        });

        session()->flash('success', __('Requisition updated successfully.'));

        return redirect()->to(AllRequisitions::getUrl(panel: 'admin'));
    }

    public function getItems(): Collection
    {
        $selectedItemIds = collect($this->selectedItems)
            ->pluck('inventory_item_id')
            ->map(fn ($itemId): int => (int) $itemId)
            ->all();
        $items = InventoryItem::query()
            ->with('unit')
            ->where(function ($query) use ($selectedItemIds): void {
                $query
                    ->where('is_active', true)
                    ->orWhereIn('id', $selectedItemIds);
            })
            ->orderBy('name')
            ->get();

        $availableQuantities = app(InventoryStockService::class)
            ->availableQuantities($items->pluck('id')->all());

        return $items
            ->filter(fn (InventoryItem $item): bool => (float) ($availableQuantities[$item->id] ?? 0) > 0 || in_array($item->id, $selectedItemIds, true))
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

    private function ensurePending(): bool
    {
        if ($this->requisition->refresh()->status === InventoryRequisition::STATUS_PENDING) {
            return true;
        }

        session()->flash('error', __(self::FINALIZED_MESSAGE));
        $this->redirect(AllRequisitions::getUrl(panel: 'admin'), navigate: true);

        return false;
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

    private function numberForInput(float $value): string
    {
        return rtrim(rtrim(number_format($value, 2, '.', ''), '0'), '.');
    }
}
