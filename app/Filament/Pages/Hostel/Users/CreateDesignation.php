<?php

namespace App\Filament\Pages\Hostel\Users;

use App\Filament\Pages\Hostel\BaseHostelPage;

class CreateDesignation extends BaseHostelPage
{
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $title = 'Create Designation';

    protected static ?string $slug = 'hostel/designations/create';

    protected string $view = 'filament.pages.hostel.users.designation-create';
}
