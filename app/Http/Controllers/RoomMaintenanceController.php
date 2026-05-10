<?php

namespace App\Http\Controllers;

use App\Filament\Pages\Hostel\Rooms\Maintenance;
use App\Models\Room;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RoomMaintenanceController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'room_id' => ['required', 'exists:rooms,id'],
        ]);

        Room::query()
            ->whereKey($validated['room_id'])
            ->where('status', '!=', 'maintenance')
            ->update(['status' => 'maintenance']);

        return redirect()
            ->to(Maintenance::getUrl(panel: 'admin'))
            ->with('success', __('Room added to maintenance.'));
    }

    public function markAvailable(Room $room): RedirectResponse
    {
        $room->update(['status' => 'available']);

        return redirect()
            ->to(Maintenance::getUrl(panel: 'admin'))
            ->with('success', __('Room marked as available.'));
    }
}
