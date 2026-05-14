@props([
    'name',
    'logoutUrl',
    'initial' => null,
])

@php
    $displayName = trim((string) $name) !== '' ? (string) $name : __('User');
    $avatarInitial = $initial ?: mb_strtoupper(mb_substr($displayName, 0, 1));
@endphp

<details class="shared-user-menu">
    <summary class="shared-user-menu__trigger" aria-label="{{ __('Open user menu') }}">
        {{ $avatarInitial }}
    </summary>

    <div class="shared-user-menu__panel">
        <div class="shared-user-menu__identity">
            <span class="shared-user-menu__identity-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5Zm0 2c-4.42 0-8 2.24-8 5v1h16v-1c0-2.76-3.58-5-8-5Z"/></svg>
            </span>
            <span>{{ $displayName }}</span>
        </div>

        <div class="shared-user-menu__themes" role="group" aria-label="{{ __('Theme mode') }}">
            <button type="button" aria-label="{{ __('Light') }}" data-shared-theme="light">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="4"></circle><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"></path></svg>
            </button>
            <button type="button" aria-label="{{ __('Dark') }}" data-shared-theme="dark">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M21 14.7A8.8 8.8 0 0 1 9.3 3a7.8 7.8 0 1 0 11.7 11.7Z"></path></svg>
            </button>
            <button type="button" aria-label="{{ __('System') }}" data-shared-theme="system">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="12" rx="1.5"></rect><path d="M8 20h8M12 16v4"></path></svg>
            </button>
        </div>

        <form method="post" action="{{ $logoutUrl }}" class="shared-user-menu__logout">
            @csrf
            <button type="submit">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><path d="M16 17l5-5-5-5"></path><path d="M21 12H9"></path></svg>
                <span>{{ __('Sign out') }}</span>
            </button>
        </form>
    </div>
</details>

@once
    <script>
        (() => {
            const applyTheme = (theme) => {
                const resolvedTheme = theme || localStorage.getItem('theme') || 'system';
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

                localStorage.setItem('theme', resolvedTheme);
                document.documentElement.classList.toggle('dark', resolvedTheme === 'dark' || (resolvedTheme === 'system' && prefersDark));
                window.dispatchEvent(new CustomEvent('theme-changed', { detail: resolvedTheme }));

                document.querySelectorAll('[data-shared-theme]').forEach((button) => {
                    button.classList.toggle('is-active', button.dataset.sharedTheme === resolvedTheme);
                });
            };

            document.addEventListener('click', (event) => {
                const themeButton = event.target.closest('[data-shared-theme]');
                if (themeButton) {
                    applyTheme(themeButton.dataset.sharedTheme);
                    themeButton.closest('details')?.removeAttribute('open');
                    return;
                }

                document.querySelectorAll('.shared-user-menu[open]').forEach((menu) => {
                    if (! menu.contains(event.target)) {
                        menu.removeAttribute('open');
                    }
                });
            });

            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
                if ((localStorage.getItem('theme') || 'system') === 'system') {
                    applyTheme('system');
                }
            });

            applyTheme(localStorage.getItem('theme') || 'system');
        })();
    </script>
@endonce
