<?php

namespace App\Filament\Pages\Hostel\Bookings;

use App\Filament\Pages\Hostel\BaseHostelPage;
use App\Models\Booking;
use Illuminate\Database\Eloquent\Collection;

class CheckInOut extends BaseHostelPage
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-arrow-path';

    protected static string | \UnitEnum | null $navigationGroup = 'Booking & Reservation';

    protected static ?string $title = 'Check-in / Check-out';

    protected static ?string $navigationLabel = 'Check-in / Check-out';

    protected static ?string $slug = 'hostel/check-in-out';

    protected static ?int $navigationSort = 33;

    protected string $view = 'filament.pages.hostel.bookings.check-in-out';

    public string $activeTab = 'pending';

    public function mount(): void
    {
        $tab = request()->query('tab');
        $this->activeTab = $tab === 'active' ? 'active' : 'pending';
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab === 'active' ? 'active' : 'pending';
    }

    /**
     * @return Collection<int, Booking>
     */
    public function getPendingBookingsProperty(): Collection
    {
        return Booking::query()
            ->with(['user', 'room'])
            ->whereIn('status', ['pending', 'booked', 'confirmed'])
            ->whereNull('checked_in_at')
            ->whereNull('checked_out_at')
            ->orderBy('check_in_date')
            ->get();
    }

    /**
     * @return Collection<int, Booking>
     */
    public function getActiveBookingsProperty(): Collection
    {
        return Booking::query()
            ->with(['user', 'room'])
            ->whereIn('status', ['checked_in', 'active'])
            ->whereNull('checked_out_at')
            ->orderBy('check_out_date')
            ->get();
    }

    public function bookingRef(Booking $booking): string
    {
        return 'BK-' . str_pad((string) $booking->id, 5, '0', STR_PAD_LEFT);
    }

    public function roomLabel(Booking $booking): string
    {
        $roomNumber = $booking->room?->room_number ?: '-';
        $type = match ($booking->room_type) {
            'vip' => 'VIP',
            'ac' => 'AC',
            'non_ac' => 'Non-AC',
            default => ucfirst((string) $booking->room_type),
        };

        return "{$roomNumber} ({$type})";
    }
}
