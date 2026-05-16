<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Room;
use App\Models\RoomAvailability;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Schema;

class RoomAvailabilityService
{
    /** @return list<string> */
    public function blockingBookingStatuses(): array
    {
        return ['pending', 'booked', 'confirmed', 'checked_in', 'active'];
    }

    public function availableRoomQuery(
        ?string $checkIn = null,
        ?string $checkOut = null,
        ?string $roomType = null,
        ?int $adults = null,
    ): Builder {
        return Room::query()
            ->where('status', '!=', 'maintenance')
            ->when(
                is_string($roomType) && $roomType !== '',
                fn (Builder $query): Builder => $query->where('room_type', $roomType)
            )
            ->when(
                is_int($adults) && $adults > 0,
                fn (Builder $query): Builder => $query->where('capacity', '>=', $adults)
            )
            ->when(
                $this->hasDateRange($checkIn, $checkOut),
                fn (Builder $query): Builder => $this->excludeBlockedRanges($query, (string) $checkIn, (string) $checkOut)
            );
    }

    public function roomIsAvailable(Room $room, string $checkIn, string $checkOut): bool
    {
        if ($room->status === 'maintenance') {
            return false;
        }

        if (! $this->hasDateRange($checkIn, $checkOut)) {
            return true;
        }

        return ! $this->roomHasBlockedRange($room->id, $checkIn, $checkOut);
    }

    public function availableBedSeatCount(Room $room, ?string $checkIn = null, ?string $checkOut = null): int
    {
        if ($room->status === 'maintenance') {
            return 0;
        }

        $capacity = max(0, (int) $room->capacity);

        if (! $this->hasDateRange($checkIn, $checkOut)) {
            return $capacity;
        }

        $reserved = (int) Booking::query()
            ->where('room_id', $room->id)
            ->whereIn('status', $this->blockingBookingStatuses())
            ->where('check_in_date', '<', $checkOut)
            ->where('check_out_date', '>', $checkIn)
            ->sum('number_of_rooms');

        return max(0, $capacity - $reserved);
    }

    public function roomsWithAvailableBeds(string $checkIn, string $checkOut): Collection
    {
        return Room::query()
            ->where('status', '!=', 'maintenance')
            ->orderBy('room_number')
            ->get()
            ->filter(fn (Room $room): bool => $this->availableBedSeatCount($room, $checkIn, $checkOut) > 0)
            ->values();
    }

    public function blockBooking(Booking $booking): void
    {
        if (! $this->canUseRoomAvailabilities() || ! $this->bookingShouldBlock($booking)) {
            return;
        }

        RoomAvailability::query()->updateOrCreate(
            ['booking_id' => $booking->id],
            [
                'room_id' => $booking->room_id,
                'starts_on' => $booking->check_in_date?->toDateString(),
                'ends_on' => $booking->check_out_date?->toDateString(),
                'status' => 'blocked',
                'source' => 'booking',
            ],
        );
    }

    public function releaseBooking(Booking $booking): void
    {
        if (! $this->canUseRoomAvailabilities()) {
            return;
        }

        RoomAvailability::query()
            ->where('booking_id', $booking->id)
            ->delete();
    }

    private function excludeBlockedRanges(Builder $query, string $checkIn, string $checkOut): Builder
    {
        if ($this->canUseRoomAvailabilities()) {
            return $query->whereNotExists(function ($subQuery) use ($checkIn, $checkOut): void {
                $subQuery->selectRaw('1')
                    ->from('room_availabilities')
                    ->whereColumn('room_availabilities.room_id', 'rooms.id')
                    ->where('room_availabilities.status', 'blocked')
                    ->where('room_availabilities.starts_on', '<', $checkOut)
                    ->where('room_availabilities.ends_on', '>', $checkIn);
            });
        }

        return $query->whereNotExists(function ($subQuery) use ($checkIn, $checkOut): void {
            $subQuery->selectRaw('1')
                ->from('bookings')
                ->whereColumn('bookings.room_id', 'rooms.id')
                ->whereIn('bookings.status', $this->blockingBookingStatuses())
                ->where('bookings.check_in_date', '<', $checkOut)
                ->where('bookings.check_out_date', '>', $checkIn);
        });
    }

    private function roomHasBlockedRange(int $roomId, string $checkIn, string $checkOut): bool
    {
        if ($this->canUseRoomAvailabilities()) {
            return RoomAvailability::query()
                ->where('room_id', $roomId)
                ->where('status', 'blocked')
                ->where('starts_on', '<', $checkOut)
                ->where('ends_on', '>', $checkIn)
                ->exists();
        }

        return Booking::query()
            ->where('room_id', $roomId)
            ->whereIn('status', $this->blockingBookingStatuses())
            ->where('check_in_date', '<', $checkOut)
            ->where('check_out_date', '>', $checkIn)
            ->exists();
    }

    private function bookingShouldBlock(Booking $booking): bool
    {
        return $booking->room_id !== null
            && $booking->check_in_date !== null
            && $booking->check_out_date !== null
            && in_array($booking->status, $this->blockingBookingStatuses(), true);
    }

    private function hasDateRange(?string $checkIn, ?string $checkOut): bool
    {
        return is_string($checkIn) && $checkIn !== ''
            && is_string($checkOut) && $checkOut !== '';
    }

    private function canUseRoomAvailabilities(): bool
    {
        return Schema::hasTable('room_availabilities')
            && Schema::hasColumn('room_availabilities', 'room_id')
            && Schema::hasColumn('room_availabilities', 'starts_on')
            && Schema::hasColumn('room_availabilities', 'ends_on');
    }
}
