<x-filament-panels::page>
    <style>
        .today-card {
            overflow: hidden;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            background: #ffffff;
            color: #001b33;
        }

        .today-card__head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            min-height: 110px;
            border-bottom: 1px solid #cbd5e1;
            padding: 28px 24px;
        }

        .today-card__title {
            font-size: 24px;
            font-weight: 600;
        }

        .today-card__time {
            color: #526783;
            font-size: 16px;
        }

        .today-card__count,
        .today-badge {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            background: #e9edf3;
            padding: 5px 16px;
            color: #526783;
            font-size: 15px;
            font-weight: 600;
            white-space: nowrap;
        }

        .today-card__body {
            min-height: 114px;
            padding: 20px 24px;
        }

        .today-empty {
            display: flex;
            min-height: 72px;
            align-items: center;
            justify-content: center;
            color: #526783;
            font-size: 20px;
        }

        .today-order {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 8px 16px;
            border-bottom: 1px solid #e5e7eb;
            padding: 12px 0;
            font-size: 14px;
        }

        .today-order:first-child {
            padding-top: 0;
        }

        .today-order:last-child {
            border-bottom: 0;
            padding-bottom: 0;
        }
    </style>

    <div class="space-y-8">
        <div>
            <p class="mt-2 text-xl text-gray-500 dark:text-gray-400">{{ $this->todayDate() }}</p>
        </div>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
            @foreach ($this->cards() as $card)
                <section class="today-card dark:border-gray-800 dark:bg-gray-900">
                    <div class="today-card__head">
                        <div>
                            <h2 class="today-card__title dark:text-white">{{ $card['label'] }}</h2>
                            <p class="today-card__time">{{ $card['time'] }}</p>
                        </div>
                        <span class="today-card__count">{{ $card['count'] }} {{ \Illuminate\Support\Str::plural('order', $card['count']) }}</span>
                    </div>
                    <div class="today-card__body">
                        @forelse ($card['orders'] as $order)
                            <div class="today-order dark:border-gray-800">
                                <div>
                                    <div class="font-semibold dark:text-white">{{ $order->guest?->name ?: 'Guest' }}</div>
                                    <div class="text-gray-500">{{ $order->menuItem?->name ?: $order->menu_item }} x {{ $order->quantity }}</div>
                                </div>
                                <span class="today-badge">{{ $order->status }}</span>
                            </div>
                        @empty
                            <div class="today-empty">No orders</div>
                        @endforelse
                    </div>
                </section>
            @endforeach
        </div>
    </div>
</x-filament-panels::page>
