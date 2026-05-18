<?php

namespace App\Filament\Pages\Hostel\ApprovalWorkflow;

use App\Models\GuestPendingApproval;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ApprovedBookings extends BaseApprovalPage
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-check-circle';

    protected static ?string $title = 'Approved Bookings';

    protected static ?string $navigationLabel = 'Approved';

    protected static ?string $slug = 'hostel/approval-workflow/approved';

    protected static ?int $navigationSort = 35;

    protected string $view = 'filament.pages.hostel.approval-workflow.approved-bookings';

    public function getBookings(): LengthAwarePaginator
    {
        return GuestPendingApproval::query()
            ->with(['user', 'room', 'reviewer'])
            ->where('status', 'approved')
            ->latest('updated_at')
            ->paginate(10);
    }
}
