<?php

namespace App\Http\Controllers;

use App\Filament\Pages\Hostel\Bookings\CheckInOut;
use App\Models\Booking;
use Illuminate\Http\RedirectResponse;

class BookingCheckInOutController extends Controller
{
    public function checkIn(Booking $booking): RedirectResponse
    {
        if (in_array($booking->status, ['pending', 'booked', 'confirmed'], true)) {
            $booking->update([
                'status' => 'checked_in',
                'checked_in_at' => now(),
                'checked_out_at' => null,
            ]);

            $booking->room?->update([
                'status' => 'occupied',
            ]);
        }

        return redirect()
            ->to(CheckInOut::getUrl(panel: 'admin'))
            ->with('success', __('Booking checked in successfully.'));
    }

    public function checkOut(Booking $booking): RedirectResponse
    {
        if (in_array($booking->status, ['checked_in', 'active'], true)) {
            $booking->update([
                'status' => 'checked_out',
                'checked_out_at' => now(),
            ]);

            $booking->room?->update([
                'status' => 'available',
            ]);
        }

        return redirect()
            ->to(CheckInOut::getUrl(['tab' => 'active'], panel: 'admin'))
            ->with('success', __('Booking checked out successfully.'));
    }
}
