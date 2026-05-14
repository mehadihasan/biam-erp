<?php

namespace App\Filament\Pages\Hostel\MealOrders;

use App\Filament\Pages\Hostel\BaseHostelPage;
use App\Models\MealOrder;
use Illuminate\Support\Collection;

class TodayMealOrders extends BaseHostelPage
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static string|\UnitEnum|null $navigationGroup = 'Meal Order';

    protected static ?string $title = "Today's Orders";

    protected static ?string $navigationLabel = "Today's Orders";

    protected static ?string $slug = 'hostel/meal-orders/today';

    protected static ?int $navigationSort = 41;

    protected string $view = 'filament.pages.hostel.meal-orders.today-meal-orders';

    public function todayDate(): string
    {
        return now()->toDateString();
    }

    public function cards(): Collection
    {
        $orders = MealOrder::query()
            ->with(['guest', 'menuItem'])
            ->whereDate('order_date', now()->toDateString())
            ->get()
            ->groupBy('meal_type');

        return collect([
            'breakfast' => ['label' => 'Breakfast', 'time' => '7:30-8:30 AM'],
            'lunch' => ['label' => 'Lunch', 'time' => '1:30-2:30 PM'],
            'supper' => ['label' => 'Supper', 'time' => '8:30-9:30 PM'],
        ])->map(function (array $meta, string $mealType) use ($orders): array {
            $mealOrders = $orders->get($mealType, collect());

            return [
                ...$meta,
                'meal_type' => $mealType,
                'count' => $mealOrders->count(),
                'orders' => $mealOrders,
            ];
        });
    }
}
