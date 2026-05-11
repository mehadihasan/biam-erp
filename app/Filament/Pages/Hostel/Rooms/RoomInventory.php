<?php

namespace App\Filament\Pages\Hostel\Rooms;

use App\Filament\Pages\Hostel\BaseHostelPage;
use App\Models\Room;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;

class RoomInventory extends BaseHostelPage
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $title = 'Room Inventory';

    protected static string | \UnitEnum | null $navigationGroup = 'Room Management';

    protected static ?string $navigationLabel = 'Room Inventory';

    protected static ?string $slug = 'hostel/rooms';

    protected static ?int $navigationSort = 21;

    protected string $view = 'filament.pages.hostel.rooms.room-inventory';

    public string $filterType = '';
    public string $filterStatus = '';
    public string $filterFloor = '';

    /** @return array<string, string> value => label */
    public function getTypeOptionsProperty(): array
    {
        $types = Room::query()
            ->select('room_type')
            ->distinct()
            ->orderBy('room_type')
            ->pluck('room_type')
            ->filter(fn ($v) => is_string($v) && $v !== '')
            ->values()
            ->all();

        $out = [];
        foreach ($types as $type) {
            $out[$type] = match ($type) {
                'vip' => 'VIP',
                'ac' => 'AC',
                'non_ac' => 'Non-AC',
                default => ucfirst(str_replace('_', ' ', $type)),
            };
        }

        return $out;
    }

    /** @return array<string, string> value => label */
    public function getStatusOptionsProperty(): array
    {
        $statuses = Room::query()
            ->select('status')
            ->distinct()
            ->orderBy('status')
            ->pluck('status')
            ->filter(fn ($v) => is_string($v) && $v !== '')
            ->values()
            ->all();

        $out = [];
        foreach ($statuses as $status) {
            $out[$status] = ucfirst(str_replace('_', ' ', $status));
        }

        return $out;
    }

    /** @return array<int, string> value => label */
    public function getFloorOptionsProperty(): array
    {
        $floors = Room::query()
            ->select('floor')
            ->distinct()
            ->orderBy('floor')
            ->pluck('floor')
            ->map(fn ($v) => (int) $v)
            ->values()
            ->all();

        $out = [];
        foreach ($floors as $floor) {
            $out[$floor] = (string) $floor;
        }

        return $out;
    }

    /**
     * @return Collection<int, Room>
     */
    public function getRoomsProperty(): Collection
    {
        return Room::query()
            ->when($this->filterType !== '', fn ($q) => $q->where('room_type', $this->filterType))
            ->when($this->filterStatus !== '', fn ($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterFloor !== '', fn ($q) => $q->where('floor', (int) $this->filterFloor))
            ->orderBy('floor')
            ->orderBy('room_number')
            ->get();
    }

    public function deleteRoom(int $id): void
    {
        Room::query()->whereKey($id)->delete();

        Notification::make()
            ->title('Room deleted')
            ->success()
            ->send();
    }
}
