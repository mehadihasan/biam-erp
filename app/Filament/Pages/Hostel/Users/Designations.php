<?php

namespace App\Filament\Pages\Hostel\Users;

use App\Filament\Pages\Hostel\BaseHostelPage;
use App\Models\Designation;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class Designations extends BaseHostelPage
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-identification';

    protected static string | \UnitEnum | null $navigationGroup = 'Settings';

    protected static ?string $title = 'Designations';

    protected static ?string $navigationLabel = 'Designation';

    protected static ?string $slug = 'hostel/designations';

    protected static ?int $navigationSort = 31;

    protected string $view = 'filament.pages.hostel.users.designations';

    public static function getNavigationUrl(): string
    {
        return static::getUrl(panel: 'admin');
    }

    public function getDesignations(): LengthAwarePaginator
    {
        return Designation::query()
            ->latest()
            ->paginate(10);
    }
}
