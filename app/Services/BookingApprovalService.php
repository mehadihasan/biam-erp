<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\BookingApprovalAction;
use App\Models\GuestPendingApproval;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class BookingApprovalService
{
    public function __construct(
        private readonly RoomAvailabilityService $roomAvailabilityService,
    ) {}

    public function approve(Booking $booking, ?User $user = null): void
    {
        DB::transaction(function () use ($booking, $user): void {
            $booking->update(['status' => 'approved']);
            $this->roomAvailabilityService->blockBooking($booking->refresh());

            $this->record($booking, $user, 'approved', $this->approvalLevel($user), 'Approved');
        });
    }

    public function approveGuestPending(GuestPendingApproval $approval, ?User $user = null): Booking
    {
        return DB::transaction(function () use ($approval, $user): Booking {
            $availableBedSeatCount = $this->roomAvailabilityService->availableBedSeatCount(
                $approval->room,
                $approval->check_in_date?->toDateString(),
                $approval->check_out_date?->toDateString(),
            );

            if ($availableBedSeatCount < $approval->number_of_rooms) {
                throw new \RuntimeException('The selected room no longer has enough available bed/seats for this request.');
            }

            $booking = Booking::query()->create([
                'ref' => $approval->ref,
                'user_id' => $approval->user_id,
                'room_id' => $approval->room_id,
                'room_type' => $approval->room_type,
                'number_of_rooms' => $approval->number_of_rooms,
                'check_in_date' => $approval->check_in_date,
                'check_out_date' => $approval->check_out_date,
                'notes' => $approval->notes,
                'duration_nights' => $approval->duration_nights,
                'rent_multiplier' => $approval->rent_multiplier,
                'base_rate' => $approval->base_rate,
                'calculated_rent' => $approval->calculated_rent,
                'booking_money' => $approval->booking_money,
                'total_rent' => $approval->total_rent,
                'status' => 'approved',
            ]);

            if ($approval->payment_amount !== null && filled($approval->payment_method)) {
                Payment::query()->create([
                    'ref' => $this->uniquePaymentReference(),
                    'booking_id' => $booking->id,
                    'guest_id' => $booking->user_id,
                    'amount' => $approval->payment_amount,
                    'type' => 'booking_money',
                    'gateway' => $approval->payment_method,
                    'transaction_id' => $this->uniquePaymentTransactionId(),
                    'status' => 'success',
                    'paid_at' => now(),
                ]);
            }

            $this->roomAvailabilityService->blockBooking($booking);
            $level = $this->approvalLevel($user);

            $approval->update([
                'booking_id' => $booking->id,
                'reviewed_by' => $user?->id,
                'status' => 'approved',
                'approval_level' => $level,
                'approval_notes' => 'Approved',
                'reviewed_at' => now(),
            ]);

            $this->record($booking, $user, 'approved', $level, 'Approved');

            return $booking;
        });
    }

    public function reject(Booking $booking, ?User $user = null, ?string $reason = null): void
    {
        DB::transaction(function () use ($booking, $user, $reason): void {
            $booking->update(['status' => 'rejected']);
            $this->roomAvailabilityService->releaseBooking($booking);

            $this->record($booking, $user, 'rejected', $this->approvalLevel($user), null, $reason ?: 'Rejected');
        });
    }

    public function rejectGuestPending(GuestPendingApproval $approval, ?User $user = null, ?string $reason = null): void
    {
        $approval->update([
            'reviewed_by' => $user?->id,
            'status' => 'rejected',
            'approval_level' => $this->approvalLevel($user),
            'rejection_reason' => $reason ?: 'Rejected by admin',
            'reviewed_at' => now(),
        ]);
    }

    public function escalate(Booking $booking, ?User $user = null): void
    {
        DB::transaction(function () use ($booking, $user): void {
            $booking->update(['status' => 'escalated']);

            $this->record($booking, $user, 'escalated', 'dg', 'Escalated for higher approval');
        });
    }

    public function escalateGuestPending(GuestPendingApproval $approval, ?User $user = null): void
    {
        $approval->update([
            'reviewed_by' => $user?->id,
            'status' => 'escalated',
            'approval_level' => 'dg',
            'approval_notes' => 'Escalated for higher approval',
            'reviewed_at' => now(),
        ]);
    }

    private function record(
        Booking $booking,
        ?User $user,
        string $action,
        string $level,
        ?string $notes = null,
        ?string $reason = null,
    ): void {
        BookingApprovalAction::query()->create([
            'booking_id' => $booking->id,
            'user_id' => $user?->id,
            'action' => $action,
            'level' => $level,
            'notes' => $notes,
            'reason' => $reason,
        ]);
    }

    private function approvalLevel(?User $user): string
    {
        $role = strtolower((string) $user?->role);

        return str_contains($role, 'dg') ? 'dg' : 'admin';
    }

    private function uniquePaymentReference(): string
    {
        do {
            $ref = 'PAY-'.random_int(10000, 99999);
        } while (Payment::query()->where('ref', $ref)->exists());

        return $ref;
    }

    private function uniquePaymentTransactionId(): string
    {
        do {
            $transactionId = 'TXN-'.random_int(100000, 999999);
        } while (Payment::query()->where('transaction_id', $transactionId)->exists());

        return $transactionId;
    }
}
