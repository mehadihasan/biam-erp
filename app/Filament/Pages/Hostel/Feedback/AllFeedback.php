<?php

namespace App\Filament\Pages\Hostel\Feedback;

use App\Filament\Pages\Hostel\BaseHostelPage;
use App\Models\Feedback;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Livewire\WithPagination;

class AllFeedback extends BaseHostelPage
{
    use WithPagination;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static string|\UnitEnum|null $navigationGroup = 'Feedback';

    protected static ?string $title = 'All Feedback';

    protected static ?string $navigationLabel = 'All Feedback';

    protected static ?string $slug = 'feedback/all';

    protected static ?int $navigationSort = 51;

    protected string $view = 'filament.pages.hostel.feedback.all-feedback';

    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function getFeedbackItems(): LengthAwarePaginator
    {
        return Feedback::query()
            ->with(['guest', 'ratings'])
            ->when(trim($this->search) !== '', function ($query): void {
                $search = '%'.trim($this->search).'%';

                $query->where(function ($subQuery) use ($search): void {
                    $subQuery
                        ->where('cadre_reference', 'like', $search)
                        ->orWhereHas('guest', fn ($guestQuery) => $guestQuery->where('name', 'like', $search));
                });
            })
            ->latest()
            ->paginate(10);
    }

    public function categories(): array
    {
        return Feedback::CATEGORIES;
    }
}
