<?php

namespace App\Filament\Pages\Hostel\Payments;

use App\Filament\Pages\Hostel\BaseHostelPage;
use App\Models\Booking;
use App\Models\Payment;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\Rule;

class AllPayments extends BaseHostelPage
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-credit-card';

    protected static string | \UnitEnum | null $navigationGroup = 'Payment & Billing';

    protected static ?string $title = 'All Payments';

    protected static ?string $navigationLabel = 'All Payments';

    protected static ?string $slug = 'hostel/payments';

    protected static ?int $navigationSort = 51;

    protected string $view = 'filament.pages.hostel.payments.all-payments';

    public bool $showPaymentForm = false;

    public Collection $bookings;

    public Collection $payments;

    public ?int $bookingId = null;

    public ?string $amount = '0';

    public string $type = 'rent';

    public ?string $transactionId = null;

    public function mount(): void
    {
        $this->loadBookings();
        $this->loadPayments();
    }

    public function loadBookings(): void
    {
        $this->bookings = Booking::query()
            ->with('user')
            ->latest()
            ->get();
    }

    public function loadPayments(): void
    {
        $this->payments = Payment::query()
            ->with(['booking.user', 'guest'])
            ->latest('paid_at')
            ->latest()
            ->get();
    }

    public function togglePaymentForm(): void
    {
        $this->showPaymentForm = ! $this->showPaymentForm;
    }

    public function cancel(): void
    {
        $this->resetForm();
        $this->showPaymentForm = false;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'bookingId' => ['required', 'exists:bookings,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'type' => ['required', Rule::in(['booking_money', 'rent', 'meal'])],
            'transactionId' => ['nullable', 'string', 'max:255'],
        ]);

        $booking = Booking::query()->findOrFail($validated['bookingId']);

        Payment::query()->create([
            'ref' => $this->uniqueReference(),
            'booking_id' => $booking->id,
            'guest_id' => $booking->user_id,
            'amount' => $validated['amount'],
            'type' => $validated['type'],
            'gateway' => filled($validated['transactionId'] ?? null) ? 'SSLCOMMERZ' : 'Cash',
            'transaction_id' => $validated['transactionId'] ?: 'Cash',
            'status' => 'success',
            'paid_at' => now(),
        ]);

        $this->resetForm();
        $this->showPaymentForm = false;
        $this->loadPayments();

        Notification::make()
            ->title('Payment recorded successfully')
            ->success()
            ->send();
    }

    public function bookingRef(Booking $booking): string
    {
        return 'BK-' . str_pad((string) $booking->id, 5, '0', STR_PAD_LEFT);
    }

    private function uniqueReference(): string
    {
        do {
            $ref = 'PAY-' . random_int(10000, 99999);
        } while (Payment::query()->where('ref', $ref)->exists());

        return $ref;
    }

    private function resetForm(): void
    {
        $this->resetValidation();
        $this->bookingId = null;
        $this->amount = '0';
        $this->type = 'rent';
        $this->transactionId = null;
    }
}
