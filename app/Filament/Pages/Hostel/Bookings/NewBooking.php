<?php

namespace App\Filament\Pages\Hostel\Bookings;

use App\Filament\Pages\Hostel\BaseHostelPage;

class NewBooking extends BaseHostelPage
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-plus';

    protected static ?string $navigationParentItem = 'Booking & Reservation';

    protected static ?string $title = 'New Booking';

    protected static ?string $navigationLabel = 'New Booking';

    protected static ?string $slug = 'hostel/bookings/new';

    protected static ?int $navigationSort = 32;

    protected string $view = 'filament.pages.hostel.bookings.new-booking';
}
