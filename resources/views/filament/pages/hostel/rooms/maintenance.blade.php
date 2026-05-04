<x-filament-panels::page>
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-950 dark:text-white">Maintenance</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Rooms currently under maintenance</p>
            </div>
            <button class="rounded-lg bg-orange-500 px-4 py-2 text-sm font-medium text-white">Add to Maintenance</button>
        </div>

        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm dark:divide-gray-800">
                    <thead class="bg-gray-50 dark:bg-gray-950/40">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs text-gray-500">Room No</th>
                            <th class="px-4 py-3 text-left text-xs text-gray-500">Floor</th>
                            <th class="px-4 py-3 text-left text-xs text-gray-500">Type</th>
                            <th class="px-4 py-3 text-left text-xs text-gray-500">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        <tr>
                            <td class="px-4 py-3">B-201</td>
                            <td class="px-4 py-3">2</td>
                            <td class="px-4 py-3">Non-AC</td>
                            <td class="px-4 py-3"><button class="rounded bg-emerald-600 px-2 py-1 text-xs text-white">Mark Available</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>
