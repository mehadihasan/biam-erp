<?php

namespace App\Filament\Pages\Hostel\MealOrders;

use App\Filament\Pages\Hostel\BaseHostelPage;
use App\Models\MealOrder;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class NewMealOrder extends BaseHostelPage
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-plus';

    protected static string|\UnitEnum|null $navigationGroup = 'Meal Order';

    protected static ?string $title = 'New Meal Order';

    protected static ?string $navigationLabel = 'New Meal Order';

    protected static ?string $slug = 'hostel/meal-orders/new';

    protected static ?int $navigationSort = 42;

    protected string $view = 'filament.pages.hostel.meal-orders.new-meal-order';

    public Collection $guests;

    public ?int $guestId = null;

    public ?string $date = null;

    /**
     * @var list<string>
     */
    public array $mealTypes = ['lunch'];

    /**
     * @var array<string, int|string|null>
     */
    public array $mealQuantities = [
        'breakfast' => 1,
        'lunch' => 1,
        'dinner' => 1,
    ];

    public function mount(): void
    {
        $this->date = $this->tomorrowDate();
        $this->loadGuests();
    }

    public function loadGuests(): void
    {
        $this->guests = User::query()
            ->with(['designation', 'activeBooking.room'])
            ->whereHas('bookings', function ($query): void {
                $query->whereIn('status', ['checked_in', 'active'])
                    ->whereNull('checked_out_at');
            })
            ->orderBy('name')
            ->get();
    }

    public function save(): void
    {
        $selectedMealTypes = array_values(array_unique(array_intersect($this->mealTypes, [
            'breakfast',
            'lunch',
            'dinner',
        ])));

        $validated = $this->validate([
            'guestId' => [
                'required',
                Rule::exists('users', 'id')->where(fn ($query) => $query->whereExists(function ($subQuery): void {
                    $subQuery->selectRaw('1')
                        ->from('bookings')
                        ->whereColumn('bookings.user_id', 'users.id')
                        ->whereIn('bookings.status', ['checked_in', 'active'])
                        ->whereNull('bookings.checked_out_at');
                })),
            ],
            'date' => ['required', 'date', 'after_or_equal:'.$this->tomorrowDate()],
            'mealTypes' => ['required', 'array', 'min:1'],
            'mealTypes.*' => ['required', Rule::in(['breakfast', 'lunch', 'dinner'])],
            'mealQuantities' => ['required', 'array'],
            'mealQuantities.breakfast' => [in_array('breakfast', $selectedMealTypes, true) ? 'required' : 'nullable', 'integer', 'min:1', 'max:100'],
            'mealQuantities.lunch' => [in_array('lunch', $selectedMealTypes, true) ? 'required' : 'nullable', 'integer', 'min:1', 'max:100'],
            'mealQuantities.dinner' => [in_array('dinner', $selectedMealTypes, true) ? 'required' : 'nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $guest = User::query()->findOrFail($validated['guestId']);

        foreach (array_values(array_unique($validated['mealTypes'])) as $mealType) {
            $ref = $this->uniqueReference();
            $quantity = (int) ($validated['mealQuantities'][$mealType] ?? 1);

            MealOrder::query()->create([
                'ref' => $ref,
                'reference' => $ref,
                'guest_id' => $guest->id,
                'cadre_reference' => $guest->cadre_number ?: (string) $guest->id,
                'order_date' => $validated['date'],
                'meal_type' => $mealType,
                'menu_item_id' => null,
                'menu_item' => Str::headline($mealType),
                'quantity' => $quantity,
                'unit_price' => 0,
                'total_price' => 0,
                'total' => 0,
                'coupon_code' => $this->uniqueCouponCode(),
                'status' => 'pending',
            ]);
        }

        Notification::make()
            ->title('Meal order created successfully')
            ->success()
            ->send();

        $this->redirect(TodayMealOrders::getUrl(panel: 'admin'));
    }

    private function uniqueReference(): string
    {
        do {
            $ref = 'MO-'.random_int(10000, 99999);
        } while (MealOrder::query()->where('ref', $ref)->orWhere('reference', $ref)->exists());

        return $ref;
    }

    public function tomorrowDate(): string
    {
        return Carbon::tomorrow()->toDateString();
    }

    private function uniqueCouponCode(): string
    {
        do {
            $coupon = 'CPN-'.Str::upper(Str::random(6));
        } while (MealOrder::query()->where('coupon_code', $coupon)->exists());

        return $coupon;
    }
}
