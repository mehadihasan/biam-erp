<?php

namespace App\Filament\Pages\Inventory\Requisitions;

use App\Filament\Pages\Inventory\BaseInventoryPage;
use App\Models\InventoryRequisition;
use App\Services\InventoryStockService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Livewire\WithPagination;

class AllRequisitions extends BaseInventoryPage
{
    use WithPagination;

    private const FINALIZED_MESSAGE = 'This requisition has already been finalized and cannot be modified.';

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static string | \UnitEnum | null $navigationGroup = 'Requisitions';

    protected static ?string $title = 'Requisitions';

    protected static ?string $navigationLabel = 'All Requisitions';

    protected static ?string $slug = 'inventory/requisitions';

    protected static ?int $navigationSort = 61;

    protected string $view = 'filament.pages.inventory.requisitions.index';

    public string $search = '';

    public string $status = '';

    public static function getNavigationUrl(): string
    {
        return static::getUrl(panel: 'admin');
    }

    public function updated(string $property): void
    {
        if (in_array($property, ['search', 'status'], true)) {
            $this->resetPage();
        }
    }

    public function approve(int $requisitionId): void
    {
        $requisition = InventoryRequisition::query()
            ->with('items.item')
            ->findOrFail($requisitionId);

        if ($requisition->status !== InventoryRequisition::STATUS_PENDING) {
            $this->showFinalizedError();

            return;
        }

        foreach ($requisition->items as $item) {
            $availableQuantity = app(InventoryStockService::class)->availableQuantity($item->inventory_item_id);

            if ((float) $item->quantity > $availableQuantity) {
                $message = __('Cannot approve :ref. :item requested quantity (:requested) exceeds available stock (:available).', [
                    'ref' => $requisition->ref_no,
                    'item' => $item->item?->name ?? __('Selected item'),
                    'requested' => $this->formatNumber((float) $item->quantity),
                    'available' => $this->formatNumber($availableQuantity),
                ]);

                $this->addError('approval', $message);
                session()->flash('error', $message);

                return;
            }
        }

        $requisition->update([
            'status' => InventoryRequisition::STATUS_APPROVED,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'rejected_by' => null,
            'rejected_at' => null,
        ]);

        session()->flash('success', __('Requisition approved successfully.'));
    }

    public function reject(int $requisitionId): void
    {
        $requisition = InventoryRequisition::query()->findOrFail($requisitionId);

        if ($requisition->status !== InventoryRequisition::STATUS_PENDING) {
            $this->showFinalizedError();

            return;
        }

        $requisition->update([
            'status' => InventoryRequisition::STATUS_REJECTED,
            'rejected_by' => auth()->id(),
            'rejected_at' => now(),
            'approved_by' => null,
            'approved_at' => null,
        ]);

        session()->flash('success', __('Requisition rejected successfully.'));
    }

    public function getRequisitions(): LengthAwarePaginator
    {
        return InventoryRequisition::query()
            ->withCount('items')
            ->withSum('items', 'quantity')
            ->when(trim($this->search) !== '', function (Builder $query): void {
                $search = '%' . trim($this->search) . '%';

                $query->where(function (Builder $query) use ($search): void {
                    $query
                        ->where('ref_no', 'like', $search)
                        ->orWhere('requester_name', 'like', $search)
                        ->orWhere('department', 'like', $search);
                });
            })
            ->when($this->status !== '', fn (Builder $query) => $query->where('status', $this->status))
            ->latest()
            ->paginate(10);
    }

    public function formatNumber(float $value): string
    {
        return rtrim(rtrim(number_format($value, 2, '.', ''), '0'), '.');
    }

    private function showFinalizedError(): void
    {
        $message = __(self::FINALIZED_MESSAGE);

        $this->addError('approval', $message);
        session()->flash('error', $message);
    }
}
