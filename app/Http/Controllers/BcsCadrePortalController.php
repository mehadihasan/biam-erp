<?php

namespace App\Http\Controllers;

use App\Http\Requests\FeedbackRequest;
use App\Http\Requests\MealOrderRequest;
use App\Models\Booking;
use App\Models\Feedback;
use App\Models\MealOrder;
use App\Models\MenuItem;
use App\Models\Room;
use App\Models\User;
use App\Services\RoomAvailabilityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BcsCadrePortalController extends Controller
{
    public function __construct(private readonly RoomAvailabilityService $roomAvailability) {}

    public function booking(Request $request): RedirectResponse|View
    {
        if ($redirect = $this->guard($request)) {
            return $redirect;
        }

        return view('bcs-cadre.booking', [
            'activeMenu' => 'booking',
            'rooms' => $this->availableRooms($request),
            'roomTypes' => $this->roomTypeOptions(),
            'filters' => [
                'check_in' => $this->queryString($request, 'check_in'),
                'check_out' => $this->queryString($request, 'check_out'),
                'room_type' => $this->queryString($request, 'room_type'),
                'adults' => $request->integer('adults') ?: 2,
            ],
        ]);
    }

    public function newBooking(Request $request): RedirectResponse|View
    {
        if ($redirect = $this->guard($request)) {
            return $redirect;
        }

        $room = Room::query()
            ->where('status', '!=', 'maintenance')
            ->findOrFail($request->integer('room'));

        $cadreUser = $this->currentCadreUser($request);
        abort_if(! $cadreUser, 403, 'Authenticated cadre user was not found.');

        return view('bcs-cadre.booking-create', [
            'activeMenu' => 'booking',
            'room' => $room,
            'cadreUser' => $cadreUser,
            'checkInDate' => old('check_in_date', $this->queryString($request, 'check_in')),
            'checkOutDate' => old('check_out_date', $this->queryString($request, 'check_out')),
            'numberOfRooms' => (int) old('number_of_rooms', $request->integer('rooms') ?: 1),
            'adults' => (int) old('adults', $request->integer('adults') ?: 2),
            'notes' => old('notes'),
            'successReference' => session('booking_success_reference'),
        ]);
    }

    public function room(Request $request, Room $room): RedirectResponse|View
    {
        if ($redirect = $this->guard($request)) {
            return $redirect;
        }

        abort_if($room->status === 'maintenance', 404);

        return view('bcs-cadre.room-detail', [
            'activeMenu' => 'booking',
            'room' => $room,
            'checkInDate' => $this->queryString($request, 'check_in') ?: now()->toDateString(),
            'checkOutDate' => $this->queryString($request, 'check_out') ?: now()->addDay()->toDateString(),
            'numberOfRooms' => $request->integer('rooms') ?: 1,
            'adults' => $request->integer('adults') ?: 2,
            'roomType' => $this->queryString($request, 'room_type'),
            'successReference' => session('booking_success_reference'),
        ]);
    }

    public function storeBooking(Request $request): RedirectResponse
    {
        if ($redirect = $this->guard($request)) {
            return $redirect;
        }

        $cadreUser = $this->currentCadreUser($request);
        abort_if(! $cadreUser, 403, 'Authenticated cadre user was not found.');

        $validated = $request->validate([
            'room_id' => [
                'required',
                Rule::exists('rooms', 'id')->where(fn ($query) => $query->where('status', '!=', 'maintenance')),
            ],
            'check_in_date' => ['required', 'date'],
            'check_out_date' => ['required', 'date', 'after:check_in_date'],
            'number_of_rooms' => ['required', 'integer', 'min:1', 'max:100'],
            'adults' => ['required', 'integer', 'min:1', 'max:20'],
            'notes' => ['nullable', 'string'],
        ]);

        $room = Room::query()->findOrFail($validated['room_id']);

        if (! $this->roomAvailability->roomIsAvailable($room, $validated['check_in_date'], $validated['check_out_date'])) {
            return back()
                ->withErrors(['check_in_date' => __('This room is not available for the selected dates.')])
                ->withInput();
        }

        $calculation = $this->calculateRent(
            $room,
            $validated['check_in_date'],
            $validated['check_out_date'],
            (int) $validated['number_of_rooms'],
        );

        $booking = Booking::query()->create([
            'user_id' => $cadreUser->id,
            'room_id' => $room->id,
            'room_type' => $room->room_type,
            'number_of_rooms' => $validated['number_of_rooms'],
            'check_in_date' => $validated['check_in_date'],
            'check_out_date' => $validated['check_out_date'],
            'notes' => $validated['notes'] ?? null,
            'duration_nights' => $calculation['duration_nights'],
            'rent_multiplier' => $calculation['rent_multiplier'],
            'base_rate' => $calculation['base_rate'],
            'calculated_rent' => $calculation['calculated_rent'],
            'booking_money' => $calculation['booking_money'],
            'total_rent' => $calculation['total_rent'],
            'status' => 'pending',
        ]);

        $this->roomAvailability->blockBooking($booking);

        $successReference = 'BKG-'.str_pad((string) $booking->id, 4, '0', STR_PAD_LEFT);

        if ($request->input('return_to') === 'room_detail') {
            return redirect()
                ->route('cadre.rooms.show', [
                    'room' => $room->id,
                    'check_in' => $validated['check_in_date'],
                    'check_out' => $validated['check_out_date'],
                    'adults' => $validated['adults'],
                ])
                ->with('booking_success_reference', $successReference);
        }

        return redirect()
            ->route('cadre.bookings.new', [
                'room' => $room->id,
                'check_in' => $validated['check_in_date'],
                'check_out' => $validated['check_out_date'],
                'adults' => $validated['adults'],
                'rooms' => $validated['number_of_rooms'],
            ])
            ->with('booking_success_reference', $successReference);
    }

    public function mealOrder(Request $request): RedirectResponse|View
    {
        if ($redirect = $this->guard($request)) {
            return $redirect;
        }

        return view('bcs-cadre.meal-order', [
            'activeMenu' => 'meal',
            'orders' => MealOrder::query()
                ->where('cadre_reference', $this->currentCadreReference($request))
                ->latest('order_date')
                ->latest()
                ->get(),
            'editingOrder' => $this->editableMealOrder($request),
        ]);
    }

    public function storeMealOrder(MealOrderRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $cadreUser = $this->currentCadreUser($request);
        abort_if(! $cadreUser, 403, 'Authenticated cadre user was not found.');

        foreach (array_values(array_unique($validated['meal_types'])) as $mealType) {
            $unitPrice = $this->mealTypeUnitPrice($mealType);
            $reference = $this->uniqueMealReference();

            MealOrder::query()->create([
                'order_date' => $validated['order_date'],
                'meal_type' => $mealType,
                'menu_item' => $this->mealTypeLabel($mealType),
                'quantity' => $validated['quantity'],
                'cadre_reference' => $this->currentCadreReference($request),
                'guest_id' => $cadreUser->id,
                'ref' => $reference,
                'reference' => $reference,
                'menu_item_id' => null,
                'unit_price' => $unitPrice,
                'total_price' => $unitPrice * (int) $validated['quantity'],
                'total' => $unitPrice * (int) $validated['quantity'],
                'status' => 'pending',
            ]);
        }

        return redirect()->route('cadre.meals');
    }

    public function updateMealOrder(MealOrderRequest $request, MealOrder $mealOrder): RedirectResponse
    {
        $this->abortIfDifferentCadre($mealOrder->cadre_reference);

        $validated = $request->validated();
        $mealTypes = array_values(array_unique($validated['meal_types']));
        $mealType = $mealTypes[0];
        $unitPrice = $this->mealTypeUnitPrice($mealType);

        $mealOrder->update([
            'order_date' => $validated['order_date'],
            'meal_type' => $mealType,
            'menu_item' => $this->mealTypeLabel($mealType),
            'quantity' => $validated['quantity'],
            'menu_item_id' => null,
            'unit_price' => $unitPrice,
            'total_price' => $unitPrice * (int) $validated['quantity'],
            'total' => $unitPrice * (int) $validated['quantity'],
        ]);

        foreach (array_slice($mealTypes, 1) as $additionalMealType) {
            $additionalUnitPrice = $this->mealTypeUnitPrice($additionalMealType);
            $reference = $this->uniqueMealReference();
            $cadreUser = $this->currentCadreUser($request);

            MealOrder::query()->create([
                'order_date' => $validated['order_date'],
                'meal_type' => $additionalMealType,
                'menu_item' => $this->mealTypeLabel($additionalMealType),
                'quantity' => $validated['quantity'],
                'cadre_reference' => $this->currentCadreReference($request),
                'guest_id' => $mealOrder->guest_id ?: $cadreUser?->id,
                'ref' => $reference,
                'reference' => $reference,
                'menu_item_id' => null,
                'unit_price' => $additionalUnitPrice,
                'total_price' => $additionalUnitPrice * (int) $validated['quantity'],
                'total' => $additionalUnitPrice * (int) $validated['quantity'],
                'status' => $mealOrder->status,
            ]);
        }

        return redirect()->route('cadre.meals');
    }

    public function destroyMealOrder(MealOrder $mealOrder): RedirectResponse
    {
        $this->abortIfDifferentCadre($mealOrder->cadre_reference);

        $mealOrder->delete();

        return redirect()->route('cadre.meals');
    }

    public function feedback(Request $request): RedirectResponse|View
    {
        if ($redirect = $this->guard($request)) {
            return $redirect;
        }

        return view('bcs-cadre.feedback', [
            'activeMenu' => 'feedback',
            'feedbackCategories' => Feedback::CATEGORIES,
            'feedbackRatings' => Feedback::RATINGS,
            'feedbackItems' => Feedback::query()
                ->with('ratings')
                ->where('cadre_reference', $this->currentCadreReference($request))
                ->latest()
                ->get(),
            'editingFeedback' => $this->editableFeedback($request),
        ]);
    }

    public function storeFeedback(FeedbackRequest $request): RedirectResponse
    {
        $cadreUser = $this->currentCadreUser($request);
        abort_if(! $cadreUser, 403, 'Authenticated cadre user was not found.');

        $feedback = Feedback::query()->create([
            'guest_id' => $cadreUser->id,
            'cadre_reference' => $this->currentCadreReference($request),
            'submitter_type' => 'cadre',
            'options' => [],
            'status' => 'submitted',
        ]);

        $this->syncFeedbackRatings($feedback, $request->validated('ratings'));

        return redirect()->route('cadre.feedback');
    }

    public function updateFeedback(FeedbackRequest $request, Feedback $feedback): RedirectResponse
    {
        $this->abortIfDifferentCadre($feedback->cadre_reference);

        $this->syncFeedbackRatings($feedback, $request->validated('ratings'));

        return redirect()->route('cadre.feedback');
    }

    public function destroyFeedback(Feedback $feedback): RedirectResponse
    {
        $this->abortIfDifferentCadre($feedback->cadre_reference);

        $feedback->delete();

        return redirect()->route('cadre.feedback');
    }

    public function billing(Request $request): RedirectResponse|View
    {
        if ($redirect = $this->guard($request)) {
            return $redirect;
        }

        $charges = Room::query()
            ->orderBy('floor')
            ->orderBy('room_number')
            ->get()
            ->map(function (Room $room, int $index): array {
                $days = ($index % 3) + 1;

                return [
                    'reference' => 'BK-'.str_pad((string) (83185 - ($index * 1413)), 5, '0', STR_PAD_LEFT),
                    'check_in' => now()->subDays($index + 2)->toDateString(),
                    'check_out' => now()->subDays($index + 2)->addDays($days)->toDateString(),
                    'days' => $days,
                    'total' => (int) $room->base_rate * $days,
                    'status' => $index % 4 === 0 ? 'confirmed' : 'pending',
                ];
            });

        return view('bcs-cadre.billing', [
            'activeMenu' => 'billing',
            'charges' => $charges,
            'totalBookings' => $charges->count(),
            'outstanding' => $charges->where('status', 'pending')->sum('total'),
            'paid' => $charges->where('status', 'confirmed')->sum('total'),
        ]);
    }

    private function guard(Request $request): ?RedirectResponse
    {
        if ($request->session()->get('cadre_auth') !== true) {
            return redirect()->route('home');
        }

        return null;
    }

    private function currentCadreReference(Request $request): string
    {
        $reference = $request->session()->get('cadre_reference');

        return is_string($reference) && $reference !== ''
            ? $reference
            : BcsCadreAuthController::DEMO_CADRE_REFERENCE;
    }

    private function currentCadreUser(Request $request): ?User
    {
        $cadreReference = $this->currentCadreReference($request);

        $user = User::query()
            ->where('cadre_number', $cadreReference)
            ->first();

        if ($user || $cadreReference !== BcsCadreAuthController::DEMO_CADRE_REFERENCE) {
            return $user;
        }

        return User::query()
            ->where('email', 'test@example.com')
            ->orWhereNotNull('cadre_number')
            ->orWhere('role', 'guest')
            ->oldest()
            ->first();
    }

    private function mealTypeUnitPrice(string $mealType): float
    {
        $price = MenuItem::query()
            ->where('meal_type', $mealType)
            ->where('is_active', true)
            ->orderBy('id')
            ->value('price_bcs');

        if ($price !== null) {
            return (float) $price;
        }

        return match ($mealType) {
            'breakfast' => 50,
            'lunch' => 100,
            'supper' => 70,
            default => 0,
        };
    }

    private function mealTypeLabel(string $mealType): string
    {
        return match ($mealType) {
            'breakfast' => 'Breakfast',
            'lunch' => 'Lunch',
            'supper' => 'Supper',
            default => Str::headline($mealType),
        };
    }

    private function availableRooms(Request $request)
    {
        $checkIn = $this->queryString($request, 'check_in');
        $checkOut = $this->queryString($request, 'check_out');
        $roomType = $this->queryString($request, 'room_type');
        $adults = $request->integer('adults') ?: null;

        return $this->roomAvailability
            ->availableRoomQuery($checkIn, $checkOut, $roomType, $adults)
            ->orderBy('floor')
            ->orderBy('room_number')
            ->get();
    }

    private function roomTypeOptions(): array
    {
        return Room::query()
            ->whereNotNull('room_type')
            ->where('room_type', '!=', '')
            ->distinct()
            ->orderBy('room_type')
            ->pluck('room_type')
            ->mapWithKeys(fn (string $roomType): array => [
                $roomType => match ($roomType) {
                    'vip' => 'VIP',
                    'ac' => 'AC',
                    'non_ac' => 'Non-AC',
                    default => Str::headline(str_replace('_', ' ', $roomType)),
                },
            ])
            ->all();
    }

    private function queryString(Request $request, string $key): ?string
    {
        $value = $request->query($key);

        return is_string($value) && $value !== '' ? $value : null;
    }

    private function calculateRent(Room $room, string $checkInDate, string $checkOutDate, int $numberOfRooms): array
    {
        $checkIn = Carbon::parse($checkInDate)->startOfDay();
        $checkOut = Carbon::parse($checkOutDate)->startOfDay();
        $duration = (int) $checkIn->diffInDays($checkOut, false);

        if ($duration < 1) {
            throw new \InvalidArgumentException('Check-out date must be after check-in date.');
        }

        $multiplier = match (true) {
            $duration <= 3 => 1,
            $duration <= 7 => 2,
            default => 3,
        };

        $baseRate = (float) $room->base_rate;
        $calculatedRent = $baseRate * $duration * $multiplier * $numberOfRooms;
        $bookingMoney = $calculatedRent * 0.20;

        return [
            'duration_nights' => $duration,
            'rent_multiplier' => $multiplier,
            'base_rate' => $baseRate,
            'calculated_rent' => $calculatedRent,
            'booking_money' => $bookingMoney,
            'total_rent' => $calculatedRent,
        ];
    }

    private function editableMealOrder(Request $request): ?MealOrder
    {
        $id = $request->integer('edit');
        if ($id === 0) {
            return null;
        }

        return MealOrder::query()
            ->where('cadre_reference', $this->currentCadreReference($request))
            ->find($id);
    }

    private function editableFeedback(Request $request): ?Feedback
    {
        $id = $request->integer('edit');
        if ($id === 0) {
            return null;
        }

        return Feedback::query()
            ->with('ratings')
            ->where('cadre_reference', $this->currentCadreReference($request))
            ->find($id);
    }

    private function syncFeedbackRatings(Feedback $feedback, array $ratings): void
    {
        foreach (Feedback::CATEGORIES as $category) {
            $feedback->ratings()->updateOrCreate(
                ['category' => $category],
                ['rating' => $ratings[$category]],
            );
        }
    }

    private function uniqueMealReference(): string
    {
        do {
            $reference = 'MO-'.Str::upper(Str::random(5));
        } while (MealOrder::query()->where('reference', $reference)->exists());

        return $reference;
    }

    private function abortIfDifferentCadre(string $cadreReference): void
    {
        $currentReference = session('cadre_reference', BcsCadreAuthController::DEMO_CADRE_REFERENCE);

        abort_if($cadreReference !== $currentReference, 404);
    }
}
