<?php

namespace App\Filament\Pages\Hostel\Bookings;

use App\Filament\Pages\Hostel\BaseHostelPage;
use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use App\Services\RoomAvailabilityService;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
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

        $this->rooms = app(RoomAvailabilityService::class)
            ->availableRoomQuery()
            ->orderBy('room_number')
            ->get();

        $this->selectedRoomId = request()->integer('room_id') ?: null;
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
        $guestRules = ['required', 'exists:users,id'];

        if ($this->cadreFlow) {
            $guestRules[] = Rule::in([(string) $this->cadreUserId]);
        }

        $validated = $this->validate([
            'selectedGuestId' => $guestRules,
            'selectedRoomId' => ['required', 'exists:rooms,id'],
            'roomType' => ['required', 'in:vip,ac,non_ac'],
            'checkInDate' => ['required', 'date'],
            'checkOutDate' => ['required', 'date', 'after:checkInDate'],
            'numberOfRooms' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string'],
        ]);

        if ($this->cadreFlow && (int) $validated['selectedGuestId'] !== $this->cadreUserId) {
            throw new HttpException(403, 'Cadre booking guest mismatch.');
        }

        $room = Room::query()->findOrFail($validated['selectedRoomId']);

        $roomAvailability = app(RoomAvailabilityService::class);

        if (! $roomAvailability->roomIsAvailable($room, $validated['checkInDate'], $validated['checkOutDate'])) {
            $this->addError('checkInDate', 'This room is not available for the selected dates.');

            return;
        }

        $calculation = $this->calculateRent(
            $room,
            $validated['checkInDate'],
            $validated['checkOutDate'],
            (int) $validated['numberOfRooms'],
        );

        $booking = Booking::query()->create([
            'user_id' => $validated['selectedGuestId'],
            'room_id' => $room->id,
            'room_type' => $validated['roomType'],
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

        $roomAvailability->blockBooking($booking);

        if ($this->cadreFlow) {
            $this->successReference = 'BKG-'.str_pad((string) $booking->id, 4, '0', STR_PAD_LEFT);
            $this->showSuccessModal = true;

            return;
        }

        Notification::make()
            ->title('Booking created successfully')
            ->success()
            ->send();

        $this->redirect(AllBookings::getUrl(panel: 'admin'));
    }

    public function bookAnother(): void
    {
        $this->showSuccessModal = false;
        $this->successReference = null;
        $this->selectedRoomId = null;
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
}
