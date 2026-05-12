<?php

namespace App\Filament\Pages\Hostel\Payments;

use App\Filament\Pages\Hostel\BaseHostelPage;
use App\Models\Payment;
use Illuminate\Support\Collection;

class RevenueReport extends BaseHostelPage
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-chart-bar';

    protected static string | \UnitEnum | null $navigationGroup = 'Payment & Billing';

    protected static ?string $title = 'Revenue Report';

    protected static ?string $navigationLabel = 'Revenue Report';

    protected static ?string $slug = 'hostel/payments/revenue';

    protected static ?int $navigationSort = 53;

    protected string $view = 'filament.pages.hostel.payments.revenue-report';

    public function stats(): array
    {
        $successful = Payment::query()->where('status', 'success');

        return [
            'total_revenue' => (float) (clone $successful)->sum('amount'),
            'booking_money' => (float) (clone $successful)->where('type', 'booking_money')->sum('amount'),
            'transactions' => (int) (clone $successful)->count(),
        ];
    }

    public function chartRows(): Collection
    {
        $totals = Payment::query()
            ->where('status', 'success')
            ->selectRaw('type, SUM(amount) as total')
            ->groupBy('type')
            ->pluck('total', 'type');

        return collect([
            'booking_money' => 'Booking Money',
            'rent' => 'Rent',
            'meal' => 'Meal',
        ])->map(fn (string $label, string $type): array => [
            'type' => $type,
            'label' => $label,
            'total' => (float) ($totals[$type] ?? 0),
        ])->values();
    }
}
