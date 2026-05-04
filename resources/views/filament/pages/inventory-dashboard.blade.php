<x-filament-panels::page>
    <div class="space-y-6">
        <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <h1 class="text-2xl font-bold text-gray-950 dark:text-white">Inventory Dashboard</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Overview of stock levels, alerts, and recent activity</p>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ($kpis as $kpi)
                <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                    <div class="flex items-start gap-4">
                        <div class="rounded-lg p-3 {{ $kpi['bgClass'] }}">
                            <x-filament::icon :icon="$kpi['icon']" class="h-6 w-6 {{ $kpi['colorClass'] }}" />
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $kpi['label'] }}</p>
                            <p class="text-2xl font-bold text-gray-950 dark:text-white">{{ $kpi['value'] }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $kpi['sub'] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if (count($alerts))
            <div class="flex items-center justify-between rounded-lg border border-orange-200 bg-orange-50 p-4 dark:border-orange-500/30 dark:bg-orange-500/10">
                <div>
                    <p class="font-semibold text-orange-800 dark:text-orange-300">
                        {{ count($alerts) }} items require attention
                    </p>
                    <p class="text-sm text-orange-700 dark:text-orange-200">
                        @foreach ($alerts as $alert)
                            {{ $alert['name'] }} ({{ $alert['current'] }}/{{ $alert['min'] }})@if (!$loop->last), @endif
                        @endforeach
                    </p>
                </div>
                <button type="button" class="rounded-lg bg-orange-600 px-4 py-2 text-sm font-medium text-white">
                    Acknowledge All
                </button>
            </div>
        @endif

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
            <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
                <h3 class="mb-4 text-sm font-semibold text-gray-900 dark:text-white">Stock by Category</h3>
                <div class="space-y-3">
                    @foreach ($categoryData as $category)
                        <div>
                            <div class="mb-1 flex justify-between text-xs text-gray-600 dark:text-gray-300">
                                <span>{{ $category['name'] }}</span>
                                <span>{{ $category['qty'] }}</span>
                            </div>
                            <div class="h-2 rounded-full bg-gray-100 dark:bg-gray-800">
                                <div class="h-2 rounded-full bg-teal-700" style="width: {{ min($category['qty'], 100) }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
                <h3 class="mb-4 text-sm font-semibold text-gray-900 dark:text-white">Recent Transactions</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 text-sm dark:divide-gray-800">
                        <thead class="bg-gray-50 dark:bg-gray-950/40">
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Ref</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Item</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Type</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Qty</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @foreach ($recentTransactions as $transaction)
                                @php
                                    $typeColor = match ($transaction['type']) {
                                        'stock_in' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300',
                                        'stock_out' => 'bg-red-100 text-red-700 dark:bg-red-500/15 dark:text-red-300',
                                        default => 'bg-orange-100 text-orange-700 dark:bg-orange-500/15 dark:text-orange-300',
                                    };
                                @endphp
                                <tr>
                                    <td class="px-3 py-2 font-mono text-xs text-gray-700 dark:text-gray-300">{{ $transaction['ref'] }}</td>
                                    <td class="px-3 py-2 text-gray-900 dark:text-white">{{ $transaction['item'] }}</td>
                                    <td class="px-3 py-2">
                                        <span class="rounded-full px-2 py-1 text-xs {{ $typeColor }}">
                                            {{ str_replace('_', ' ', $transaction['type']) }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2 text-gray-900 dark:text-white">{{ $transaction['qty'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
            <h3 class="mb-4 text-sm font-semibold text-gray-900 dark:text-white">Low Stock Items</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm dark:divide-gray-800">
                    <thead class="bg-gray-50 dark:bg-gray-950/40">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Code</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Name</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Qty</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Min</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach ($lowStockItems as $item)
                            <tr>
                                <td class="px-3 py-2 font-mono text-xs text-gray-700 dark:text-gray-300">{{ $item['code'] }}</td>
                                <td class="px-3 py-2 text-gray-900 dark:text-white">{{ $item['name'] }}</td>
                                <td class="px-3 py-2 font-semibold text-red-600">{{ $item['qty'] }}</td>
                                <td class="px-3 py-2 text-gray-900 dark:text-white">{{ $item['min'] }}</td>
                                <td class="px-3 py-2">
                                    <button type="button" class="rounded-md bg-teal-700 px-2 py-1 text-xs text-white">Reorder</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>
