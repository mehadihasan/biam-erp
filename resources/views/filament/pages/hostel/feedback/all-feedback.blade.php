<x-filament-panels::page>
    <style>
        .feedback-card {
            overflow: hidden;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            background: #ffffff;
        }

        .feedback-search-wrap {
            border-bottom: 1px solid #cbd5e1;
            padding: 24px;
        }

        .feedback-search {
            display: flex;
            align-items: center;
            gap: 14px;
            max-width: 576px;
            height: 56px;
            border: 1px solid #cbd5e1;
            border-radius: 9px;
            background: #f8fafc;
            padding: 0 18px;
        }

        .feedback-search input {
            width: 100%;
            border: 0;
            background: transparent;
            color: #001b33;
            font-size: 16px;
            outline: none;
        }

        .feedback-table {
            min-width: 1180px;
            width: 100%;
            border-collapse: collapse;
            color: #001b33;
            font-size: 15px;
        }

        .feedback-table th {
            background: #f1f5f9;
            color: #526783;
            font-weight: 700;
        }

        .feedback-table th,
        .feedback-table td {
            border-bottom: 1px solid #cbd5e1;
            padding: 16px 18px;
            text-align: left;
            vertical-align: top;
        }

        .feedback-ratings {
            display: grid;
            grid-template-columns: repeat(2, minmax(260px, 1fr));
            gap: 8px 16px;
        }

        .feedback-rating {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            border-radius: 8px;
            background: #f8fafc;
            padding: 8px 10px;
        }

        .feedback-rating strong {
            color: #001b33;
            font-weight: 700;
        }

        .feedback-rating span {
            color: #526783;
            font-weight: 700;
            white-space: nowrap;
        }
    </style>

    @php
        $feedbackItems = $this->getFeedbackItems();
    @endphp

    <div class="space-y-8">
        <div class="feedback-card dark:border-gray-800 dark:bg-gray-900">
            <div class="feedback-search-wrap">
                <label class="feedback-search">
                    <x-filament::icon icon="heroicon-o-magnifying-glass" class="h-6 w-6 text-slate-500" />
                    <input wire:model.live.debounce.300ms="search" type="search" placeholder="Search by submitter...">
                </label>
            </div>

            <div class="overflow-x-auto">
                <table class="feedback-table">
                    <thead>
                        <tr>
                            <th>Submitter</th>
                            <th>Type</th>
                            <th>Ratings</th>
                            <th>Status</th>
                            <th>Submitted</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($feedbackItems as $feedback)
                            @php
                                $ratingMap = $feedback->ratingMap();
                            @endphp
                            <tr>
                                <td>{{ $feedback->submitter_name }}</td>
                                <td>{{ ucfirst($feedback->submitter_type ?: 'guest') }}</td>
                                <td>
                                    <div class="feedback-ratings">
                                        @foreach ($this->categories() as $category)
                                            <div class="feedback-rating">
                                                <strong>{{ $category }}</strong>
                                                <span>{{ $ratingMap[$category] ?? '-' }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </td>
                                <td>{{ ucfirst($feedback->status) }}</td>
                                <td>{{ $feedback->created_at?->format('Y-m-d h:i A') ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-gray-500">No feedback found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div>
            {{ $feedbackItems->links() }}
        </div>
    </div>
</x-filament-panels::page>
