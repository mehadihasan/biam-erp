<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Create Designation') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-950">
    <main class="mx-auto w-full max-w-3xl px-4 py-8 sm:px-6">
        <div class="mb-6">
            <h1 class="text-2xl font-bold">{{ __('Create Designation') }}</h1>
        </div>

        <form method="post" action="{{ route('designations.store') }}" class="space-y-5 rounded-lg border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
            @csrf

            <label class="block space-y-1 text-sm font-medium">
                <span>{{ __('Name') }} <span class="text-red-600">*</span></span>
                <input name="name" type="text" value="{{ old('name') }}" class="w-full rounded-lg border-gray-300 text-sm">
                @error('name') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
            </label>

            <label class="block space-y-1 text-sm font-medium">
                <span>{{ __('Description') }}</span>
                <textarea name="description" rows="4" class="w-full rounded-lg border-gray-300 text-sm">{{ old('description') }}</textarea>
                @error('description') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
            </label>

            <div class="flex flex-col gap-3 sm:flex-row">
                <button class="rounded-lg bg-blue-950 px-6 py-2 text-sm font-medium text-white">{{ __('Save') }}</button>
                <a href="{{ route('designations.index') }}" class="rounded-lg border border-gray-300 px-6 py-2 text-center text-sm">{{ __('Cancel') }}</a>
            </div>
        </form>
    </main>
</body>
</html>
