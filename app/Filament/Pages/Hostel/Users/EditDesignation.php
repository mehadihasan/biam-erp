<?php

namespace App\Filament\Pages\Hostel\Users;

use App\Filament\Pages\Hostel\BaseHostelPage;
use App\Models\Designation;

class EditDesignation extends BaseHostelPage
{
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $title = 'Edit Designation';

    protected static ?string $slug = 'hostel/designations/{id}/edit';

    protected string $view = 'filament.pages.hostel.users.designation-edit';

    public Designation $designation;

    public function mount(string | int $id): void
    {
        $this->designation = Designation::query()->findOrFail($id);
    }
}
