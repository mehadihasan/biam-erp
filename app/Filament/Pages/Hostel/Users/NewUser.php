<?php

namespace App\Filament\Pages\Hostel\Users;

use App\Filament\Pages\Hostel\BaseHostelPage;
use App\Models\Designation;
use Illuminate\Database\Eloquent\Collection;

class NewUser extends BaseHostelPage
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-user-plus';

    protected static string | \UnitEnum | null $navigationGroup = 'User Management';

    protected static ?string $title = 'Add New User';

    protected static ?string $navigationLabel = 'Add New User';

    protected static ?string $slug = 'hostel/user/create';

    protected static ?int $navigationSort = 12;

    protected string $view = 'filament.pages.hostel.users.new-user';

    public Collection $designations;

    public function mount(): void
    {
        $this->designations = Designation::query()
            ->orderBy('name')
            ->get();
    }
}
