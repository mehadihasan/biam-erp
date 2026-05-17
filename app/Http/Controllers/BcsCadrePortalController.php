<?php

namespace App\Http\Controllers;

use App\Http\Requests\FeedbackRequest;
use App\Http\Requests\MealOrderRequest;
use App\Models\Booking;
use App\Models\Feedback;
use App\Models\MealOrder;
use App\Models\MenuItem;
use App\Models\Payment;
use App\Models\Room;
use App\Models\User;
use App\Services\RoomAvailabilityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
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
            'portalRoutePrefix' => $this->portalRoutePrefix($request),
            'rooms' => $this->availableRooms($request),
            'roomTypes' => $this->roomTypeOptions(),
            'filters' => [
                'check_in' => $this->queryString($request, 'check_in'),
                'check_out' => $this->queryString($request, 'check_out'),
                'room_type' => $this->queryString($request, 'room_type'),
                'adults' => $request->integer('adults') ?: 1,
            ],
        ]);
    }

    public function newBooking(Request $request): RedirectResponse|View
    {
        if ($redirect = $this->guard($request)) {
            return $redirect;
        }

        $portalUser = $this->currentPortalUser($request);
        abort_if(! $portalUser, 403, 'Authenticated portal user was not found.');

        $checkInDate = old('check_in_date', $this->queryString($request, 'check_in'));
        $checkOutDate = old('check_out_date', $this->queryString($request, 'check_out'));
        $selectedRoomId = (int) old('room_id', $request->integer('room'));
        $selectedRoom = $selectedRoomId
            ? Room::query()
                ->where('status', '!=', 'maintenance')
                ->find($selectedRoomId)
            : null;
        $availableRooms = $this->hasValidDateRange($checkInDate, $checkOutDate)
            ? $this->roomAvailability->roomsWithAvailableBeds($checkInDate, $checkOutDate)
            : collect();
        $room = $selectedRoom ?: null;
        $selectedRoomAvailable = $room
            && $this->hasValidDateRange($checkInDate, $checkOutDate)
            && $availableRooms->contains('id', $room->id);
        $selectedRoomUnavailable = $room
            && $this->hasValidDateRange($checkInDate, $checkOutDate)
            && ! $selectedRoomAvailable;
        $availableBedSeatCount = $room && $this->hasValidDateRange($checkInDate, $checkOutDate)
            ? $this->roomAvailability->availableBedSeatCount($room, $checkInDate, $checkOutDate)
            : null;
        $numberOfRooms = max(1, (int) old('number_of_rooms', $request->integer('rooms') ?: 1));
        $duplicateBookingDate = $portalUser
            ? $this->userHasBookingUpdatedToday($portalUser->id)
            : false;

        if (is_int($availableBedSeatCount) && $availableBedSeatCount > 0) {
            $numberOfRooms = min($numberOfRooms, $availableBedSeatCount, 5);
        }

        return view('bcs-cadre.booking-create', [
            'activeMenu' => 'booking',
            'portalRoutePrefix' => $this->portalRoutePrefix($request),
            'room' => $room,
            'availableRooms' => $availableRooms,
            'selectedRoomId' => $room?->id,
            'selectedRoomAvailable' => (bool) $selectedRoomAvailable,
            'selectedRoomUnavailable' => (bool) $selectedRoomUnavailable,
            'availableBedSeatCount' => $availableBedSeatCount,
            'bedSeatOptions' => is_int($availableBedSeatCount) && $availableBedSeatCount > 0 ? range(1, min($availableBedSeatCount, 5)) : [],
            'duplicateBookingDate' => $duplicateBookingDate,
            'cadreUser' => $portalUser,
            'checkInDate' => $checkInDate,
            'checkOutDate' => $checkOutDate,
            'numberOfRooms' => $numberOfRooms,
            'ref' => old('ref'),
            'notes' => old('notes'),
            'paymentGuestName' => old('payment_guest_name', $portalUser->name),
            'paymentAmount' => old('payment_amount'),
            'paymentMethod' => old('payment_method'),
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
            'portalRoutePrefix' => $this->portalRoutePrefix($request),
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

        $portalUser = $this->currentPortalUser($request);
        abort_if(! $portalUser, 403, 'Authenticated portal user was not found.');

        $requiresPayment = $request->input('return_to') !== 'room_detail';

        $validated = $request->validate([
            'room_id' => [
                'required',
                Rule::exists('rooms', 'id')->where(fn ($query) => $query->where('status', '!=', 'maintenance')),
            ],
            'check_in_date' => ['required', 'date'],
            'check_out_date' => ['required', 'date', 'after:check_in_date'],
            'number_of_rooms' => ['required', 'integer', 'min:1', 'max:5'],
            'ref' => ['nullable', 'string', 'max:50', 'unique:bookings,ref'],
            'adults' => ['nullable', 'integer', 'min:1', 'max:20'],
            'notes' => ['nullable', 'string'],
            'payment_guest_name' => [$requiresPayment ? 'required' : 'nullable', 'string', 'max:255'],
            'payment_method' => [$requiresPayment ? 'required' : 'nullable', Rule::in(['Card', 'Mobile Banking'])],
        ]);

        $room = Room::query()->findOrFail($validated['room_id']);

        if ($this->userHasBookingUpdatedToday($portalUser->id)) {
            return back()
                ->withErrors(['booking_quota' => __('You have already filled your quota today. Please try again tomorrow.')])
                ->withInput();
        }

        $availableBedSeatCount = $this->roomAvailability->availableBedSeatCount($room, $validated['check_in_date'], $validated['check_out_date']);

        if ($availableBedSeatCount < 1) {
            return back()
                ->withErrors(['check_in_date' => __('This room is not available for the selected dates.')])
                ->withInput();
        }

        if ((int) $validated['number_of_rooms'] > $availableBedSeatCount) {
            return back()
                ->withErrors(['number_of_rooms' => __('The selected bed/seat count exceeds availability.')])
                ->withInput();
        }

        $calculation = $this->calculateRent(
            $room,
            $validated['check_in_date'],
            $validated['check_out_date'],
            (int) $validated['number_of_rooms'],
        );
        $minimumPayment = round((float) $calculation['total_rent'] * 0.20, 2);

        $payment = $requiresPayment
            ? $request->validate(['payment_amount' => ['required', 'numeric', 'min:'.$minimumPayment]])
            : null;

        $booking = DB::transaction(function () use ($portalUser, $room, $validated, $calculation, $payment, $requiresPayment): Booking {
            $booking = Booking::query()->create([
                'ref' => $validated['ref'] ?? null,
                'user_id' => $portalUser->id,
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

            if ($requiresPayment && is_array($payment)) {
                Payment::query()->create([
                    'ref' => $this->uniquePaymentReference(),
                    'booking_id' => $booking->id,
                    'guest_id' => $booking->user_id,
                    'amount' => $payment['payment_amount'],
                    'type' => 'booking_money',
                    'gateway' => $validated['payment_method'],
                    'transaction_id' => $this->uniquePaymentTransactionId(),
                    'status' => 'success',
                    'paid_at' => now(),
                ]);
            }

            return $booking;
        });

        $successReference = 'BKG-'.str_pad((string) $booking->id, 4, '0', STR_PAD_LEFT);

        if ($request->input('return_to') === 'room_detail') {
            return redirect()
                ->route($this->portalRouteName($request, 'rooms.show'), [
                    'room' => $room->id,
                    'check_in' => $validated['check_in_date'],
                    'check_out' => $validated['check_out_date'],
                    'adults' => $validated['adults'] ?? 1,
                ])
                ->with('booking_success_reference', $successReference);
        }

        return redirect()
            ->route($this->portalRouteName($request, 'bookings.new'), [
                'room' => $room->id,
                'check_in' => $validated['check_in_date'],
                'check_out' => $validated['check_out_date'],
                'rooms' => $validated['number_of_rooms'],
            ])
            ->with('booking_success_reference', $successReference);
    }

    public function mealOrder(Request $request): RedirectResponse|View
    {
        if ($redirect = $this->guard($request)) {
            return $redirect;
        }

        $availableMealOrderDates = $this->availableMealOrderDates($request);

        return view('bcs-cadre.meal-order', [
            'activeMenu' => 'meal',
            'portalRoutePrefix' => $this->portalRoutePrefix($request),
            'availableMealOrderDates' => $availableMealOrderDates,
            'orders' => MealOrder::query()
                ->when(
                    $this->portalIsGuest($request),
                    fn ($query) => $query->where('guest_id', $this->currentPortalUser($request)?->id),
                    fn ($query) => $query->where('cadre_reference', $this->currentCadreReference($request)),
                )
                ->latest('order_date')
                ->latest()
                ->get(),
            'editingOrder' => $this->editableMealOrder($request),
        ]);
    }

    public function storeMealOrder(MealOrderRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $portalUser = $this->currentPortalUser($request);
        abort_if(! $portalUser, 403, 'Authenticated portal user was not found.');

        if (! $this->mealOrderDateIsAvailable($request, $validated['order_date'])) {
            return back()
                ->withErrors(['order_date' => __('Please select a date from your available booking dates.')])
                ->withInput();
        }

        foreach (array_values(array_unique($validated['meal_types'])) as $mealType) {
            $unitPrice = $this->mealTypeUnitPrice($mealType);
            $quantity = (int) $validated['quantities'][$mealType];
            $reference = $this->uniqueMealReference();

            MealOrder::query()->create([
                'order_date' => $validated['order_date'],
                'meal_type' => $mealType,
                'menu_item' => $this->mealTypeLabel($mealType),
                'quantity' => $quantity,
                'cadre_reference' => $this->currentCadreReference($request),
                'guest_id' => $portalUser->id,
                'ref' => $reference,
                'reference' => $reference,
                'menu_item_id' => null,
                'unit_price' => $unitPrice,
                'total_price' => $unitPrice * $quantity,
                'total' => $unitPrice * $quantity,
                'status' => 'pending',
            ]);
        }

        return redirect()->route($this->portalRouteName($request, 'meals'));
    }

    public function updateMealOrder(MealOrderRequest $request, MealOrder $mealOrder): RedirectResponse
    {
        $this->abortIfUnauthorizedRecord($request, $mealOrder);

        $validated = $request->validated();

        if (! $this->mealOrderDateIsAvailable($request, $validated['order_date'])) {
            return back()
                ->withErrors(['order_date' => __('Please select a date from your available booking dates.')])
                ->withInput();
        }

        $mealTypes = array_values(array_unique($validated['meal_types']));
        $mealType = $mealTypes[0];
        $unitPrice = $this->mealTypeUnitPrice($mealType);
        $quantity = (int) $validated['quantities'][$mealType];

        $mealOrder->update([
            'order_date' => $validated['order_date'],
            'meal_type' => $mealType,
            'menu_item' => $this->mealTypeLabel($mealType),
            'quantity' => $quantity,
            'menu_item_id' => null,
            'unit_price' => $unitPrice,
            'total_price' => $unitPrice * $quantity,
            'total' => $unitPrice * $quantity,
        ]);

        foreach (array_slice($mealTypes, 1) as $additionalMealType) {
            $additionalUnitPrice = $this->mealTypeUnitPrice($additionalMealType);
            $additionalQuantity = (int) $validated['quantities'][$additionalMealType];
            $reference = $this->uniqueMealReference();
            $portalUser = $this->currentPortalUser($request);

            MealOrder::query()->create([
                'order_date' => $validated['order_date'],
                'meal_type' => $additionalMealType,
                'menu_item' => $this->mealTypeLabel($additionalMealType),
                'quantity' => $additionalQuantity,
                'cadre_reference' => $this->currentCadreReference($request),
                'guest_id' => $mealOrder->guest_id ?: $portalUser?->id,
                'ref' => $reference,
                'reference' => $reference,
                'menu_item_id' => null,
                'unit_price' => $additionalUnitPrice,
                'total_price' => $additionalUnitPrice * $additionalQuantity,
                'total' => $additionalUnitPrice * $additionalQuantity,
                'status' => $mealOrder->status,
            ]);
        }

        return redirect()->route($this->portalRouteName($request, 'meals'));
    }

    public function destroyMealOrder(Request $request, MealOrder $mealOrder): RedirectResponse
    {
        $this->abortIfUnauthorizedRecord($request, $mealOrder);

        $mealOrder->delete();

        return redirect()->route($this->portalRouteName($request, 'meals'));
    }

    public function feedback(Request $request): RedirectResponse|View
    {
        if ($redirect = $this->guard($request)) {
            return $redirect;
        }

        return view('bcs-cadre.feedback', [
            'activeMenu' => 'feedback',
            'portalRoutePrefix' => $this->portalRoutePrefix($request),
            'feedbackCategories' => Feedback::CATEGORIES,
            'feedbackRatings' => Feedback::RATINGS,
            'feedbackItems' => Feedback::query()
                ->with('ratings')
                ->when(
                    $this->portalIsGuest($request),
                    fn ($query) => $query->where('guest_id', $this->currentPortalUser($request)?->id),
                    fn ($query) => $query->where('cadre_reference', $this->currentCadreReference($request)),
                )
                ->latest()
                ->get(),
            'editingFeedback' => $this->editableFeedback($request),
        ]);
    }

    public function storeFeedback(FeedbackRequest $request): RedirectResponse
    {
        $portalUser = $this->currentPortalUser($request);
        abort_if(! $portalUser, 403, 'Authenticated portal user was not found.');

        $feedback = Feedback::query()->create([
            'guest_id' => $portalUser->id,
            'cadre_reference' => $this->currentCadreReference($request),
            'submitter_type' => $request->session()->get('guest_verified') === true && $request->session()->get('cadre_auth') !== true ? 'guest' : 'cadre',
            'options' => [
                'comments' => $request->validated('comments'),
            ],
            'status' => 'submitted',
        ]);

        $this->syncFeedbackRatings($feedback, $request->validated('ratings'));

        return redirect()->route($this->portalRouteName($request, 'feedback'));
    }

    public function updateFeedback(FeedbackRequest $request, Feedback $feedback): RedirectResponse
    {
        $this->abortIfUnauthorizedRecord($request, $feedback);

        $feedback->update([
            'options' => [
                ...($feedback->options ?? []),
                'comments' => $request->validated('comments'),
            ],
        ]);

        $this->syncFeedbackRatings($feedback, $request->validated('ratings'));

        return redirect()->route($this->portalRouteName($request, 'feedback'));
    }

    public function destroyFeedback(Request $request, Feedback $feedback): RedirectResponse
    {
        $this->abortIfUnauthorizedRecord($request, $feedback);

        $feedback->delete();

        return redirect()->route($this->portalRouteName($request, 'feedback'));
    }

    public function billing(Request $request): RedirectResponse|View
    {
        if ($redirect = $this->guard($request)) {
            return $redirect;
        }

        $portalUser = $this->currentPortalUser($request);
        abort_if(! $portalUser, 403, 'Authenticated portal user was not found.');

        $charges = Booking::query()
            ->with('room')
            ->where('user_id', $portalUser->id)
            ->latest('check_in_date')
            ->get()
            ->map(function (Booking $booking): array {
                return [
                    'reference' => 'BKG-'.str_pad((string) $booking->id, 4, '0', STR_PAD_LEFT),
                    'check_in' => $booking->check_in_date?->toDateString() ?: '-',
                    'check_out' => $booking->check_out_date?->toDateString() ?: '-',
                    'days' => (int) $booking->duration_nights,
                    'total' => (float) $booking->total_rent,
                    'status' => $booking->status,
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
        if (! $this->portalAuthenticated($request)) {
            return redirect()->route('home');
        }

        return null;
    }

    private function portalAuthenticated(Request $request): bool
    {
        return $request->routeIs('guest.*')
            ? $request->session()->get('guest_verified') === true
            : $request->session()->get('cadre_auth') === true;
    }

    private function portalIsGuest(Request $request): bool
    {
        return $request->routeIs('guest.*');
    }

    private function portalRoutePrefix(Request $request): string
    {
        return $this->portalIsGuest($request) ? 'guest' : 'cadre';
    }

    private function portalRouteName(Request $request, string $name): string
    {
        return $this->portalRoutePrefix($request).'.'.$name;
    }

    private function currentCadreReference(Request $request): string
    {
        $reference = $request->session()->get('cadre_reference')
            ?: $request->session()->get('guest_cadre_reference');

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
            ->oldest()
            ->first();
    }

    private function currentPortalUser(Request $request): ?User
    {
        if ($this->portalIsGuest($request)) {
            $guestUserId = $request->session()->get('guest_user_id');

            return is_numeric($guestUserId)
                ? User::query()->where('role', 'guest')->find((int) $guestUserId)
                : null;
        }

        return $this->currentCadreUser($request);
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
            'dinner' => 70,
            default => 0,
        };
    }

    private function mealTypeLabel(string $mealType): string
    {
        return match ($mealType) {
            'breakfast' => 'Breakfast',
            'lunch' => 'Lunch',
            'dinner' => 'Dinner',
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

    private function hasValidDateRange(?string $checkInDate, ?string $checkOutDate): bool
    {
        if (! $checkInDate || ! $checkOutDate) {
            return false;
        }

        try {
            return Carbon::parse($checkOutDate)->startOfDay()
                ->greaterThan(Carbon::parse($checkInDate)->startOfDay());
        } catch (\Throwable) {
            return false;
        }
    }

    private function uniquePaymentReference(): string
    {
        do {
            $ref = 'PAY-'.random_int(10000, 99999);
        } while (Payment::query()->where('ref', $ref)->exists());

        return $ref;
    }

    private function uniquePaymentTransactionId(): string
    {
        do {
            $transactionId = 'TXN-'.random_int(100000, 999999);
        } while (Payment::query()->where('transaction_id', $transactionId)->exists());

        return $transactionId;
    }

    private function editableMealOrder(Request $request): ?MealOrder
    {
        $id = $request->integer('edit');
        if ($id === 0) {
            return null;
        }

        return MealOrder::query()
            ->when(
                $this->portalIsGuest($request),
                fn ($query) => $query->where('guest_id', $this->currentPortalUser($request)?->id),
                fn ($query) => $query->where('cadre_reference', $this->currentCadreReference($request)),
            )
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
            ->when(
                $this->portalIsGuest($request),
                fn ($query) => $query->where('guest_id', $this->currentPortalUser($request)?->id),
                fn ($query) => $query->where('cadre_reference', $this->currentCadreReference($request)),
            )
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

    /**
     * @return list<string>
     */
    private function availableMealOrderDates(Request $request): array
    {
        $portalUser = $this->currentPortalUser($request);

        if (! $portalUser) {
            return [];
        }

        $earliestOrderDate = now()->startOfDay();
        $dates = [];

        Booking::query()
            ->where('user_id', $portalUser->id)
            ->whereIn('status', $this->roomAvailability->blockingBookingStatuses())
            ->whereDate('check_out_date', '>', $earliestOrderDate->toDateString())
            ->orderBy('check_in_date')
            ->get(['check_in_date', 'check_out_date'])
            ->each(function (Booking $booking) use ($earliestOrderDate, &$dates): void {
                if (! $booking->check_in_date || ! $booking->check_out_date) {
                    return;
                }

                $date = $booking->check_in_date->greaterThan($earliestOrderDate)
                    ? $booking->check_in_date->copy()->startOfDay()
                    : $earliestOrderDate->copy();
                $lastDate = $booking->check_out_date->copy()->subDay()->startOfDay();

                while ($date->lessThanOrEqualTo($lastDate)) {
                    $dates[$date->toDateString()] = true;
                    $date->addDay();
                }
            });

        ksort($dates);

        return array_keys($dates);
    }

    private function mealOrderDateIsAvailable(Request $request, string $orderDate): bool
    {
        return in_array($orderDate, $this->availableMealOrderDates($request), true);
    }

    private function userHasBookingUpdatedToday(int $userId): bool
    {
        return Booking::query()
            ->where('user_id', $userId)
            ->whereDate('updated_at', now()->toDateString())
            ->exists();
    }

    private function abortIfUnauthorizedRecord(Request $request, MealOrder|Feedback $record): void
    {
        if ($this->portalIsGuest($request)) {
            abort_if((int) $record->guest_id !== (int) $this->currentPortalUser($request)?->id, 404);

            return;
        }

        abort_if($record->cadre_reference !== $this->currentCadreReference($request), 404);
    }
}
