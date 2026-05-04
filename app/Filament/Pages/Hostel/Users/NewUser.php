<?php

namespace App\Filament\Pages\Hostel\Users;

use App\Filament\Pages\Hostel\BaseHostelPage;

class NewUser extends BaseHostelPage
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-user-plus';

    protected static ?string $navigationParentItem = 'User Management';

    protected static ?string $title = 'Add New User';

    protected static ?string $navigationLabel = 'Add New User';

    protected static ?string $slug = 'hostel/users/new';

    protected static ?int $navigationSort = 12;

    protected string $view = 'filament.pages.hostel.users.new-user';
}
