<?php

namespace App\Filament\Pages\Hostel\Rooms;

use App\Filament\Pages\Hostel\BaseHostelPage;
use App\Models\Room;
use Filament\Facades\Filament;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RoomDetail extends BaseHostelPage
{
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $slug = 'hostel/rooms/detail';

    protected static ?int $navigationSort = 23;

    protected string $view = 'filament.pages.hostel.rooms.room-detail';

    public ?Room $room = null;

    public function mount(): void
    {
        $roomId = (int) request()->query('room', 0);
        if ($roomId <= 0) {
            throw new NotFoundHttpException('Room not found.');
        }

        $this->room = Room::query()->findOrFail($roomId);
    }

    public function getHeading(): string | Htmlable
    {
        return 'Room ' . ($this->room?->room_number ?? '');
    }

    /** @return array<int, string> */
    public function getImageUrlsProperty(): array
    {
        return $this->room?->imageUrls() ?? [];
    }

    /**
     * Link to this page with ?room=id. Prefer the named Filament route; if missing (often due to a stale
     * `filament:cache-components` manifest), fall back to the panel path so the listing page does not 500.
     * Run `php artisan filament:optimize-clear` (and then `php artisan filament:cache-components` if you use caching) so the route is registered.
     */
    public static function urlForRoom(int $roomId, string $panelId = 'admin'): string
    {
        $panel = Filament::getPanel($panelId);
        $query = '?room=' . $roomId;
        $name = static::getRouteName($panel);

        if (Route::has($name)) {
            return route($name) . $query;
        }

        $prefix = trim($panel->getPath(), '/');
        $slug = static::$slug ?? 'hostel/rooms/detail';

        return url($prefix . '/' . $slug) . $query;
    }
}
