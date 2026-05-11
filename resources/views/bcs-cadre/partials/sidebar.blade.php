<aside class="bcs-sidebar" aria-label="{{ __('Cadre navigation') }}">
    <nav class="bcs-sidebar__nav">
        <a href="{{ route('cadre.booking') }}" class="bcs-sidebar__link{{ $activeMenu === 'booking' ? ' bcs-sidebar__link--active' : '' }}">
            <span class="bcs-sidebar__icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"></rect><path d="M16 2v4M8 2v4M3 10h18M8 14h4M8 18h8"></path></svg>
            </span>
            {{ __('Booking & Reservation') }}
        </a>
        <a href="{{ route('cadre.meals') }}" class="bcs-sidebar__link{{ $activeMenu === 'meal' ? ' bcs-sidebar__link--active' : '' }}">
            <span class="bcs-sidebar__icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m16 2-2.5 2.5M21 7l-2.5 2.5M9 11l10 10M5 2v7a4 4 0 0 0 4 4h0a4 4 0 0 0 4-4V2M9 2v20"></path></svg>
            </span>
            {{ __('Meal Order') }}
        </a>
        <a href="{{ route('cadre.feedback') }}" class="bcs-sidebar__link{{ $activeMenu === 'feedback' ? ' bcs-sidebar__link--active' : '' }}">
            <span class="bcs-sidebar__icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a4 4 0 0 1-4 4H7l-4 4V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z"></path></svg>
            </span>
            {{ __('Feedback') }}
        </a>
        <a href="{{ route('cadre.billing') }}" class="bcs-sidebar__link{{ $activeMenu === 'billing' ? ' bcs-sidebar__link--active' : '' }}">
            <span class="bcs-sidebar__icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="5" width="18" height="14" rx="2"></rect><path d="M3 10h18"></path></svg>
            </span>
            {{ __('Billing') }}
        </a>
    </nav>
</aside>
