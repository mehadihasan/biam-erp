<?php

namespace App\Filament\Pages\Hostel\Bookings;

use App\Filament\Pages\Hostel\BaseHostelPage;

class CheckInOut extends BaseHostelPage
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-arrow-path';

    protected static string | \UnitEnum | null $navigationGroup = 'Booking & Reservation';

    protected static ?string $title = 'Check-in / Check-out';

    protected static ?string $navigationLabel = 'Check-in / Check-out';

    protected static ?string $slug = 'hostel/bookings/checkinout';

    protected static ?int $navigationSort = 33;

    protected string $view = 'filament.pages.hostel.bookings.check-in-out';
}
