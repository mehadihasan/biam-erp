<x-filament-panels::page>
    <div class="space-y-4">
        <div>
            <h2 class="text-xl font-bold text-gray-950 dark:text-white">Add New User</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Create or edit a hostel user profile</p>
        </div>

        <div class="max-w-4xl rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <input type="text" placeholder="Full Name" class="rounded-lg border-gray-300 text-sm dark:border-gray-700 dark:bg-gray-800">
                <input type="email" placeholder="Email" class="rounded-lg border-gray-300 text-sm dark:border-gray-700 dark:bg-gray-800">
                <input type="text" placeholder="Username" class="rounded-lg border-gray-300 text-sm dark:border-gray-700 dark:bg-gray-800">
                <input type="password" placeholder="Password" class="rounded-lg border-gray-300 text-sm dark:border-gray-700 dark:bg-gray-800">
                <select class="rounded-lg border-gray-300 text-sm dark:border-gray-700 dark:bg-gray-800"><option>guest</option><option>staff</option><option>admin</option></select>
                <select class="rounded-lg border-gray-300 text-sm dark:border-gray-700 dark:bg-gray-800"><option>bcs_officer</option><option>family_member</option><option>retired_officer</option></select>
            </div>
            <div class="mt-6 flex gap-3">
                <button class="rounded-lg bg-amber-500 px-6 py-2 text-sm font-medium text-white">Create User</button>
                <a href="{{ \App\Filament\Pages\Hostel\Users\AllUsers::getUrl(panel: 'admin') }}" class="rounded-lg border px-6 py-2 text-sm">Cancel</a>
            </div>
        </div>
    </div>
</x-filament-panels::page>
