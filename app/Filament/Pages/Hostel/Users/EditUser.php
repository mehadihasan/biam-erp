<?php

namespace App\Filament\Pages\Hostel\Users;

use App\Filament\Pages\Hostel\BaseHostelPage;
use App\Models\Designation;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class EditUser extends BaseHostelPage
{
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $title = 'Edit User';

    protected static ?string $slug = 'hostel/user/{id}/edit';

    protected string $view = 'filament.pages.hostel.users.edit-user';

    public User $user;

    public Collection $designations;

    public static function getNavigationUrl(): string
    {
        return AllUsers::getUrl(panel: 'admin');
    }

    public function mount(string | int $id): void
    {
        $this->user = User::query()
            ->with('designation')
            ->findOrFail($id);

        $this->designations = Designation::query()
            ->orderBy('name')
            ->get();
    }
}
