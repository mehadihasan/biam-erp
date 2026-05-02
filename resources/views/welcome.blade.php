<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'BIAM Hostel Management System') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet">
    <style>
        :root {
            --biam-navy: #1e3a5f;
            --biam-page: #f5f5f5;
            --biam-card: #ffffff;
            --biam-input: #f9f9f9;
            --biam-muted: #6b7280;
            --biam-label: #374151;
            --biam-heading: #111827;
            --biam-footer: rgba(255, 255, 255, 0.55);
            --biam-shadow: 0 12px 40px rgba(30, 58, 95, 0.08), 0 4px 12px rgba(0, 0, 0, 0.04);
            --radius-card: 16px;
            --radius-control: 10px;
        }

        *, *::before, *::after { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;
            color: var(--biam-heading);
            background: var(--biam-page);
            -webkit-font-smoothing: antialiased;
        }

        .landing {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        @media (min-width: 900px) {
            .landing {
                flex-direction: row;
            }
        }

        .panel-brand {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 2.5rem 1.5rem 4rem;
            background: var(--biam-navy);
            color: #fff;
            position: relative;
        }

        @media (min-width: 900px) {
            .panel-brand {
                padding: 3rem 2rem;
                min-height: 100vh;
            }
        }

        .panel-brand__inner {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1.25rem;
            max-width: 28rem;
        }

        .panel-brand__logo {
            width: 140px;
            height: 140px;
            object-fit: contain;
            border-radius: 50%;
        }

        .panel-brand__title {
            font-size: clamp(1.35rem, 3vw, 1.85rem);
            font-weight: 700;
            line-height: 1.25;
            letter-spacing: -0.02em;
        }

        .panel-brand__subtitle {
            font-size: 0.95rem;
            font-weight: 400;
            line-height: 1.5;
            opacity: 0.92;
            max-width: 22rem;
        }

        .panel-brand__footer {
            position: absolute;
            bottom: 1.25rem;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 0.75rem;
            color: var(--biam-footer);
        }

        .panel-form {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.25rem 3rem;
            background: var(--biam-page);
        }

        @media (min-width: 900px) {
            .panel-form {
                min-height: 100vh;
                padding: 2rem;
            }
        }

        .card {
            width: 100%;
            max-width: 420px;
            background: var(--biam-card);
            border-radius: var(--radius-card);
            box-shadow: var(--biam-shadow);
            padding: 2.5rem 2rem;
        }

        @media (min-width: 480px) {
            .card {
                padding: 2.75rem 2.5rem;
            }
        }

        .card__heading {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--biam-navy);
            letter-spacing: -0.02em;
            margin: 0 0 0.35rem;
        }

        .card__sub {
            font-size: 0.95rem;
            color: var(--biam-muted);
            margin: 0 0 1.75rem;
        }

        .field {
            margin-bottom: 1.25rem;
        }

        .field label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--biam-label);
            margin-bottom: 0.45rem;
        }

        .field__wrap {
            position: relative;
            display: flex;
            align-items: center;
        }

        .field input[type="email"],
        .field input[type="password"],
        .field input[type="text"] {
            width: 100%;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            border: 1px solid transparent;
            border-radius: var(--radius-control);
            background: var(--biam-input);
            color: var(--biam-heading);
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        .field input[type="password"] {
            padding-right: 2.75rem;
        }

        .field input::placeholder {
            color: #9ca3af;
        }

        .field input:focus {
            outline: none;
            border-color: rgba(30, 58, 95, 0.35);
            box-shadow: 0 0 0 3px rgba(30, 58, 95, 0.12);
        }

        .toggle-password {
            position: absolute;
            right: 0.65rem;
            top: 50%;
            transform: translateY(-50%);
            padding: 0.35rem;
            border: none;
            background: none;
            cursor: pointer;
            color: var(--biam-muted);
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .toggle-password:hover {
            color: var(--biam-label);
            background: rgba(0, 0, 0, 0.04);
        }

        .alert {
            font-size: 0.875rem;
            color: #b91c1c;
            background: #fef2f2;
            border-radius: var(--radius-control);
            padding: 0.65rem 0.85rem;
            margin-bottom: 1.25rem;
        }

        .btn-submit {
            width: 100%;
            margin-top: 0.25rem;
            padding: 0.85rem 1rem;
            font-size: 0.95rem;
            font-weight: 700;
            color: #fff;
            background: var(--biam-navy);
            border: none;
            border-radius: var(--radius-control);
            cursor: pointer;
            transition: background 0.15s ease, transform 0.05s ease;
        }

        .btn-submit:hover {
            background: #162d4a;
        }

        .btn-submit:active {
            transform: scale(0.99);
        }
    </style>
</head>
<body>
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
            <div class="card">
                <h2 class="card__heading">Welcome Back</h2>
                <p class="card__sub">Sign in to continue</p>

                @if ($errors->any())
                    <div class="alert" role="alert">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="post" action="{{ route('site.login') }}" novalidate>
                    @csrf

                    <div class="field">
                        <label for="email">Username</label>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            value="{{ old('email') }}"
                            placeholder="Enter your username"
                            autocomplete="username"
                            required
                            autofocus
                        >
                    </div>

                    <div class="field">
                        <label for="password">Password</label>
                        <div class="field__wrap">
                            <input
                                id="password"
                                name="password"
                                type="password"
                                placeholder="Enter your password"
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

                    <button type="submit" class="btn-submit">Sign In</button>
                </form>
            </div>
        </main>
    </div>

    <script>
        (function () {
            var input = document.getElementById('password');
            var btn = document.querySelector('.toggle-password');
            if (!input || !btn) return;

            btn.addEventListener('click', function () {
                var showing = input.type === 'password';
                input.type = showing ? 'text' : 'password';
                btn.setAttribute('aria-label', showing ? btn.dataset.visibleLabel : btn.dataset.hiddenLabel);
            });
        })();
    </script>
</body>
</html>
