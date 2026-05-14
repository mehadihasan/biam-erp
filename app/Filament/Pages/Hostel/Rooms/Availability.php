<?php

namespace App\Filament\Pages\Hostel\Rooms;

use App\Filament\Pages\Hostel\BaseHostelPage;
use App\Filament\Pages\Hostel\Bookings\NewBooking;
use App\Models\Room;
use App\Services\RoomAvailabilityService;
use Illuminate\Database\Eloquent\Collection;

class Availability extends BaseHostelPage
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-magnifying-glass';

    protected static string|\UnitEnum|null $navigationGroup = 'Room Management';

    protected static ?string $title = 'Room Availability';

    protected static ?string $navigationLabel = 'Room Availability';

    protected static ?string $slug = 'hostel/rooms/availability';

    protected static ?int $navigationSort = 23;

    protected string $view = 'filament.pages.hostel.rooms.availability';

    public ?string $checkInDate = null;

    public ?string $checkOutDate = null;

    public bool $searched = false;

    public function mount(): void
    {
        $this->checkInDate = request()->query('check_in');
        $this->checkOutDate = request()->query('check_out');
        $this->searched = filled($this->checkInDate) && filled($this->checkOutDate);
    }

    public function checkAvailability(): void
    {
        $this->validate([
            'checkInDate' => ['required', 'date'],
            'checkOutDate' => ['required', 'date', 'after:checkInDate'],
        ]);

        $this->searched = true;
    }

    /**
     * @return Collection<int, Room>
     */
    public function getAvailableRoomsProperty(): Collection
    {
        if (! $this->searched || blank($this->checkInDate) || blank($this->checkOutDate)) {
            return Room::query()->whereRaw('1 = 0')->get();
        }

        return app(RoomAvailabilityService::class)
            ->availableRoomQuery($this->checkInDate, $this->checkOutDate)
            ->orderBy('floor')
            ->orderBy('room_number')
            ->get();
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

    public function bookNowUrl(Room $room): string
    {
        return NewBooking::getUrl([
            'room_id' => $room->id,
            'check_in' => $this->checkInDate,
            'check_out' => $this->checkOutDate,
        ], panel: 'admin');
    }
}
