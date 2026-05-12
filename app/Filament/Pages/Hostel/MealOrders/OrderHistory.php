<?php

namespace App\Filament\Pages\Hostel\MealOrders;

use App\Filament\Pages\Hostel\BaseHostelPage;
use App\Models\MealOrder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Livewire\WithPagination;

class OrderHistory extends BaseHostelPage
{
    use WithPagination;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-clock';

    protected static string | \UnitEnum | null $navigationGroup = 'Meal Order';

    protected static ?string $title = 'Order History';

    protected static ?string $navigationLabel = 'Order History';

    protected static ?string $slug = 'hostel/meal-orders/history';

    protected static ?int $navigationSort = 44;

    protected string $view = 'filament.pages.hostel.meal-orders.order-history';

    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function getOrders(): LengthAwarePaginator
    {
        return MealOrder::query()
            ->with(['guest.activeBooking.room', 'menuItem'])
            ->when(trim($this->search) !== '', function ($query): void {
                $search = '%' . trim($this->search) . '%';

                $query->where(function ($subQuery) use ($search): void {
                    $subQuery
                        ->where('coupon_code', 'like', $search)
                        ->orWhereHas('guest', fn ($guestQuery) => $guestQuery->where('name', 'like', $search));
                });
            })
            ->latest()
            ->paginate(10);
    }
}
