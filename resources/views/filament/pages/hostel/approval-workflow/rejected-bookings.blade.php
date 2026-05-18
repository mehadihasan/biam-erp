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
    </style>

    @php
        $bookings = $this->getBookings();
    @endphp

    <div class="space-y-7">
        <h2 class="text-2xl font-bold text-gray-950 dark:text-white">Rejected Bookings</h2>

        <div class="approval-table">
            <div class="overflow-x-auto">
                <table>
                    <thead>
                        <tr>
                            <th>Ref</th>
                            <th>Guest</th>
                            <th>Room</th>
                            <th>Rejected By</th>
                            <th>Reason</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($bookings as $booking)
                            <tr>
                                <td class="approval-ref">{{ $this->bookingRef($booking) }}</td>
                                <td>{{ $booking->user?->name ?: '-' }}</td>
                                <td>{{ $booking->room?->room_number ?: '-' }}</td>
                                <td>{{ $booking->reviewer?->name ?: 'System' }}</td>
                                <td>{{ $booking->rejection_reason ?: 'Rejected' }}</td>
                                <td>{{ $booking->reviewed_at?->format('d/m/Y') ?: $booking->updated_at?->format('d/m/Y') ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-gray-500">No rejected bookings found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{ $bookings->links() }}
    </div>
</x-filament-panels::page>
