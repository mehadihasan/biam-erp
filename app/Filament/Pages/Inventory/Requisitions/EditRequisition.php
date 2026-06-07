<?php

namespace App\Filament\Pages\Inventory\Requisitions;

use App\Filament\Pages\Inventory\BaseInventoryPage;
use App\Models\InventoryItem;
use App\Models\InventoryRequisition;
use App\Models\InventoryUnit;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EditRequisition extends BaseInventoryPage
{
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

                $this->selectedItems[$index]['inventory_unit_id'] = $unit->id;
                $this->selectedItems[$index]['unit_name'] = $unit->name;
                $this->selectedItems[$index]['quantity'] = $this->numberForInput($quantity);
                $this->selectedItems[$index]['notes'] = trim($this->notes) !== '' ? trim($this->notes) : $selectedItem['notes'];

                $this->resetSelectionRow();

                return;
            }
        }

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

    public function refreshSelectedItemRow(int $index): void
    {
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

    public function selectedItem(): ?InventoryItem
    {
        if ($this->selectedItemId === '') {
            return null;
        }

        return InventoryItem::query()->with('unit')->find($this->selectedItemId);
    }

    private function resetSelectionRow(): void
    {
        $this->selectedItemId = '';
        $this->selectedUnitId = '';
        $this->quantity = '1';
        $this->notes = '';
        $this->resetValidation(['selectedItemId', 'selectedUnitId', 'quantity', 'notes']);
    }

    private function numberForInput(float $value): string
    {
        return rtrim(rtrim(number_format($value, 2, '.', ''), '0'), '.');
    }
}
