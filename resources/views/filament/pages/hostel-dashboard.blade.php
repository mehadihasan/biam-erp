<x-filament-panels::page>
    <div class="space-y-6">
        <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <h1 class="text-2xl font-bold text-gray-950 dark:text-white">Dashboard</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Overview of BIAM Hostel operations</p>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ($kpis as $kpi)
                <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                    <div class="flex items-center gap-4">
                        <div class="rounded-lg bg-gray-100 p-3 dark:bg-gray-800">
                            <x-filament::icon :icon="$kpi['icon']" class="h-6 w-6 {{ $kpi['color'] }}" />
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-950 dark:text-white">{{ $kpi['value'] }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $kpi['label'] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <h3 class="mb-4 text-sm font-medium text-gray-900 dark:text-white">Room Status Grid</h3>
            <div class="grid grid-cols-2 gap-2 sm:grid-cols-4 lg:grid-cols-8">
                @foreach ($rooms as $room)
                    @php
                        $statusClass = match ($room['status']) {
                            'available' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300',
                            'occupied' => 'bg-red-100 text-red-700 dark:bg-red-500/15 dark:text-red-300',
                            default => 'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300',
                        };
                    @endphp
                    <div class="rounded-lg p-3 text-center text-xs font-medium {{ $statusClass }}">
                        <p class="font-bold">{{ $room['number'] }}</p>
                        <p class="text-[10px]">{{ $room['type'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 xl:grid-cols-3">
            <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                <h3 class="mb-4 text-sm font-medium text-gray-900 dark:text-white">Room Type Distribution</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex items-center justify-between rounded bg-amber-50 px-3 py-2 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300">
                        <span>VIP</span>
                        <span class="font-semibold">22</span>
                    </div>
                    <div class="flex items-center justify-between rounded bg-sky-50 px-3 py-2 text-sky-700 dark:bg-sky-500/15 dark:text-sky-300">
                        <span>AC</span>
                        <span class="font-semibold">58</span>
                    </div>
                    <div class="flex items-center justify-between rounded bg-slate-100 px-3 py-2 text-slate-700 dark:bg-slate-500/15 dark:text-slate-300">
                        <span>Non-AC</span>
                        <span class="font-semibold">40</span>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-4 xl:col-span-2 dark:border-gray-800 dark:bg-gray-900">
                <h3 class="mb-4 text-sm font-medium text-gray-900 dark:text-white">Today's Meals</h3>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                    @foreach ($mealStats as $meal)
                        <div class="rounded-lg bg-gray-100 p-4 text-center dark:bg-gray-800">
                            <p class="text-2xl font-bold text-gray-950 dark:text-white">{{ $meal['count'] }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $meal['label'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                <div class="border-b border-gray-100 p-4 dark:border-gray-800">
                    <h3 class="text-sm font-medium text-gray-900 dark:text-white">Recent Bookings</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 text-sm dark:divide-gray-800">
                        <thead class="bg-gray-50 dark:bg-gray-950/40">
                            <tr>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Ref</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Guest</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Room</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Check-in</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @foreach ($recentBookings as $booking)
                                <tr>
                                    <td class="px-4 py-3 font-mono text-xs text-gray-700 dark:text-gray-300">{{ $booking['ref'] }}</td>
                                    <td class="px-4 py-3 text-gray-900 dark:text-white">{{ $booking['guest'] }}</td>
                                    <td class="px-4 py-3 text-gray-900 dark:text-white">{{ $booking['room'] }}</td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $booking['checkin'] }}</td>
                                    <td class="px-4 py-3">
                                        <span class="rounded-full bg-amber-100 px-2 py-1 text-xs font-medium text-amber-700 dark:bg-amber-500/15 dark:text-amber-300">
                                            {{ str_replace('_', ' ', $booking['status']) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                <div class="border-b border-gray-100 p-4 dark:border-gray-800">
                    <h3 class="text-sm font-medium text-gray-900 dark:text-white">Pending Approvals</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 text-sm dark:divide-gray-800">
                        <thead class="bg-gray-50 dark:bg-gray-950/40">
                            <tr>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Guest</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Room</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Date</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @foreach ($pendingApprovals as $approval)
                                <tr>
                                    <td class="px-4 py-3 text-gray-900 dark:text-white">{{ $approval['guest'] }}</td>
                                    <td class="px-4 py-3 text-gray-900 dark:text-white">{{ $approval['room'] }}</td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $approval['date'] }}</td>
                                    <td class="px-4 py-3">
                                        <button type="button" class="rounded-md bg-emerald-600 px-2 py-1 text-xs font-medium text-white">Approve</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>        
    </div>
</x-filament-panels::page>
