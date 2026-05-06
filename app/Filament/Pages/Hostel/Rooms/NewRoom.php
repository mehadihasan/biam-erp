<?php

namespace App\Filament\Pages\Hostel\Rooms;

use App\Filament\Pages\Hostel\BaseHostelPage;

class NewRoom extends BaseHostelPage
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-plus-circle';

    protected static string | \UnitEnum | null $navigationGroup = 'Room Management';

    protected static ?string $title = 'Add / Edit Room';

    protected static ?string $navigationLabel = 'Add / Edit Room';

    protected static ?string $slug = 'hostel/rooms/new';

    protected static ?int $navigationSort = 22;

    protected string $view = 'filament.pages.hostel.rooms.new-room';
}
