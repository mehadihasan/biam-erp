<x-filament-panels::page>
    <div class="space-y-4">
        <div>
            <h2 class="text-xl font-bold text-gray-950 dark:text-white">Room Availability</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Check available rooms by date range</p>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                <input type="date" class="rounded-lg border-gray-300 text-sm dark:border-gray-700 dark:bg-gray-800">
                <input type="date" class="rounded-lg border-gray-300 text-sm dark:border-gray-700 dark:bg-gray-800">
                <button class="rounded-lg bg-amber-500 px-4 py-2 text-sm font-medium text-white">Check Availability</button>
            </div>
        </div>

        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm dark:divide-gray-800">
                    <thead class="bg-gray-50 dark:bg-gray-950/40">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs text-gray-500">Room</th>
                            <th class="px-4 py-3 text-left text-xs text-gray-500">Type</th>
                            <th class="px-4 py-3 text-left text-xs text-gray-500">Rate</th>
                            <th class="px-4 py-3 text-left text-xs text-gray-500">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        <tr>
                            <td class="px-4 py-3">A-101</td>
                            <td class="px-4 py-3">VIP</td>
                            <td class="px-4 py-3">BDT 5000</td>
                            <td class="px-4 py-3"><a href="{{ \App\Filament\Pages\Hostel\Bookings\NewBooking::getUrl(panel: 'admin') }}" class="rounded bg-amber-500 px-2 py-1 text-xs text-white">Book Now</a></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>
