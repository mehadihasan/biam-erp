<?php

namespace App\Filament\Pages\Hostel\ApprovalWorkflow;

use App\Models\GuestPendingApproval;
use App\Models\User;
use App\Services\BookingApprovalService;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PendingApprovals extends BaseApprovalPage
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-clock';

    protected static ?string $title = 'Pending Approvals';

    protected static ?string $navigationLabel = 'Pending Approvals';

    protected static ?string $slug = 'hostel/approval-workflow/pending';

    protected static ?int $navigationSort = 34;

    protected string $view = 'filament.pages.hostel.approval-workflow.pending-approvals';

    public function getBookings(): LengthAwarePaginator
    {
        return GuestPendingApproval::query()
            ->with(['user.designation', 'room'])
            ->whereIn('status', ['pending', 'escalated'])
            ->latest()
            ->paginate(10);
    }

    public function approve(int $approvalId, BookingApprovalService $approvalService): void
    {
        $approval = GuestPendingApproval::query()->whereIn('status', ['pending', 'escalated'])->findOrFail($approvalId);

        try {
            $approvalService->approveGuestPending($approval, $this->adminUser());
            Notification::make()->title('Booking approved successfully.')->success()->send();
        } catch (\Throwable $exception) {
            Notification::make()
                ->title('Booking could not be approved.')
                ->body($exception->getMessage())
                ->danger()
                ->send();
        }
    }

    public function reject(int $approvalId, BookingApprovalService $approvalService): void
    {
        $approval = GuestPendingApproval::query()->whereIn('status', ['pending', 'escalated'])->findOrFail($approvalId);
        $approvalService->rejectGuestPending($approval, $this->adminUser(), 'Rejected by admin');

        Notification::make()->title('Booking rejected successfully.')->success()->send();
    }

    public function escalate(int $approvalId, BookingApprovalService $approvalService): void
    {
        $approval = GuestPendingApproval::query()->where('status', 'pending')->findOrFail($approvalId);
        $approvalService->escalateGuestPending($approval, $this->adminUser());

        Notification::make()->title('Booking escalated successfully.')->success()->send();
    }

    private function adminUser(): ?User
    {
        $user = auth()->user();

        return $user instanceof User ? $user : null;
    }
}
