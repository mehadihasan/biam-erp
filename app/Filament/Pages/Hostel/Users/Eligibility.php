<?php

namespace App\Filament\Pages\Hostel\Users;

use App\Filament\Pages\Hostel\BaseHostelPage;

class Eligibility extends BaseHostelPage
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationParentItem = 'User Management';

    protected static ?string $title = 'Eligibility Verification';

    protected static ?string $navigationLabel = 'Eligibility Verification';

    protected static ?string $slug = 'hostel/users/eligibility';

    protected static ?int $navigationSort = 13;

    protected string $view = 'filament.pages.hostel.users.eligibility';
}
