<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Add New User') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .user-create-page {
            width: 100%;
            max-width: 756px;
            padding: 28px 10px;
        }

        .user-create-form {
            width: 100%;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: #ffffff;
            padding: 32px 28px 28px;
        }

        .user-create-form__grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            column-gap: 18px;
            row-gap: 20px;
        }

        .user-create-form__field {
            display: flex;
            min-width: 0;
            flex-direction: column;
            gap: 8px;
            color: #001b33;
            font-size: 16px;
            font-weight: 500;
            line-height: 1.25;
        }

        .user-create-form__required {
            color: #dc2626;
        }

        .user-create-form__control {
            width: 100%;
            min-width: 0;
            height: 42px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background-color: #f8fafc;
            padding: 8px 16px;
            color: #001b33;
            font-size: 16px;
            line-height: 1.4;
            outline: none;
        }

        .user-create-form__control:focus {
            border-color: #173c63;
            box-shadow: 0 0 0 1px #173c63;
        }

        .user-create-form__checkbox {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #001b33;
            font-size: 16px;
            font-weight: 400;
            line-height: 1.25;
        }

        .user-create-form__checkbox input {
            width: 16px;
            height: 16px;
            border: 1px solid #94a3b8;
            border-radius: 3px;
        }

        .user-create-form__error {
            color: #dc2626;
            font-size: 12px;
            line-height: 1.35;
        }

        .user-create-form__actions {
            display: flex;
            gap: 14px;
            margin-top: 26px;
        }

        .user-create-form__button,
        .user-create-form__cancel {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 106px;
            height: 42px;
            border-radius: 8px;
            padding: 0 26px;
            font-size: 16px;
            font-weight: 500;
            line-height: 1;
        }

        .user-create-form__button {
            border: 1px solid #173c63;
            background: #173c63;
            color: #ffffff;
        }

        .user-create-form__cancel {
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #001b33;
            text-decoration: none;
        }

        @media (max-width: 767px) {
            .user-create-page {
                max-width: none;
                padding: 24px 12px;
            }

            .user-create-form {
                padding: 24px 18px;
            }

            .user-create-form__grid {
                grid-template-columns: 1fr;
                row-gap: 18px;
            }

            .user-create-form__actions {
                flex-direction: column;
            }

            .user-create-form__button,
            .user-create-form__cancel {
                width: 100%;
            }
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-950">
    <main class="user-create-page">
        <div class="mb-6">
            <h1 class="text-2xl font-bold">{{ __('Add New User') }}</h1>
        </div>

        @if (session('success'))
            <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ __('Please fix the highlighted fields and try again.') }}
            </div>
        @endif

        <form method="post" action="{{ route('users.store') }}" class="user-create-form">
            @csrf

            <div class="user-create-form__grid">
                <label class="user-create-form__field">
                    <span>{{ __('Full Name') }} <span class="user-create-form__required">*</span></span>
                    <input name="full_name" type="text" value="{{ old('full_name') }}" class="user-create-form__control">
                    @error('full_name') <span class="user-create-form__error">{{ $message }}</span> @enderror
                </label>

                <label class="user-create-form__field">
                    <span>{{ __('Email') }} <span class="user-create-form__required">*</span></span>
                    <input name="email" type="email" value="{{ old('email') }}" class="user-create-form__control">
                    @error('email') <span class="user-create-form__error">{{ $message }}</span> @enderror
                </label>

                <label class="user-create-form__field">
                    <span>{{ __('Phone') }} <span class="user-create-form__required">*</span></span>
                    <input name="phone" type="text" value="{{ old('phone') }}" class="user-create-form__control">
                    @error('phone') <span class="user-create-form__error">{{ $message }}</span> @enderror
                </label>

                <label class="user-create-form__field">
                    <span>{{ __('Role') }} <span class="user-create-form__required">*</span></span>
                    <select name="role" class="user-create-form__control">
                        @foreach (['Super admin', 'admin', 'staff', 'guest'] as $role)
                            <option value="{{ $role }}" @selected(old('role', 'guest') === $role)>{{ $role }}</option>
                        @endforeach
                    </select>
                    @error('role') <span class="user-create-form__error">{{ $message }}</span> @enderror
                </label>

                <label class="user-create-form__field">
                    <span>{{ __('Designation') }} <span class="user-create-form__required">*</span></span>
                    <select name="designation_id" class="user-create-form__control">
                        <option value="">{{ __('Select designation') }}</option>
                        @foreach ($designations as $designation)
                            <option value="{{ $designation->id }}" @selected((string) old('designation_id') === (string) $designation->id)>{{ $designation->name }}</option>
                        @endforeach
                    </select>
                    @error('designation_id') <span class="user-create-form__error">{{ $message }}</span> @enderror
                </label>

                <label class="user-create-form__field">
                    <span>{{ __('Office ID') }} <span class="user-create-form__required">*</span></span>
                    <input name="cadre_number" type="text" value="{{ old('cadre_number') }}" placeholder="{{ __('Office ID') }}" class="user-create-form__control">
                    @error('cadre_number') <span class="user-create-form__error">{{ $message }}</span> @enderror
                </label>
            </div>

            <div class="user-create-form__actions">
                <button class="user-create-form__button">{{ __('Create User') }}</button>
                <a href="{{ \App\Filament\Pages\Hostel\Users\AllUsers::getUrl(panel: 'admin') }}" class="user-create-form__cancel">{{ __('Cancel') }}</a>
            </div>
        </form>
    </main>
</body>
</html>
