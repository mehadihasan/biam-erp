<?php

namespace App\Filament\Pages\Hostel\Bookings;

use App\Filament\Pages\Hostel\BaseHostelPage;

class Calendar extends BaseHostelPage
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationParentItem = 'Booking & Reservation';

    protected static ?string $title = 'Booking Calendar';

    protected static ?string $navigationLabel = 'Booking Calendar';

    protected static ?string $slug = 'hostel/bookings/calendar';

    protected static ?int $navigationSort = 34;

    protected string $view = 'filament.pages.hostel.bookings.calendar';
}
