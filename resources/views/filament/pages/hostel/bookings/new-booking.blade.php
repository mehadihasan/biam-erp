<x-filament-panels::page>
    <div class="space-y-4">
        <div>
            <h2 class="text-xl font-bold text-gray-950 dark:text-white">New Booking</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Create a new room reservation</p>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <select class="rounded-lg border-gray-300 text-sm dark:border-gray-700 dark:bg-gray-800"><option>Select Guest</option></select>
                <select class="rounded-lg border-gray-300 text-sm dark:border-gray-700 dark:bg-gray-800"><option>Room Type</option><option>VIP</option><option>AC</option><option>Non-AC</option></select>
                <select class="rounded-lg border-gray-300 text-sm dark:border-gray-700 dark:bg-gray-800"><option>Select Room</option></select>
                <input type="number" placeholder="Number of Rooms" class="rounded-lg border-gray-300 text-sm dark:border-gray-700 dark:bg-gray-800">
                <input type="date" class="rounded-lg border-gray-300 text-sm dark:border-gray-700 dark:bg-gray-800">
                <input type="date" class="rounded-lg border-gray-300 text-sm dark:border-gray-700 dark:bg-gray-800">
            </div>
            <div class="mt-6 rounded-lg bg-gray-50 p-4 text-sm dark:bg-gray-800/70">
                Duration, multiplier, booking money, and total rent preview will be shown here.
            </div>
            <div class="mt-6 flex gap-3">
                <button class="rounded-lg bg-amber-500 px-6 py-2 text-sm font-medium text-white">Create Booking</button>
                <a href="{{ \App\Filament\Pages\Hostel\Bookings\AllBookings::getUrl(panel: 'admin') }}" class="rounded-lg border px-6 py-2 text-sm">Cancel</a>
            </div>
        </div>
    </div>
</x-filament-panels::page>
