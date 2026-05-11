<?php

namespace App\Filament\Pages\Hostel\Rooms;

use App\Filament\Pages\Hostel\BaseHostelPage;
use App\Models\Room;
use Illuminate\Database\Eloquent\Collection;

class Maintenance extends BaseHostelPage
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static string | \UnitEnum | null $navigationGroup = 'Room Management';

    protected static ?string $title = 'Maintenance';

    protected static ?string $navigationLabel = 'Maintenance';

    protected static ?string $slug = 'hostel/maintenance';

    protected static ?int $navigationSort = 24;

    protected string $view = 'filament.pages.hostel.rooms.maintenance';

    /**
     * @return Collection<int, Room>
     */
    public function getMaintenanceRoomsProperty(): Collection
    {
        return Room::query()
            ->where('status', 'maintenance')
            ->orderBy('floor')
            ->orderBy('room_number')
            ->get();
    }

    /**
     * @return Collection<int, Room>
     */
    public function getAvailableRoomsProperty(): Collection
    {
        return Room::query()
            ->where('status', '!=', 'maintenance')
            ->orderBy('floor')
            ->orderBy('room_number')
            ->get();
    }

    public function roomTypeLabel(?string $type): string
    {
        return match ($type) {
            'vip' => 'VIP',
            'ac' => 'AC',
            'non_ac' => 'Non-AC',
            default => ucfirst(str_replace('_', ' ', (string) $type)),
        };
    }
}
