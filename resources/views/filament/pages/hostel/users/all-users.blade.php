<x-filament-panels::page>
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-950 dark:text-white">User Management</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Manage all system users</p>
            </div>
            <a href="{{ \App\Filament\Pages\Hostel\Users\NewUser::getUrl(panel: 'admin') }}"
               class="rounded-lg bg-amber-500 px-4 py-2 text-sm font-medium text-white">
                Add New User
            </a>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <div class="grid grid-cols-1 gap-3 md:grid-cols-4">
                <input type="text" placeholder="Search by name or email..." class="rounded-lg border-gray-300 text-sm dark:border-gray-700 dark:bg-gray-800">
                <select class="rounded-lg border-gray-300 text-sm dark:border-gray-700 dark:bg-gray-800"><option>All Roles</option></select>
                <select class="rounded-lg border-gray-300 text-sm dark:border-gray-700 dark:bg-gray-800"><option>All Categories</option></select>
                <select class="rounded-lg border-gray-300 text-sm dark:border-gray-700 dark:bg-gray-800"><option>All Status</option></select>
            </div>
        </div>

        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm dark:divide-gray-800">
                    <thead class="bg-gray-50 dark:bg-gray-950/40">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs text-gray-500">#</th>
                            <th class="px-4 py-3 text-left text-xs text-gray-500">Name</th>
                            <th class="px-4 py-3 text-left text-xs text-gray-500">Email</th>
                            <th class="px-4 py-3 text-left text-xs text-gray-500">Role</th>
                            <th class="px-4 py-3 text-left text-xs text-gray-500">Category</th>
                            <th class="px-4 py-3 text-left text-xs text-gray-500">Eligibility</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        <tr>
                            <td class="px-4 py-3">1</td>
                            <td class="px-4 py-3 font-medium">Demo Guest</td>
                            <td class="px-4 py-3">guest@biam.gov.bd</td>
                            <td class="px-4 py-3"><span class="rounded bg-slate-100 px-2 py-1 text-xs">guest</span></td>
                            <td class="px-4 py-3">bcs_officer</td>
                            <td class="px-4 py-3"><span class="rounded bg-emerald-100 px-2 py-1 text-xs text-emerald-700">verified</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>
