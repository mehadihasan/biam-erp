<?php

namespace App\Filament\Pages\Hostel\Bookings;

use App\Filament\Pages\Hostel\BaseHostelPage;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Room;
use App\Models\User;
use App\Services\RoomAvailabilityService;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpKernel\Exception\HttpException;

class NewBooking extends BaseHostelPage
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-plus';

    protected static string|\UnitEnum|null $navigationGroup = 'Booking & Reservation';

    protected static ?string $title = 'New Booking';

    protected static ?string $navigationLabel = 'New Booking';

    protected static ?string $slug = 'hostel/bookings/new';

    protected static ?int $navigationSort = 32;

    protected string $view = 'filament.pages.hostel.bookings.new-booking';

    public Collection $users;

    public Collection $rooms;

    public ?int $selectedGuestId = null;

    public ?string $ref = null;

    public string $roomType = 'ac';

    public ?int $selectedRoomId = null;

    public ?string $checkInDate = null;

    public ?string $checkOutDate = null;

    public int $numberOfRooms = 1;

    public ?string $notes = null;

    public bool $cadreFlow = false;

    public ?int $cadreUserId = null;

    public bool $showSuccessModal = false;

    public ?string $successReference = null;

    public bool $showPaymentModal = false;

    public ?string $paymentGuestName = null;

    public ?string $paymentAmount = null;

    public ?string $paymentMethod = null;

    public function mount(): void
    {
        $this->cadreFlow = request()->boolean('cadre') && request()->session()->get('cadre_auth') === true;
        $cadreReference = request()->session()->get('cadre_reference');
        $cadreUser = $this->cadreFlow && is_string($cadreReference)
            ? User::query()->where('cadre_number', $cadreReference)->first()
            : null;

        if ($this->cadreFlow && ! $cadreUser) {
            throw new HttpException(403, 'Authenticated cadre user was not found.');
        }

        $this->users = User::query()
            ->with('designation')
            ->when($this->cadreFlow, fn ($query) => $query->whereKey($cadreUser?->id))
            ->orderBy('name')
            ->get();

        $this->rooms = new Collection;

        $this->selectedRoomId = request()->integer('room_id') ?: request()->integer('room') ?: null;
        $this->selectedGuestId = $this->cadreFlow ? $cadreUser?->id : null;
        $this->cadreUserId = $cadreUser?->id;
        $this->roomType = Room::query()->whereKey($this->selectedRoomId)->value('room_type') ?: 'ac';
        $checkIn = request()->query('check_in');
        $checkOut = request()->query('check_out');
        $this->checkInDate = is_string($checkIn) ? $checkIn : null;
        $this->checkOutDate = is_string($checkOut) ? $checkOut : null;
    }

    public function roomTypeLabel(?string $type): string
    {
        return match ($type) {
            'vip' => 'VIP',
            'ac' => 'AC',
            'non_ac' => 'Non_AC',
            default => ucfirst((string) $type),
        };
    }

    public function updatedSelectedRoomId(): void
    {
        $roomType = Room::query()->whereKey($this->selectedRoomId)->value('room_type');

        if (is_string($roomType) && $roomType !== '') {
            $this->roomType = $roomType;
        }

        $this->clampBedSeatSelection();
    }

    public function updatedCheckInDate(): void
    {
        $this->resetBedSeatSelection();
    }

    public function updatedCheckOutDate(): void
    {
        $this->resetBedSeatSelection();
    }

    public function getAvailableRoomsProperty(): Collection
    {
        if (! $this->hasValidDateRange()) {
            return new Collection;
        }

        return app(RoomAvailabilityService::class)
            ->roomsWithAvailableBeds($this->checkInDate, $this->checkOutDate);
    }

    public function getAvailableBedSeatCountProperty(): ?int
    {
        if (! $this->selectedRoomId || ! $this->hasValidDateRange()) {
            return null;
        }

        $room = Room::query()->find($this->selectedRoomId);

        if (! $room) {
            return null;
        }

        return app(RoomAvailabilityService::class)
            ->availableBedSeatCount($room, $this->checkInDate, $this->checkOutDate);
    }

    public function getSelectedRoomProperty(): ?Room
    {
        if (! $this->selectedRoomId) {
            return null;
        }

        return Room::query()->find($this->selectedRoomId);
    }

    public function getSelectedRoomUnavailableProperty(): bool
    {
        if (! $this->selectedRoom || ! $this->hasValidDateRange()) {
            return false;
        }

        return ! $this->availableRooms->contains('id', $this->selectedRoom->id);
    }

    public function getBedSeatOptionsProperty(): array
    {
        $availableBedSeatCount = $this->getAvailableBedSeatCountProperty();

        if (! is_int($availableBedSeatCount) || $availableBedSeatCount < 1) {
            return [];
        }

        return range(1, $availableBedSeatCount);
    }

    public function getCalculationProperty(): ?array
    {
        if (! $this->selectedRoomId || ! $this->checkInDate || ! $this->checkOutDate) {
            return null;
        }

        $room = Room::query()->find($this->selectedRoomId);
        if (! $room) {
            return null;
        }

        try {
            return $this->calculateRent(
                $room,
                $this->checkInDate,
                $this->checkOutDate,
                max(1, (int) $this->numberOfRooms),
            );
        } catch (\Throwable) {
            return null;
        }
    }

    public function save(): void
    {
        $validated = $this->validatedBookingData();
        $room = Room::query()->findOrFail($validated['selectedRoomId']);

        if (! $this->ensureRoomAndBedSeatAvailability($room, $validated)) {
            return;
        }

        $calculation = $this->calculateRent(
            $room,
            $validated['checkInDate'],
            $validated['checkOutDate'],
            (int) $validated['numberOfRooms'],
        );

        $this->paymentGuestName = User::query()->whereKey($validated['selectedGuestId'])->value('name');
        $this->paymentAmount = number_format((float) $calculation['booking_money'], 2, '.', '');
        $this->paymentMethod = null;
        $this->showPaymentModal = true;
    }

    public function cancelPayment(): void
    {
        $this->showPaymentModal = false;
        $this->resetValidation(['paymentGuestName', 'paymentAmount', 'paymentMethod']);
    }

    public function pay(): void
    {
        $validated = $this->validatedBookingData();
        $room = Room::query()->findOrFail($validated['selectedRoomId']);

        if (! $this->ensureRoomAndBedSeatAvailability($room, $validated)) {
            $this->showPaymentModal = false;

            return;
        }

        $calculation = $this->calculateRent(
            $room,
            $validated['checkInDate'],
            $validated['checkOutDate'],
            (int) $validated['numberOfRooms'],
        );
        $minimumPayment = round((float) $calculation['total_rent'] * 0.20, 2);

        $payment = $this->validate([
            'paymentGuestName' => ['required', 'string', 'max:255'],
            'paymentAmount' => ['required', 'numeric', 'min:'.$minimumPayment],
            'paymentMethod' => ['required', Rule::in(['Card', 'Mobile Banking'])],
        ]);

        DB::transaction(function () use ($validated, $room, $calculation, $payment): void {
            $booking = Booking::query()->create([
                'ref' => $validated['ref'] ?? null,
                'user_id' => $validated['selectedGuestId'],
                'room_id' => $room->id,
                'room_type' => $room->room_type,
                'number_of_rooms' => $validated['numberOfRooms'],
                'check_in_date' => $validated['checkInDate'],
                'check_out_date' => $validated['checkOutDate'],
                'notes' => $validated['notes'] ?? null,
                'duration_nights' => $calculation['duration_nights'],
                'rent_multiplier' => $calculation['rent_multiplier'],
                'base_rate' => $calculation['base_rate'],
                'calculated_rent' => $calculation['calculated_rent'],
                'booking_money' => $calculation['booking_money'],
                'total_rent' => $calculation['total_rent'],
                'status' => 'pending',
            ]);

            app(RoomAvailabilityService::class)->blockBooking($booking);

            Payment::query()->create([
                'ref' => $this->uniquePaymentReference(),
                'booking_id' => $booking->id,
                'guest_id' => $booking->user_id,
                'amount' => $payment['paymentAmount'],
                'type' => 'booking_money',
                'gateway' => $payment['paymentMethod'],
                'transaction_id' => $this->uniquePaymentTransactionId(),
                'status' => 'success',
                'paid_at' => now(),
            ]);
        });

        Notification::make()
            ->title('Booking created and payment recorded successfully.')
            ->success()
            ->send();

        $this->redirect(AllBookings::getUrl(panel: 'admin'));
    }

    private function validatedBookingData(): array
    {
        $guestRules = ['required', 'exists:users,id'];

        if ($this->cadreFlow) {
            $guestRules[] = Rule::in([(string) $this->cadreUserId]);
        }

        return $this->validate([
            'selectedGuestId' => $guestRules,
            'ref' => ['nullable', 'string', 'max:50', 'unique:bookings,ref'],
            'selectedRoomId' => ['required', 'exists:rooms,id'],
            'checkInDate' => ['required', 'date'],
            'checkOutDate' => ['required', 'date', 'after:checkInDate'],
            'numberOfRooms' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string'],
        ]);
    }

    private function ensureRoomAndBedSeatAvailability(Room $room, array $validated): bool
    {
        if ($this->cadreFlow && (int) $validated['selectedGuestId'] !== $this->cadreUserId) {
            throw new HttpException(403, 'Cadre booking guest mismatch.');
        }

        $availableRoomIds = $this->getAvailableRoomsProperty()->pluck('id')->all();

        if (! in_array($room->id, $availableRoomIds, true)) {
            $this->addError('selectedRoomId', 'Please select an available room for the selected dates.');

            return false;
        }

        $roomAvailability = app(RoomAvailabilityService::class);
        $availableBedSeatCount = $roomAvailability->availableBedSeatCount($room, $validated['checkInDate'], $validated['checkOutDate']);

        if ($availableBedSeatCount < 1) {
            $this->addError('checkInDate', 'This room is not available for the selected dates.');

            return false;
        }

        if ((int) $validated['numberOfRooms'] > $availableBedSeatCount) {
            $this->addError('numberOfRooms', 'The selected bed/seat count exceeds availability.');

            return false;
        }

        return true;
    }

    public function bookAnother(): void
    {
        $this->showSuccessModal = false;
        $this->successReference = null;
        $this->selectedRoomId = null;
        $this->ref = null;
        $this->checkInDate = null;
        $this->checkOutDate = null;
        $this->numberOfRooms = 1;
        $this->notes = null;
        $this->roomType = 'ac';

        if ($this->cadreFlow) {
            $this->selectedGuestId = $this->cadreUserId;
        }
    }

    public function done(): void
    {
        $this->redirect($this->cadreFlow ? route('cadre.booking') : AllBookings::getUrl(panel: 'admin'));
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

    private function clampBedSeatSelection(): void
    {
        $availableBedSeatCount = $this->getAvailableBedSeatCountProperty();

        if (! is_int($availableBedSeatCount) || $availableBedSeatCount < 1) {
            $this->numberOfRooms = 1;

            return;
        }

        $this->numberOfRooms = min(max(1, (int) $this->numberOfRooms), $availableBedSeatCount);
    }

    private function resetBedSeatSelection(): void
    {
        $this->numberOfRooms = 1;
    }

    private function hasValidDateRange(): bool
    {
        if (! $this->checkInDate || ! $this->checkOutDate) {
            return false;
        }

        try {
            return Carbon::parse($this->checkOutDate)->startOfDay()
                ->greaterThan(Carbon::parse($this->checkInDate)->startOfDay());
        } catch (\Throwable) {
            return false;
        }
    }
}
