<?php

namespace App\Filament\Pages\Hostel\MealOrders;

use App\Filament\Pages\Hostel\BaseHostelPage;
use App\Models\Booking;
use App\Models\MealOrder;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
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
            'date' => [
                'required',
                'date',
                Rule::in($this->availableMealOrderDatesForGuest($this->guestId)),
            ],
            'mealTypes' => ['required', 'array', 'min:1'],
            'mealTypes.*' => ['required', Rule::in(['breakfast', 'lunch', 'dinner'])],
            'mealQuantities' => ['required', 'array'],
            'mealQuantities.breakfast' => [in_array('breakfast', $selectedMealTypes, true) ? 'required' : 'nullable', 'integer', 'min:1', 'max:100'],
            'mealQuantities.lunch' => [in_array('lunch', $selectedMealTypes, true) ? 'required' : 'nullable', 'integer', 'min:1', 'max:100'],
            'mealQuantities.dinner' => [in_array('dinner', $selectedMealTypes, true) ? 'required' : 'nullable', 'integer', 'min:1', 'max:100'],
        ], [
            'date.in' => 'Please select a date from the selected guest booking dates.',
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

    public function updatedGuestId(): void
    {
        $availableDates = $this->availableMealOrderDatesForGuest($this->guestId);

        $this->date = $availableDates[0] ?? null;
    }

    /**
     * @return list<string>
     */
    public function selectedMealOrderDates(): array
    {
        return $this->availableMealOrderDatesForGuest($this->guestId);
    }

    private function uniqueReference(): string
    {
        do {
            $ref = 'MO-'.random_int(10000, 99999);
        } while (MealOrder::query()->where('ref', $ref)->orWhere('reference', $ref)->exists());

        return $ref;
    }

    /**
     * @return list<string>
     */
    private function availableMealOrderDatesForGuest(?int $guestId): array
    {
        if (! $guestId) {
            return [];
        }

        $booking = User::query()
            ->with('activeBooking')
            ->find($guestId)
            ?->activeBooking;

        if (! $booking?->check_in_date || ! $booking->check_out_date) {
            return [];
        }

        return $this->mealOrderDatesForBooking($booking);
    }

    /**
     * @return list<string>
     */
    private function mealOrderDatesForBooking(Booking $booking): array
    {
        $date = $booking->check_in_date->greaterThan(now()->startOfDay())
            ? $booking->check_in_date->copy()->startOfDay()
            : now()->startOfDay();
        $lastDate = $booking->check_out_date->copy()->subDay()->startOfDay();
        $dates = [];

        while ($date->lessThanOrEqualTo($lastDate)) {
            $dates[] = $date->toDateString();
            $date->addDay();
        }

        return $dates;
    }

    private function uniqueCouponCode(): string
    {
        do {
            $coupon = 'CPN-'.Str::upper(Str::random(6));
        } while (MealOrder::query()->where('coupon_code', $coupon)->exists());

        return $coupon;
    }
}
