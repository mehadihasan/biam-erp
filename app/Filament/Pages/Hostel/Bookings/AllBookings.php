<?php

namespace App\Filament\Pages\Hostel\Bookings;

use App\Filament\Pages\Hostel\BaseHostelPage;
use App\Models\Booking;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AllBookings extends BaseHostelPage
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static string | \UnitEnum | null $navigationGroup = 'Booking & Reservation';

    protected static ?string $title = 'All Bookings';

    protected static ?string $navigationLabel = 'All Bookings';

    protected static ?string $slug = 'hostel/bookings';

    protected static ?int $navigationSort = 31;

    protected string $view = 'filament.pages.hostel.bookings.all-bookings';

    public function getBookings(): LengthAwarePaginator
    {
        return Booking::query()
            ->with(['user', 'room'])
            ->latest()
            ->paginate(10);
    }
}
