<?php

namespace App\Filament\Pages\Inventory\Settings;

use App\Filament\Pages\Inventory\BaseInventoryPage;
use App\Models\InventorySupplier;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Livewire\WithPagination;

class Suppliers extends BaseInventoryPage
{
    use WithPagination;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-truck';

    protected static string | \UnitEnum | null $navigationGroup = 'Settings';

    protected static ?string $title = 'Suppliers';

    protected static ?string $navigationLabel = 'Suppliers';

    protected static ?string $slug = 'inventory/settings/suppliers';

    protected static ?int $navigationSort = 93;

    protected string $view = 'filament.pages.inventory.settings.suppliers';

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

    public function getSuppliers(): LengthAwarePaginator
    {
        return InventorySupplier::query()
            ->with('category')
            ->when(trim($this->search) !== '', function (Builder $query): void {
                $search = '%' . trim($this->search) . '%';

                $query->where(function (Builder $query) use ($search): void {
                    $query
                        ->where('company_name', 'like', $search)
                        ->orWhere('contact_person', 'like', $search)
                        ->orWhere('phone', 'like', $search)
                        ->orWhere('email', 'like', $search)
                        ->orWhereHas('category', fn (Builder $query) => $query->where('name', 'like', $search));
                });
            })
            ->when($this->status !== '', fn (Builder $query) => $query->where('is_active', $this->status === '1'))
            ->latest()
            ->paginate(10);
    }
}
