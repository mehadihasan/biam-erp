@php
    $portalRoutePrefix = session('guest_verified') === true && session('cadre_auth') !== true ? 'guest' : 'cadre';
@endphp

<aside class="bcs-sidebar" aria-label="{{ __('Portal navigation') }}" data-bcs-sidebar>
    <div class="bcs-sidebar__header">
        <button class="bcs-sidebar__toggle" type="button" aria-label="{{ __('Toggle sidebar') }}" aria-expanded="true" data-bcs-sidebar-toggle>
            <svg class="bcs-sidebar__toggle-icon bcs-sidebar__toggle-icon--collapse" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg>
            <svg class="bcs-sidebar__toggle-icon bcs-sidebar__toggle-icon--expand" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m9 18 6-6-6-6"></path></svg>
        </button>
    </div>

    <nav class="bcs-sidebar__nav">
        <a href="{{ route($portalRoutePrefix.'.booking') }}" wire:navigate title="{{ __('Booking & Reservation') }}" class="bcs-sidebar__link{{ $activeMenu === 'booking' ? ' bcs-sidebar__link--active' : '' }}">
            <span class="bcs-sidebar__icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"></rect><path d="M16 2v4M8 2v4M3 10h18M8 14h4M8 18h8"></path></svg>
            </span>
            <span class="bcs-sidebar__label">{{ __('Booking & Reservation') }}</span>
        </a>
        <a href="{{ route($portalRoutePrefix.'.meals') }}" wire:navigate title="{{ __('Meal Order') }}" class="bcs-sidebar__link{{ $activeMenu === 'meal' ? ' bcs-sidebar__link--active' : '' }}">
            <span class="bcs-sidebar__icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m16 2-2.5 2.5M21 7l-2.5 2.5M9 11l10 10M5 2v7a4 4 0 0 0 4 4h0a4 4 0 0 0 4-4V2M9 2v20"></path></svg>
            </span>
            <span class="bcs-sidebar__label">{{ __('Meal Order') }}</span>
        </a>
        <a href="{{ route($portalRoutePrefix.'.feedback') }}" wire:navigate title="{{ __('Feedback') }}" class="bcs-sidebar__link{{ $activeMenu === 'feedback' ? ' bcs-sidebar__link--active' : '' }}">
            <span class="bcs-sidebar__icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a4 4 0 0 1-4 4H7l-4 4V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z"></path></svg>
            </span>
            <span class="bcs-sidebar__label">{{ __('Feedback') }}</span>
        </a>
        <a href="{{ route($portalRoutePrefix.'.billing') }}" wire:navigate title="{{ __('Billing') }}" class="bcs-sidebar__link{{ $activeMenu === 'billing' ? ' bcs-sidebar__link--active' : '' }}">
            <span class="bcs-sidebar__icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="5" width="18" height="14" rx="2"></rect><path d="M3 10h18"></path></svg>
            </span>
            <span class="bcs-sidebar__label">{{ __('Billing') }}</span>
        </a>
    </nav>
</aside>
