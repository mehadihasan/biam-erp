<?php

namespace App\Http\Controllers;

use App\Filament\Pages\Hostel\Bookings\CheckInOut;
use App\Models\Booking;
use App\Services\RoomAvailabilityService;
use Illuminate\Http\RedirectResponse;

class BookingCheckInOutController extends Controller
{
    public function __construct(private readonly RoomAvailabilityService $roomAvailability) {}

    public function checkIn(Booking $booking): RedirectResponse
    {
        if (in_array($booking->status, ['pending', 'booked', 'confirmed'], true)) {
            $booking->update([
                'status' => 'checked_in',
                'checked_in_at' => now(),
                'checked_out_at' => null,
            ]);

            $this->roomAvailability->blockBooking($booking->refresh());
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

            $this->roomAvailability->releaseBooking($booking);
        }

        return redirect()
            ->to(CheckInOut::getUrl(['tab' => 'active'], panel: 'admin'))
            ->with('success', __('Booking checked out successfully.'));
    }
}
