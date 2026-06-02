<?php

namespace App\Filament\Pages\Inventory\Items;

use App\Filament\Pages\Inventory\BaseInventoryPage;
use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Livewire\WithPagination;

class AllItems extends BaseInventoryPage
{
    use WithPagination;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-list-bullet';

    protected static string | \UnitEnum | null $navigationGroup = 'Items';

    protected static ?string $title = 'All Items';

    protected static ?string $navigationLabel = 'All Items';

    protected static ?string $slug = 'inventory/items';

    protected static ?int $navigationSort = 81;

    protected string $view = 'filament.pages.inventory.items.index';

    public string $search = '';

    public string $category = '';

    public string $status = '';

    public string $stockLevel = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'category' => ['except' => ''],
        'status' => ['except' => ''],
        'stockLevel' => ['as' => 'stock_level', 'except' => ''],
    ];

    public static function getNavigationUrl(): string
    {
        return static::getUrl(panel: 'admin');
    }

    public function updated(string $property): void
    {
        if (in_array($property, ['search', 'category', 'status', 'stockLevel'], true)) {
            $this->resetPage();
        }
    }

    public function getItems(): LengthAwarePaginator
    {
        return InventoryItem::query()
            ->with(['category', 'unit'])
            ->when(trim($this->search) !== '', fn (Builder $query) => $this->applySearch($query, $this->search))
            ->when($this->category !== '', fn (Builder $query) => $query->where('inventory_category_id', $this->category))
            ->when($this->status !== '', fn (Builder $query) => $query->where('is_active', $this->status === '1'))
            ->when($this->stockLevel === 'low', fn (Builder $query) => $query
                ->where('current_stock_quantity', '>', 0)
                ->whereColumn('current_stock_quantity', '<=', 'min_threshold'))
            ->when($this->stockLevel === 'out', fn (Builder $query) => $query->where('current_stock_quantity', '<=', 0))
            ->latest()
            ->paginate(10)
            ->withQueryString();
    }

    public function getCategories(): Collection
    {
        return InventoryCategory::query()
            ->orderBy('name')
            ->get();
    }

    private function applySearch(Builder $query, string $search): void
    {
        $search = trim($search);

        $query->where(function (Builder $query) use ($search): void {
            $query->where('name', 'like', '%' . $search . '%');

            $this->applyCodeSearch($query, $search);
        });
    }

    private function applyCodeSearch(Builder $query, string $search): void
    {
        $driver = $query->getModel()->getConnection()->getDriverName();
        $like = '%' . $search . '%';

        match ($driver) {
            'mysql', 'mariadb' => $query->orWhereRaw("CONCAT('ITM-', LPAD(UPPER(HEX(id)), 6, '0')) LIKE ?", [$like]),
            'sqlite' => $query->orWhereRaw("'ITM-' || printf('%06X', id) LIKE ?", [$like]),
            'pgsql' => $query->orWhereRaw("'ITM-' || LPAD(UPPER(TO_HEX(id)), 6, '0') LIKE ?", [$like]),
            default => $this->applyFallbackCodeSearch($query, $search),
        };
    }

    private function applyFallbackCodeSearch(Builder $query, string $search): void
    {
        $code = strtoupper(str_replace(['ITM-', 'ITM'], '', $search));

        if ($code !== '' && ctype_xdigit($code)) {
            $query->orWhere('id', hexdec($code));
        }
    }
}
