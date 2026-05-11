<x-filament-panels::page>
    <style>
        .user-create-form {
            width: 100%;
            max-width: 756px;
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

        .user-create-form__error {
            color: #dc2626;
            font-size: 12px;
        }

        @media (max-width: 767px) {
            .user-create-form {
                max-width: none;
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

    <div class="space-y-4">
        <div>
            <h2 class="text-xl font-bold text-gray-950 dark:text-white">Edit User</h2>
        </div>

        @if ($errors->any())
            <div class="max-w-4xl rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ __('Please fix the highlighted fields and try again.') }}
            </div>
        @endif

        <form method="post" action="{{ route('users.update', $user) }}" class="user-create-form">
            @csrf
            @method('put')

            <div class="user-create-form__grid">
                <label class="user-create-form__field">
                    <span>Full Name <span class="user-create-form__required">*</span></span>
                    <input name="full_name" type="text" value="{{ old('full_name', $user->name) }}" class="user-create-form__control">
                    @error('full_name') <span class="user-create-form__error">{{ $message }}</span> @enderror
                </label>

                <label class="user-create-form__field">
                    <span>Email <span class="user-create-form__required">*</span></span>
                    <input name="email" type="email" value="{{ old('email', $user->email) }}" class="user-create-form__control">
                    @error('email') <span class="user-create-form__error">{{ $message }}</span> @enderror
                </label>

                <label class="user-create-form__field">
                    <span>Phone <span class="user-create-form__required">*</span></span>
                    <input name="phone" type="text" value="{{ old('phone', $user->phone) }}" class="user-create-form__control">
                    @error('phone') <span class="user-create-form__error">{{ $message }}</span> @enderror
                </label>

                <label class="user-create-form__field">
                    <span>Role <span class="user-create-form__required">*</span></span>
                    <select name="role" class="user-create-form__control">
                        @foreach (['Super admin', 'admin', 'staff', 'guest'] as $role)
                            <option value="{{ $role }}" @selected(old('role', $user->role) === $role)>{{ $role }}</option>
                        @endforeach
                    </select>
                    @error('role') <span class="user-create-form__error">{{ $message }}</span> @enderror
                </label>

                <label class="user-create-form__field">
                    <span>Designation <span class="user-create-form__required">*</span></span>
                    <select name="designation_id" class="user-create-form__control">
                        <option value="">Select designation</option>
                        @foreach ($designations as $designation)
                            <option value="{{ $designation->id }}" @selected((string) old('designation_id', $user->designation_id) === (string) $designation->id)>{{ $designation->name }}</option>
                        @endforeach
                    </select>
                    @error('designation_id') <span class="user-create-form__error">{{ $message }}</span> @enderror
                </label>

                <label class="user-create-form__field">
                    <span>Office ID <span class="user-create-form__required">*</span></span>
                    <input name="cadre_number" type="text" value="{{ old('cadre_number', $user->cadre_number) }}" placeholder="Office ID" class="user-create-form__control">
                    @error('cadre_number') <span class="user-create-form__error">{{ $message }}</span> @enderror
                </label>
            </div>

            <div class="user-create-form__actions">
                <button class="user-create-form__button">Update User</button>
                <a href="{{ \App\Filament\Pages\Hostel\Users\AllUsers::getUrl(panel: 'admin') }}" class="user-create-form__cancel">Cancel</a>
            </div>
        </form>
    </div>
</x-filament-panels::page>
