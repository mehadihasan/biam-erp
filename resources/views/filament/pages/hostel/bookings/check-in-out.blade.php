<x-filament-panels::page>
    <style>
        .check-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .check-tab {
            height: 38px;
            border: 1px solid #cbd5e1;
            border-radius: 7px;
            background: #ffffff;
            padding: 0 16px;
            color: #001b33;
            font-size: 14px;
            font-weight: 500;
        }

        .check-tab--active {
            border-color: #173c63;
            background: #173c63;
            color: #ffffff;
        }

        .check-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            padding: 5px 12px;
            color: #ffffff;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
        }

        .check-action--in {
            background: #207a36;
        }

        .check-action--out {
            background: #1f62b5;
        }
    </style>

    @php
        $pendingBookings = $this->pendingBookings;
        $activeBookings = $this->activeBookings;
        $currentBookings = $activeTab === 'active' ? $activeBookings : $pendingBookings;
    @endphp

    <div class="space-y-4">
        <div>
            <h2 class="text-xl font-bold text-gray-950 dark:text-white">Check-in / Check-out</h2>
        </div>

        @if (session('success'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="check-tabs">
            <button
                type="button"
                wire:click="setTab('pending')"
                class="check-tab {{ $activeTab === 'pending' ? 'check-tab--active' : '' }}"
            >
                Pending Check-in ({{ $pendingBookings->count() }})
            </button>
            <button
                type="button"
                wire:click="setTab('active')"
                class="check-tab {{ $activeTab === 'active' ? 'check-tab--active' : '' }}"
            >
                Active Stays ({{ $activeBookings->count() }})
            </button>
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
                            <th class="px-4 py-3 text-left text-xs text-gray-500">Duration</th>
                            <th class="px-4 py-3 text-left text-xs text-gray-500">Total Rent</th>
                            <th class="px-4 py-3 text-left text-xs text-gray-500">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse ($currentBookings as $booking)
                            <tr>
                                <td class="px-4 py-3 font-mono text-xs">{{ $this->bookingRef($booking) }}</td>
                                <td class="px-4 py-3">{{ $booking->user?->name ?: '-' }}</td>
                                <td class="px-4 py-3">{{ $this->roomLabel($booking) }}</td>
                                <td class="px-4 py-3">{{ $booking->check_in_date?->toDateString() }}</td>
                                <td class="px-4 py-3">{{ $booking->check_out_date?->toDateString() }}</td>
                                <td class="px-4 py-3">{{ $booking->duration_nights }} {{ \Illuminate\Support\Str::plural('night', $booking->duration_nights) }}</td>
                                <td class="px-4 py-3">BDT {{ number_format((float) $booking->total_rent, 0) }}</td>
                                <td class="px-4 py-3">
                                    @if ($activeTab === 'pending')
                                        @if ($this->canCheckIn($booking))
                                            <form method="post" action="{{ route('check-in-out.check-in', $booking) }}">
                                                @csrf
                                                @method('patch')
                                                <button type="submit" class="check-action check-action--in">Check In</button>
                                            </form>
                                        @else
                                            <span class="text-xs text-gray-500">Available on booking date</span>
                                        @endif
                                    @else
                                        <form method="post" action="{{ route('check-in-out.check-out', $booking) }}">
                                            @csrf
                                            @method('patch')
                                            <button type="submit" class="check-action check-action--out">Check Out</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                    {{ $activeTab === 'pending' ? 'No pending check-ins found.' : 'No active stays found.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>
