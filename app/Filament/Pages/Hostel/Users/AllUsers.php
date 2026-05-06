<?php

namespace App\Filament\Pages\Hostel\Users;

use App\Filament\Pages\Hostel\BaseHostelPage;

class AllUsers extends BaseHostelPage
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-users';

    protected static string | \UnitEnum | null $navigationGroup = 'User Management';

    protected static ?string $title = 'User Management';

    protected static ?string $navigationLabel = 'All Users';

    protected static ?string $slug = 'hostel/users';

    protected static ?int $navigationSort = 11;

    protected string $view = 'filament.pages.hostel.users.all-users';
}
