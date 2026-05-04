<?php

namespace App\Filament\Pages\Hostel\Rooms;

use App\Filament\Pages\Hostel\BaseHostelPage;

class Maintenance extends BaseHostelPage
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static ?string $navigationParentItem = 'Room Management';

    protected static ?string $title = 'Maintenance';

    protected static ?string $navigationLabel = 'Maintenance';

    protected static ?string $slug = 'hostel/rooms/maintenance';

    protected static ?int $navigationSort = 24;

    protected string $view = 'filament.pages.hostel.rooms.maintenance';
}
