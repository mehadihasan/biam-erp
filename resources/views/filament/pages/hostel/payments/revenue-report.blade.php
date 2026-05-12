<x-filament-panels::page>
    <style>
        .revenue-card,
        .revenue-chart-card {
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: #ffffff;
        }

        .revenue-card {
            min-height: 118px;
            padding: 28px 30px;
        }

        .revenue-card strong {
            display: block;
            color: #001b33;
            font-size: 30px;
            line-height: 1.1;
        }

        .revenue-card span {
            color: #526783;
            font-size: 14px;
        }

        .revenue-chart-card {
            padding: 20px;
        }

        .revenue-chart {
            position: relative;
            height: 254px;
            border-bottom: 1px solid #6b7280;
            border-left: 1px solid #6b7280;
            background-image: repeating-linear-gradient(to top, transparent 0, transparent 61px, #d7dbe1 62px, transparent 63px);
        }

        .revenue-bars {
            position: absolute;
            inset: 0 24px 0 34px;
            display: grid;
            grid-template-columns: repeat(3, minmax(90px, 1fr));
            align-items: end;
            gap: 72px;
        }

        .revenue-bar {
            min-height: 2px;
            border-radius: 4px 4px 0 0;
            background: #173c63;
        }

        .revenue-axis {
            position: absolute;
            left: 0;
            top: -10px;
            width: 34px;
            height: 100%;
            color: #526783;
            font-size: 14px;
        }

        .revenue-axis span {
            position: absolute;
            right: 8px;
            transform: translateY(-50%);
        }

        @media (max-width: 767px) {
            .revenue-bars {
                gap: 24px;
                grid-template-columns: repeat(3, minmax(54px, 1fr));
            }
        }
    </style>

    @php
        $stats = $this->stats();
        $rows = $this->chartRows();
        $max = max(1, (float) $rows->max('total'));
        $axisMax = max(100, (int) ceil($max / 500) * 500);
    @endphp

    <div class="space-y-6">

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
            <div class="revenue-card dark:border-gray-800 dark:bg-gray-900">
                <strong>৳{{ number_format($stats['total_revenue'], 0) }}</strong>
                <span>Total Revenue</span>
            </div>
            <div class="revenue-card dark:border-gray-800 dark:bg-gray-900">
                <strong>৳{{ number_format($stats['booking_money'], 0) }}</strong>
                <span>Booking Money</span>
            </div>
            <div class="revenue-card dark:border-gray-800 dark:bg-gray-900">
                <strong>{{ number_format($stats['transactions']) }}</strong>
                <span>Transactions</span>
            </div>
        </div>

        <section class="revenue-chart-card dark:border-gray-800 dark:bg-gray-900">
            <h2 class="mb-4 text-base font-semibold text-gray-950 dark:text-white">Revenue Overview</h2>
            <div class="revenue-chart">
                <div class="revenue-axis">
                    <span style="top: 0%">{{ number_format($axisMax) }}</span>
                    <span style="top: 25%">{{ number_format($axisMax * .75) }}</span>
                    <span style="top: 50%">{{ number_format($axisMax * .5) }}</span>
                    <span style="top: 75%">{{ number_format($axisMax * .25) }}</span>
                    <span style="top: 100%">0</span>
                </div>
                <div class="revenue-bars">
                    @foreach ($rows as $row)
                        <div title="{{ $row['label'] }}: ৳{{ number_format($row['total'], 0) }}" class="revenue-bar" style="height: {{ max(2, ($row['total'] / $axisMax) * 100) }}%"></div>
                    @endforeach
                </div>
            </div>
        </section>
    </div>
</x-filament-panels::page>
