<x-filament-panels::page>
    <style>
        .approval-table {
            overflow: hidden;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: #ffffff;
        }

        .approval-table table {
            min-width: 100%;
            border-collapse: collapse;
            font-size: 14px;
            color: #001b33;
        }

        .approval-table th {
            background: #f1f3f5;
            padding: 14px 20px;
            color: #536b87;
            font-weight: 500;
            text-align: left;
            white-space: nowrap;
        }

        .approval-table td {
            border-top: 1px solid #cbd5e1;
            padding: 14px 20px;
            vertical-align: middle;
            white-space: nowrap;
        }

        .approval-ref {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", monospace;
            font-size: 12px;
        }

        .approval-badge {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            background: #e6eaf0;
            padding: 3px 12px;
            color: #536b87;
            font-size: 12px;
            line-height: 1.4;
        }

        .approval-muted {
            display: inline-block;
            width: 24px;
            height: 4px;
            border-radius: 999px;
            background: #e1e5eb;
        }

        .approval-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
        }

        .approval-action {
            border: 1px solid transparent;
            border-radius: 4px;
            padding: 5px 10px;
            font-size: 12px;
            font-weight: 600;
            line-height: 1.2;
            white-space: nowrap;
        }

        .approval-action--approve {
            background: #23823d;
            color: #ffffff;
        }

        .approval-action--reject {
            background: #d71920;
            color: #ffffff;
        }

        .approval-action--escalate {
            border-color: #cbd5e1;
            background: #ffffff;
            color: #001b33;
        }

        @media (max-width: 768px) {
            .approval-table th,
            .approval-table td {
                padding: 12px 14px;
            }
        }
    </style>

    @php
        $bookings = $this->getBookings();
    @endphp

    <div class="space-y-7">
        <div>
            <h2 class="text-2xl font-bold text-gray-950 dark:text-white">Pending Approvals</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $bookings->total() }} bookings awaiting approval</p>
        </div>

        <div class="approval-table">
            <div class="overflow-x-auto">
                <table>
                    <thead>
                        <tr>
                            <th>Ref</th>
                            <th>Guest</th>
                            <th>Room</th>
                            <th>Check-in</th>
                            <th>Category</th>
                            <th>Submitted</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($bookings as $booking)
                            <tr>
                                <td class="approval-ref">{{ $this->bookingRef($booking) }}</td>
                                <td>{{ $booking->user?->name ?: '-' }}</td>
                                <td>{{ $this->roomLabel($booking) }}</td>
                                <td>{{ $booking->check_in_date?->toDateString() ?: '-' }}</td>
                                <td>
                                    @if ($this->categoryLabel($booking))
                                        <span class="approval-badge">{{ $this->categoryLabel($booking) }}</span>
                                    @else
                                        <span class="approval-muted" aria-label="No category"></span>
                                    @endif
                                </td>
                                <td>{{ $booking->created_at?->format('d/m/Y') ?: '-' }}</td>
                                <td>
                                    <div class="approval-actions">
                                        <button type="button" wire:click="approve({{ $booking->id }})" class="approval-action approval-action--approve">
                                            Approve
                                        </button>
                                        <button type="button" wire:click="reject({{ $booking->id }})" wire:confirm="Reject this booking?" class="approval-action approval-action--reject">
                                            Reject
                                        </button>
                                        <button type="button" wire:click="escalate({{ $booking->id }})" class="approval-action approval-action--escalate" @disabled($booking->status === 'escalated')>
                                            Escalate
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-gray-500">No pending approvals found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{ $bookings->links() }}
    </div>
</x-filament-panels::page>
