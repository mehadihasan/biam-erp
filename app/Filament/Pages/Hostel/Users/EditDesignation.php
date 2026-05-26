<?php

namespace App\Filament\Pages\Hostel\Users;

use App\Filament\Pages\Hostel\BaseHostelPage;
use App\Models\Designation;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EditDesignation extends BaseHostelPage
{
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $title = 'Edit Designation';

    protected static ?string $slug = 'hostel/designations/edit';

    protected string $view = 'filament.pages.hostel.users.designation-edit';

    public Designation $designation;

    public static function getNavigationUrl(): string
    {
        return Designations::getUrl(panel: 'admin');
    }

    public function mount(): void
    {
        $designationId = (int) request()->query('id', 0);

        if ($designationId <= 0) {
            throw new NotFoundHttpException('Designation not found.');
        }

        $this->designation = Designation::query()->findOrFail($designationId);
    }

    public static function urlForDesignation(int $designationId, string $panel = 'admin'): string
    {
        return static::getUrl(panel: $panel) . '?id=' . $designationId;
    }
}
