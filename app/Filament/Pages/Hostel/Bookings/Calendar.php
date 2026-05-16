<?php

namespace App\Filament\Pages\Hostel\Bookings;

use App\Filament\Pages\Hostel\BaseHostelPage;
use App\Models\Booking;
use App\Models\Room;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class Calendar extends BaseHostelPage
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $title = 'Booking Calendar';

    protected static ?string $navigationLabel = 'Booking Calendar';

    protected static ?string $slug = 'hostel/bookings/calendar';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.pages.hostel.bookings.calendar';

    public string $month;

    public function mount(): void
    {
        $this->month = now()->format('Y-m');
    }

    public function previousMonth(): void
    {
        $this->month = $this->monthStart()->subMonth()->format('Y-m');
    }

    public function nextMonth(): void
    {
        $this->month = $this->monthStart()->addMonth()->format('Y-m');
    }

    public function monthStart(): Carbon
    {
        return Carbon::createFromFormat('Y-m-d', $this->month . '-01')->startOfMonth();
    }

    public function monthEnd(): Carbon
    {
        return $this->monthStart()->copy()->endOfMonth();
    }

    public function calendarStart(): Carbon
    {
        return $this->monthStart()->copy()->startOfWeek(Carbon::SUNDAY);
    }

    public function calendarEnd(): Carbon
    {
        return $this->monthEnd()->copy()->endOfWeek(Carbon::SATURDAY);
    }

    public function monthLabel(): string
    {
        return $this->monthStart()->format('F Y');
    }

    /**
     * @return Collection<int, Room>
     */
    public function getRoomsProperty(): Collection
    {
        return Room::query()
            ->select('rooms.*')
            ->selectRaw(
                'exists (
                    select 1
                    from bookings
                    where bookings.room_id = rooms.id
                    and bookings.status in (?, ?)
                    and bookings.checked_out_at is null
                ) as has_active_stay',
                ['checked_in', 'active'],
            )
            ->orderByRaw($this->roomTypeOrderSql())
            ->orderBy('floor')
            ->orderBy('room_number')
            ->get();
    }

    /**
     * @return Collection<int, Booking>
     */
    public function getBookingsProperty(): Collection
    {
        return Booking::query()
            ->with(['room', 'user'])
            ->whereNotIn('status', ['cancelled', 'checked_out', 'completed'])
            ->where('check_in_date', '<=', $this->monthEnd()->toDateString())
            ->where('check_out_date', '>', $this->monthStart()->toDateString())
            ->orderBy('check_in_date')
            ->get();
    }

    /**
     * @return array<int, Carbon>
     */
    public function calendarDays(): array
    {
        $days = [];
        $day = $this->calendarStart();
        $end = $this->calendarEnd();

        while ($day->lte($end)) {
            $days[] = $day->copy();
            $day->addDay();
        }

        return $days;
    }

    /**
     * @return Collection<int, Booking>
     */
    public function bookingsForDay(Carbon $day): Collection
    {
        return $this->bookings
            ->filter(fn (Booking $booking): bool => $booking->check_in_date->lte($day)
                && $booking->check_out_date->gt($day))
            ->values();
    }

    public function roomBookingStatusesForDay(Carbon $day): array
    {
        return $this->bookingsForDay($day)
            ->filter(fn (Booking $booking): bool => $booking->room !== null)
            ->groupBy('room_id')
            ->map(function ($bookings): ?array {
                /** @var Booking $firstBooking */
                $firstBooking = $bookings->first();
                $room = $firstBooking->room;

                if ($room === null) {
                    return null;
                }

                $roomCapacity = max(0, (int) $room->capacity);
                $bookedBedSeatCount = (int) $bookings->sum('number_of_rooms');

                if ($roomCapacity < 1 || $bookedBedSeatCount < 1) {
                    return null;
                }

                $availableBedSeatCount = max(0, $roomCapacity - $bookedBedSeatCount);
                $status = $bookedBedSeatCount >= $roomCapacity ? 'occupied' : 'partial';

                return [
                    'status' => $status,
                    'label' => $status === 'occupied' ? 'Occupied' : 'Partially Booked',
                    'room_number' => $room->room_number,
                    'room_capacity' => $roomCapacity,
                    'booked_bed_seat_count' => $bookedBedSeatCount,
                    'available_bed_seat_count' => $availableBedSeatCount,
                    'bookings' => $bookings
                        ->map(fn (Booking $booking): array => [
                            'guest_name' => $booking->user?->name ?: 'Guest',
                            'bed_seat_count' => (int) $booking->number_of_rooms,
                        ])
                        ->values()
                        ->all(),
                ];
            })
            ->filter()
            ->sortBy([
                ['status', 'asc'],
                ['room_number', 'asc'],
            ])
            ->values()
            ->all();
    }

    public function roomTypeLabel(?string $type): string
    {
        return match ($this->roomTypeKey($type)) {
            'ac' => 'AC',
            'non_ac' => 'Non-AC',
            'vip' => 'VIP',
            default => ucfirst((string) $type),
        };
    }

    public function roomTypeKey(?string $type): string
    {
        return match (strtolower(str_replace('-', '_', (string) $type))) {
            'ac' => 'ac',
            'non_ac' => 'non_ac',
            'vip' => 'vip',
            default => strtolower((string) $type),
        };
    }

    public function roomStatusLabel(Room $room): string
    {
        if ((bool) ($room->has_active_stay ?? false)) {
            return 'Occupied';
        }

        if ($room->status === 'maintenance') {
            return 'Maintenance';
        }

        return 'Available';
    }

    public function roomStatusClass(Room $room): string
    {
        return match ($this->roomStatusLabel($room)) {
            'Occupied' => 'booking-calendar-status booking-calendar-status--occupied',
            'Maintenance' => 'booking-calendar-status booking-calendar-status--maintenance',
            default => 'booking-calendar-status booking-calendar-status--available',
        };
    }

    public function bookingTypeClass(Booking $booking): string
    {
        return match ($this->roomTypeKey($booking->room?->room_type ?? $booking->room_type)) {
            'vip' => 'booking-calendar-event booking-calendar-event--vip',
            'non_ac' => 'booking-calendar-event booking-calendar-event--non-ac',
            default => 'booking-calendar-event booking-calendar-event--ac',
        };
    }

    public function bookingStatusClass(array $roomStatus): string
    {
        return match ($roomStatus['status'] ?? null) {
            'occupied' => 'booking-calendar-event booking-calendar-event--occupied',
            default => 'booking-calendar-event booking-calendar-event--partial',
        };
    }

    private function roomTypeOrderSql(): string
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            return "FIELD(lower(replace(room_type, '-', '_')), 'ac', 'non_ac', 'vip')";
        }

        return "case lower(replace(room_type, '-', '_')) when 'ac' then 1 when 'non_ac' then 2 when 'vip' then 3 else 4 end";
    }
}
