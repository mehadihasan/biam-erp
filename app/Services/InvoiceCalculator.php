<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\MealOrder;
use App\Models\Payment;
use Illuminate\Support\Collection;

class InvoiceCalculator
{
    public function calculate(Booking $booking): array
    {
        $booking->loadMissing(['user', 'room']);

        $duration = max(1, (int) ($booking->duration_nights ?: $booking->check_in_date?->diffInDays($booking->check_out_date) ?: 1));
        $rentMultiplier = max(1, (int) $booking->rent_multiplier);
        $roomRate = (float) ($booking->base_rate ?: $booking->room?->base_rate ?: 0);
        $subtotal = $roomRate * $duration * $rentMultiplier;

        $payments = Payment::query()
            ->where('booking_id', $booking->id)
            ->where('status', 'success')
            ->get();

        $mealTotal = $this->mealTotalForBooking($booking);
        $bookingMoneyPaid = (float) $payments->where('type', 'booking_money')->sum('amount');
        $rentPaid = (float) $payments->where('type', 'rent')->sum('amount');
        $mealPaid = (float) $payments->where('type', 'meal')->sum('amount');
        $balanceDue = $subtotal + $mealTotal - $bookingMoneyPaid - $rentPaid - $mealPaid;

        return [
            'booking' => $booking,
            'duration' => $duration,
            'rent_multiplier' => $rentMultiplier,
            'room_rate' => $roomRate,
            'subtotal' => $subtotal,
            'meal_total' => $mealTotal,
            'booking_money_paid' => $bookingMoneyPaid,
            'rent_paid' => $rentPaid,
            'meal_paid' => $mealPaid,
            'balance_due' => $balanceDue,
            'payments' => $payments,
        ];
    }

    private function mealTotalForBooking(Booking $booking): float
    {
        if (! $booking->user_id) {
            return 0.0;
        }

        $query = MealOrder::query()
            ->where('guest_id', $booking->user_id);

        if ($booking->check_in_date && $booking->check_out_date) {
            $query->whereDate('created_at', '>=', $booking->check_in_date->toDateString())
                ->whereDate('created_at', '<=', $booking->check_out_date->toDateString());
        }

        return (float) $query->get()->sum(fn (MealOrder $order): float => $order->display_total);
    }
}
