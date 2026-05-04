<?php

namespace App\Filament\Pages\Hostel\Bookings;

use App\Filament\Pages\Hostel\BaseHostelPage;

class AllBookings extends BaseHostelPage
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationParentItem = 'Booking & Reservation';

    protected static ?string $title = 'All Bookings';

    protected static ?string $navigationLabel = 'All Bookings';

    protected static ?string $slug = 'hostel/bookings';

    protected static ?int $navigationSort = 31;

    protected string $view = 'filament.pages.hostel.bookings.all-bookings';
}
