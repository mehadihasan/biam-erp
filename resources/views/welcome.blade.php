<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'BIAM Hostel Management System') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet">
    @vite(['resources/css/welcome.css', 'resources/js/welcome.js'])
</head>
<body>
    @php
        $modalErrorKeys = ['otp', 'guest_otp'];
        $firstMainErrorKey = collect($errors->keys())->first(fn ($k) => ! in_array($k, $modalErrorKeys, true));
    @endphp
    <div class="landing">
        <aside class="panel-brand" aria-label="{{ __('Branding') }}">
            <div class="panel-brand__inner">
                <img
                    class="panel-brand__logo"
                    src="{{ asset('images/biam-logo.png') }}"
                    alt="{{ __('Bangladesh Institute of Administration and Management emblem') }}"
                    width="140"
                    height="140"
                >
                <h1 class="panel-brand__title">BIAM Hostel Management System</h1>
                <p class="panel-brand__subtitle">Bangladesh Institute of Administration and Management</p>
            </div>
            <p class="panel-brand__footer">Powered by Thinkflow Innovation.</p>
        </aside>

        <main class="panel-form">
            <header class="panel-form__header">
                <nav class="welcome-topnav" aria-label="{{ __('Quick links') }}">
                    @if ($panelView === 'cadre')
                        <a href="{{ route('home', ['view' => 'guest']) }}" class="welcome-topnav__link">{{ __('Guest login') }}</a>
                        <a href="{{ route('home', ['view' => 'staff']) }}" class="welcome-topnav__link">{{ __('Admin login') }}</a>
                    @elseif ($panelView === 'staff')
                        <a href="{{ route('home') }}" class="welcome-topnav__link">{{ __('BCS Cadre Login') }}</a>
                        <a href="{{ route('home', ['view' => 'guest']) }}" class="welcome-topnav__link">{{ __('Guest login') }}</a>
                    @else
                        <a href="{{ route('home') }}" class="welcome-topnav__link">{{ __('BCS Cadre Login') }}</a>
                        <a href="{{ route('home', ['view' => 'staff']) }}" class="welcome-topnav__link">{{ __('Admin login') }}</a>
                    @endif
                </nav>
            </header>
            <div class="panel-form__body">
                <div class="card">
                    @if ($firstMainErrorKey)
                        <div class="alert" role="alert">
                            {{ $errors->first($firstMainErrorKey) }}
                        </div>
                    @endif

                    @if (session('guest_application_success'))
                        <div class="alert alert--success" role="status">
                            {{ __('Your guest application has been submitted successfully.') }}
                        </div>
                    @endif

                    @if ($panelView === 'cadre')
                        <div class="card__title-row">
                            <span class="card__title-icon" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                                </svg>
                            </span>
                            <div>
                                <h2 class="card__heading">{{ __('BCS Cadre Login') }}</h2>
                                <p class="card__sub">{{ __('Sign in with your GEMS Cadre Reference') }}</p>
                            </div>
                        </div>

                        <form method="post" action="{{ route('cadre.login.store') }}" novalidate>
                            @csrf
                            <div class="field">
                                <label for="cadre_reference">{{ __('Cadre Reference ID') }}</label>
                                <input
                                    id="cadre_reference"
                                    name="cadre_reference"
                                    type="text"
                                    value="{{ old('cadre_reference', $demoCadreReference) }}"
                                    placeholder="{{ __('e.g. :ref', ['ref' => $demoCadreReference]) }}"
                                    inputmode="numeric"
                                    autocomplete="username"
                                    readonly
                                >
                            </div>
                            <button type="submit" class="btn-submit">{{ __('Submit') }}</button>
                        </form>

                        <div class="cadre-demo">
                            <div class="cadre-demo__head">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                                </svg>
                                {{ __('Demo Credentials') }}
                            </div>
                            <p class="cadre-demo__text">
                                {{ __('Cadre Reference:') }} <strong>{{ $demoCadreReference }}</strong>
                                <span aria-hidden="true"> | </span>
                                {{ __('OTP:') }} <strong>{{ $demoOtp }}</strong>
                            </p>
                            <button type="button" class="cadre-demo__autofill" data-welcome-cadre-autofill data-demo-cadre="{{ $demoCadreReference }}">
                                {{ __('Auto-fill') }}
                            </button>
                        </div>
                    @elseif ($panelView === 'guest')
                        <div class="card__title-row">
                            <span class="card__title-icon" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H9.75m8.25 0V4.5A2.25 2.25 0 0 0 15.75 2.25h-7.5A2.25 2.25 0 0 0 6 4.5v3.75m12 0V19.5A2.25 2.25 0 0 1 15.75 21.75h-7.5A2.25 2.25 0 0 1 6 19.5V8.25m12 0H6" />
                                </svg>
                            </span>
                            <div>
                                <h2 class="card__heading">{{ __('Guest Booking Application') }}</h2>
                                <p class="card__sub">{{ __('Submit your application to book a hostel room as a guest of a BCS officer.') }}</p>
                            </div>
                        </div>

                        @if (! session('guest_application_success'))
                            <form
                                method="post"
                                action="{{ route('guest.application.store') }}"
                                enctype="multipart/form-data"
                                novalidate
                            >
                                @csrf
                                <div class="field">
                                    <span class="field__label">{{ __('Upload your application scan copy') }}</span>
                                    <label class="guest-upload" for="guest_application_scan">
                                        <input
                                            id="guest_application_scan"
                                            class="guest-upload__input"
                                            type="file"
                                            name="application_scan"
                                            accept=".pdf,.jpg,.jpeg,.png,application/pdf,image/jpeg,image/png"
                                            required
                                        >
                                        <span class="guest-upload__ui" aria-hidden="true">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
                                            </svg>
                                            <span class="guest-upload__prompt">{{ __('Choose file (PDF/JPG/PNG)') }}</span>
                                            <span class="guest-upload__filename" data-guest-upload-filename hidden></span>
                                        </span>
                                    </label>
                                </div>
                                <div class="field">
                                    <label for="guest_cadre_reference">{{ __('Reference ID (BCS Cadre)') }}</label>
                                    <input
                                        id="guest_cadre_reference"
                                        name="guest_cadre_reference"
                                        type="text"
                                        value="{{ old('guest_cadre_reference', $demoCadreReference) }}"
                                        placeholder="{{ __('e.g. :ref', ['ref' => $demoCadreReference]) }}"
                                        inputmode="numeric"
                                        autocomplete="off"
                                    >
                                </div>
                                <button type="submit" class="btn-submit">{{ __('Submit Application') }}</button>
                            </form>

                            <div class="cadre-demo">
                                <div class="cadre-demo__head">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                                    </svg>
                                    {{ __('Demo') }}
                                </div>
                                <p class="cadre-demo__text">
                                    {{ __('Reference:') }} <strong>{{ $demoCadreReference }}</strong>
                                    <span aria-hidden="true"> | </span>
                                    {{ __('OTP:') }} <strong>{{ $demoOtp }}</strong>
                                </p>
                                <button type="button" class="cadre-demo__autofill" data-guest-demo-autofill data-demo-ref="{{ $demoCadreReference }}">
                                    {{ __('Auto-fill') }}
                                </button>
                            </div>
                        @endif
                    @else
                        <h2 class="card__heading">{{ __('Welcome Back') }}</h2>
                        <p class="card__sub">{{ __('Sign in to continue') }}</p>

                        <form method="post" action="{{ route('site.login') }}" novalidate>
                            @csrf

                            <div class="field">
                                <label for="email">{{ __('Username') }}</label>
                                <input
                                    id="email"
                                    name="email"
                                    type="email"
                                    value="{{ old('email') }}"
                                    placeholder="{{ __('Enter your username') }}"
                                    autocomplete="username"
                                    required
                                    autofocus
                                >
                            </div>

                            <div class="field">
                                <label for="password">{{ __('Password') }}</label>
                                <div class="field__wrap">
                                    <input
                                        id="password"
                                        name="password"
                                        type="password"
                                        placeholder="{{ __('Enter your password') }}"
                                        autocomplete="current-password"
                                        required
                                    >
                                    <button
                                        type="button"
                                        class="toggle-password"
                                        aria-label="{{ __('Show password') }}"
                                        data-visible-label="{{ __('Hide password') }}"
                                        data-hidden-label="{{ __('Show password') }}"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <button type="submit" class="btn-submit">{{ __('Sign In') }}</button>
                        </form>
                    @endif
                </div>
            </div>
        </main>
    </div>

    <div
        class="modal{{ $showCadreOtpModal ? ' modal--open' : '' }}"
        id="cadre-otp-modal"
        role="dialog"
        aria-modal="true"
        aria-labelledby="cadre-otp-modal-title"
    >
        <div class="modal__backdrop" tabindex="-1"></div>
        <div class="modal__panel">
            <h2 class="modal__title" id="cadre-otp-modal-title">{{ __('Enter OTP') }}</h2>
            <p class="modal__lead">{{ __('An OTP has been sent to your registered mobile number in GEMS.') }}</p>
            @error('otp')
                <div class="alert" role="alert">{{ $message }}</div>
            @enderror
            <form method="post" action="{{ route('cadre.otp.verify') }}" class="modal__form" novalidate>
                @csrf
                <div class="field field--otp">
                    <label class="sr-only" for="cadre_otp">{{ __('One-time password') }}</label>
                    <input
                        id="cadre_otp"
                        name="otp"
                        type="text"
                        value="{{ old('otp') }}"
                        placeholder="{{ __('Enter 5-digit OTP') }}"
                        inputmode="numeric"
                        maxlength="8"
                        autocomplete="one-time-code"
                        required
                    >
                </div>
                <div class="modal__actions">
                    <a href="{{ route('cadre.otp.cancel') }}" class="btn-secondary">{{ __('Cancel') }}</a>
                    <button type="submit" class="btn-submit btn-submit--inline">{{ __('Verify') }}</button>
                </div>
            </form>
            <p class="modal__hint">{{ __('Demo OTP:') }} {{ $demoOtp }}</p>
        </div>
    </div>

    <div
        class="modal{{ $showGuestOtpModal ? ' modal--open' : '' }}"
        id="guest-otp-modal"
        role="dialog"
        aria-modal="true"
        aria-labelledby="guest-otp-modal-title"
    >
        <div class="modal__backdrop" tabindex="-1"></div>
        <div class="modal__panel">
            <h2 class="modal__title" id="guest-otp-modal-title">{{ __('Enter OTP') }}</h2>
            <p class="modal__lead">{{ __('An OTP has been sent to your registered mobile number in GEMS.') }}</p>
            @error('guest_otp')
                <div class="alert" role="alert">{{ $message }}</div>
            @enderror
            <form method="post" action="{{ route('guest.otp.verify') }}" class="modal__form" novalidate>
                @csrf
                <div class="field field--otp">
                    <label class="sr-only" for="guest_otp">{{ __('One-time password') }}</label>
                    <input
                        id="guest_otp"
                        name="guest_otp"
                        type="text"
                        value="{{ old('guest_otp') }}"
                        placeholder="{{ __('Enter 5-digit OTP') }}"
                        inputmode="numeric"
                        maxlength="8"
                        autocomplete="one-time-code"
                        required
                    >
                </div>
                <div class="modal__actions">
                    <a href="{{ route('guest.otp.cancel') }}" class="btn-secondary">{{ __('Cancel') }}</a>
                    <button type="submit" class="btn-submit btn-submit--inline">{{ __('Verify') }}</button>
                </div>
            </form>
            <p class="modal__hint">{{ __('Demo OTP:') }} {{ $demoOtp }}</p>
        </div>
    </div>
</body>
</html>
