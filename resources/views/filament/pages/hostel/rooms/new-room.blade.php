<x-filament-panels::page>
    <div class="space-y-4">
        <div>
            <h2 class="text-xl font-bold text-gray-950 dark:text-white">Add / Edit Room</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Create room records with type, rate, and status</p>
        </div>

        <div class="max-w-3xl rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <input type="text" placeholder="Room Number" class="rounded-lg border-gray-300 text-sm dark:border-gray-700 dark:bg-gray-800">
                <input type="number" placeholder="Floor" class="rounded-lg border-gray-300 text-sm dark:border-gray-700 dark:bg-gray-800">
                <select class="rounded-lg border-gray-300 text-sm dark:border-gray-700 dark:bg-gray-800"><option>VIP</option><option>AC</option><option>Non-AC</option></select>
                <input type="number" placeholder="Base Rate (BDT)" class="rounded-lg border-gray-300 text-sm dark:border-gray-700 dark:bg-gray-800">
            </div>
            <div class="mt-6 flex gap-3">
                <button class="rounded-lg bg-amber-500 px-6 py-2 text-sm font-medium text-white">Save Room</button>
                <a href="{{ \App\Filament\Pages\Hostel\Rooms\RoomInventory::getUrl(panel: 'admin') }}" class="rounded-lg border px-6 py-2 text-sm">Cancel</a>
            </div>
        </div>
    </div>
</x-filament-panels::page>
