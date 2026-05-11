<x-filament-panels::page>
    <div class="space-y-4">
        <div>
            <h2 class="text-xl font-bold text-gray-950 dark:text-white">Eligibility Verification</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Review and verify pending users</p>
        </div>

        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm dark:divide-gray-800">
                    <thead class="bg-gray-50 dark:bg-gray-950/40">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs text-gray-500">Name</th>
                            <th class="px-4 py-3 text-left text-xs text-gray-500">Category</th>
                            <th class="px-4 py-3 text-left text-xs text-gray-500">Submitted</th>
                            <th class="px-4 py-3 text-left text-xs text-gray-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        <tr>
                            <td class="px-4 py-3">Pending User</td>
                            <td class="px-4 py-3">family_member</td>
                            <td class="px-4 py-3">2026-05-03</td>
                            <td class="px-4 py-3 space-x-2">
                                <button class="rounded bg-emerald-600 px-2 py-1 text-xs text-white">Verify</button>
                                <button class="rounded bg-red-600 px-2 py-1 text-xs text-white">Reject</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>
