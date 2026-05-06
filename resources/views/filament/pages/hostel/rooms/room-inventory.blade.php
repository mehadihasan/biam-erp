<x-filament-panels::page>
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-950 dark:text-white">Room Inventory</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Manage all hostel rooms</p>
            </div>
            <x-filament::button
                tag="a"
                :href="\App\Filament\Pages\Hostel\Rooms\NewRoom::getUrl(panel: 'admin')"
            >
                Add Room
            </x-filament::button>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ([['A-101','VIP','available'],['A-102','AC','occupied'],['B-201','Non-AC','maintenance'],['C-301','VIP','available']] as $room)
                <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                    <div class="flex items-center justify-between">
                        <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $room[0] }}</p>
                        <span class="rounded bg-slate-100 px-2 py-1 text-xs">{{ $room[1] }}</span>
                    </div>
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Status: {{ $room[2] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</x-filament-panels::page>
