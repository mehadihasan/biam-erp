<?php

namespace App\Filament\Pages\Hostel\Rooms;

use App\Filament\Pages\Hostel\BaseHostelPage;

class Availability extends BaseHostelPage
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-magnifying-glass';

    protected static ?string $navigationParentItem = 'Room Management';

    protected static ?string $title = 'Room Availability';

    protected static ?string $navigationLabel = 'Room Availability';

    protected static ?string $slug = 'hostel/rooms/availability';

    protected static ?int $navigationSort = 23;

    protected string $view = 'filament.pages.hostel.rooms.availability';
}
