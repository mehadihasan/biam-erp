<?php

namespace App\Filament\Pages\Hostel\ApprovalWorkflow;

use App\Models\GuestPendingApproval;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class RejectedBookings extends BaseApprovalPage
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-x-circle';

    protected static ?string $title = 'Rejected Bookings';

    protected static ?string $navigationLabel = 'Rejected';

    protected static ?string $slug = 'hostel/approval-workflow/rejected';

    protected static ?int $navigationSort = 36;

    protected string $view = 'filament.pages.hostel.approval-workflow.rejected-bookings';

    public function getBookings(): LengthAwarePaginator
    {
        return GuestPendingApproval::query()
            ->with(['user', 'room', 'reviewer'])
            ->where('status', 'rejected')
            ->latest('updated_at')
            ->paginate(10);
    }
}
