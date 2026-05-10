<x-filament-panels::page>
    @php
        $bookings = $this->getBookings();
    @endphp

    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-950 dark:text-white">All Bookings</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Manage reservations and booking status</p>
            </div>
            <x-filament::button
                tag="a"
                :href="\App\Filament\Pages\Hostel\Bookings\NewBooking::getUrl(panel: 'admin')"
            >
                New Booking
            </x-filament::button>
        </div>

        @if (session('success'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

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
                        @forelse ($bookings as $booking)
                            <tr>
                                <td class="px-4 py-3 font-mono text-xs">BKG-{{ str_pad((string) $booking->id, 4, '0', STR_PAD_LEFT) }}</td>
                                <td class="px-4 py-3">{{ $booking->user?->name ?: '-' }}</td>
                                <td class="px-4 py-3">{{ $booking->room?->room_number ?: '-' }} ({{ $booking->room_type }})</td>
                                <td class="px-4 py-3">{{ $booking->check_in_date?->toDateString() }}</td>
                                <td class="px-4 py-3">{{ $booking->check_out_date?->toDateString() }}</td>
                                <td class="px-4 py-3"><span class="rounded bg-amber-100 px-2 py-1 text-xs text-amber-700">{{ $booking->status }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500">No bookings found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div>
            {{ $bookings->links() }}
        </div>
    </div>
</x-filament-panels::page>
