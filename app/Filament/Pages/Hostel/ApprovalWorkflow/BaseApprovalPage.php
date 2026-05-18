<?php

namespace App\Filament\Pages\Hostel\ApprovalWorkflow;

use App\Filament\Pages\Hostel\BaseHostelPage;
use App\Models\Booking;
use App\Models\GuestPendingApproval;

abstract class BaseApprovalPage extends BaseHostelPage
{
    protected static string | \UnitEnum | null $navigationGroup = 'Approval Workflow';

    public function bookingRef(Booking|GuestPendingApproval $booking): string
    {
        $prefix = $booking instanceof GuestPendingApproval ? 'GP-' : 'BK-';

        return $booking->ref ?: $prefix.str_pad((string) $booking->id, 5, '0', STR_PAD_LEFT);
    }

    public function roomLabel(Booking|GuestPendingApproval $booking): string
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

    public function categoryLabel(Booking|GuestPendingApproval $booking): ?string
    {
        $designation = $booking->user?->designation?->name;

        if (filled($designation)) {
            return str($designation)->snake()->toString();
        }

        return $booking->user?->isBcsCadre() ? 'bcs_officer' : null;
    }
}
