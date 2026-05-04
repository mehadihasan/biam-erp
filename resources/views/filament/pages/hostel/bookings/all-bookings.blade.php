<x-filament-panels::page>
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-950 dark:text-white">All Bookings</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Manage reservations and booking status</p>
            </div>
            <a href="{{ \App\Filament\Pages\Hostel\Bookings\NewBooking::getUrl(panel: 'admin') }}"
               class="rounded-lg bg-amber-500 px-4 py-2 text-sm font-medium text-white">
                New Booking
            </a>
        </div>

        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm dark:divide-gray-800">
                    <thead class="bg-gray-50 dark:bg-gray-950/40">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs text-gray-500">Ref</th>
                            <th class="px-4 py-3 text-left text-xs text-gray-500">Guest</th>
                            <th class="px-4 py-3 text-left text-xs text-gray-500">Room</th>
                            <th class="px-4 py-3 text-left text-xs text-gray-500">Check-in</th>
                            <th class="px-4 py-3 text-left text-xs text-gray-500">Check-out</th>
                            <th class="px-4 py-3 text-left text-xs text-gray-500">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        <tr>
                            <td class="px-4 py-3 font-mono text-xs">BKG-1024</td>
                            <td class="px-4 py-3">Demo Guest</td>
                            <td class="px-4 py-3">A-101 (VIP)</td>
                            <td class="px-4 py-3">2026-05-04</td>
                            <td class="px-4 py-3">2026-05-06</td>
                            <td class="px-4 py-3"><span class="rounded bg-amber-100 px-2 py-1 text-xs text-amber-700">pending</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>
