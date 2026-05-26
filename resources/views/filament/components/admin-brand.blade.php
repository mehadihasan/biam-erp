@props([
    'name' => 'BIAM',
])

<div class="fi-admin-brand flex items-center gap-2.5">
    <img
        src="{{ asset('images/biam-logo.png') }}"
        alt="{{ $name }}"
        class="fi-admin-brand__logo h-8 w-8 shrink-0 object-contain"
    >
    <span class="fi-admin-brand__name text-base font-bold leading-none tracking-tight">{{ $name }}</span>
</div>
