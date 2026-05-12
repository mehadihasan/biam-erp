<?php

namespace App\Filament\Pages\Hostel\Payments;

use App\Filament\Pages\Hostel\BaseHostelPage;
use App\Models\Booking;
use App\Services\InvoiceCalculator;
use Illuminate\Database\Eloquent\Collection;

class GenerateInvoice extends BaseHostelPage
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-text';

    protected static string | \UnitEnum | null $navigationGroup = 'Payment & Billing';

    protected static ?string $title = 'Generate Invoice';

    protected static ?string $navigationLabel = 'Generate Invoice';

    protected static ?string $slug = 'hostel/payments/invoice';

    protected static ?int $navigationSort = 52;

    protected string $view = 'filament.pages.hostel.payments.generate-invoice';

    public ?int $bookingId = null;

    public function getBookingsProperty(): Collection
    {
        return Booking::query()
            ->with('user')
            ->latest()
            ->get();
    }

    public function getInvoiceProperty(): ?array
    {
        if (! $this->bookingId) {
            return null;
        }

        $booking = Booking::query()
            ->with(['user', 'room'])
            ->find($this->bookingId);

        if (! $booking) {
            return null;
        }

        return app(InvoiceCalculator::class)->calculate($booking);
    }

    public function bookingRef(Booking $booking): string
    {
        return 'BK-' . str_pad((string) $booking->id, 5, '0', STR_PAD_LEFT);
    }
}
