<x-filament-panels::page>
    <style>
        .history-card {
            overflow: hidden;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            background: #ffffff;
        }

        .history-search-wrap {
            border-bottom: 1px solid #cbd5e1;
            padding: 24px;
        }

        .history-search {
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

        .history-search input {
            width: 100%;
            border: 0;
            background: transparent;
            color: #001b33;
            font-size: 16px;
            outline: none;
        }

        .history-table {
            min-width: 1180px;
            width: 100%;
            border-collapse: collapse;
            color: #001b33;
            font-size: 16px;
        }

        .history-table th {
            background: #f1f5f9;
            color: #526783;
            font-weight: 600;
        }

        .history-table th,
        .history-table td {
            border-bottom: 1px solid #cbd5e1;
            padding: 18px 24px;
            text-align: left;
            vertical-align: middle;
        }

        .history-badge {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            background: #e9edf3;
            padding: 5px 14px;
            color: #526783;
            font-size: 14px;
            font-weight: 600;
        }

        .history-status {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            background: #fef3c7;
            padding: 5px 12px;
            color: #92400e;
            font-size: 14px;
            font-weight: 600;
        }

        .history-status--served {
            background: #dcfce7;
            color: #166534;
        }

        .history-status--cancelled {
            background: #fee2e2;
            color: #991b1b;
        }
    </style>

    @php
        $orders = $this->getOrders();
    @endphp

    <div class="space-y-8">

        <div class="history-card dark:border-gray-800 dark:bg-gray-900">
            <div class="history-search-wrap">
                <label class="history-search">
                    <x-filament::icon icon="heroicon-o-magnifying-glass" class="h-6 w-6 text-slate-500" />
                    <input wire:model.live.debounce.300ms="search" type="search" placeholder="Search by guest or coupon...">
                </label>
            </div>

            <div class="overflow-x-auto">
                <table class="history-table">
                    <thead>
                        <tr>
                            <th>Ref</th>
                            <th>Guest</th>
                            <th>Room</th>
                            <th>Meal</th>
                            <th>Date</th>
                            <th>Item</th>
                            <th>Qty</th>
                            <th>Total</th>
                            <th>Coupon</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                            @php
                                $room = $order->guest?->activeBooking?->room?->room_number;
                                $statusClass = match ($order->status) {
                                    'served' => 'history-status--served',
                                    'cancelled' => 'history-status--cancelled',
                                    default => '',
                                };
                            @endphp
                            <tr>
                                <td class="font-mono text-sm">{{ $order->display_ref }}</td>
                                <td>{{ $order->guest?->name ?: '-' }}</td>
                                <td>{{ $room ?: '-' }}</td>
                                <td><span class="history-badge">{{ $order->meal_type }}</span></td>
                                <td>{{ $order->order_date?->toFormattedDateString() ?: '-' }}</td>
                                <td>{{ $order->menuItem?->name ?: $order->menu_item }}</td>
                                <td>{{ $order->quantity }}</td>
                                <td>৳{{ number_format($order->display_total, 0) }}</td>
                                <td class="font-mono text-sm">{{ $order->coupon_code ?: '-' }}</td>
                                <td><span class="history-status {{ $statusClass }}">{{ $order->status }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-gray-500">No meal orders found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div>
            {{ $orders->links() }}
        </div>
    </div>
</x-filament-panels::page>
