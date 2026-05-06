<?php

namespace App\Filament\Pages\Hostel\Rooms;

use App\Filament\Pages\Hostel\BaseHostelPage;

class RoomInventory extends BaseHostelPage
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $title = 'Room Inventory';

    protected static string | \UnitEnum | null $navigationGroup = 'Room Management';

    protected static ?string $navigationLabel = 'Room Inventory';

    protected static ?string $slug = 'hostel/rooms';

    protected static ?int $navigationSort = 21;

    protected string $view = 'filament.pages.hostel.rooms.room-inventory';
}
