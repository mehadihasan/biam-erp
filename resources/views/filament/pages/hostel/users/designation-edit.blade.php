<x-filament-panels::page>
    <style>
        .designation-form {
            width: 100%;
            max-width: 756px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: #ffffff;
            padding: 32px 28px 28px;
        }

        .designation-form__grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            column-gap: 18px;
            row-gap: 20px;
        }

        .designation-form__field {
            display: flex;
            min-width: 0;
            flex-direction: column;
            gap: 8px;
            color: #001b33;
            font-size: 16px;
            font-weight: 500;
            line-height: 1.25;
        }

        .designation-form__field--wide {
            grid-column: 1 / -1;
        }

        .designation-form__required {
            color: #dc2626;
        }

        .designation-form__control {
            width: 100%;
            min-width: 0;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background-color: #f8fafc;
            padding: 8px 16px;
            color: #001b33;
            font-size: 16px;
            line-height: 1.4;
            outline: none;
        }

        input.designation-form__control {
            height: 42px;
        }

        .designation-form__control:focus {
            border-color: #173c63;
            box-shadow: 0 0 0 1px #173c63;
        }

        .designation-form__error {
            color: #dc2626;
            font-size: 12px;
            line-height: 1.35;
        }

        .designation-form__actions {
            display: flex;
            gap: 14px;
            margin-top: 26px;
        }

        .designation-form__button,
        .designation-form__cancel {
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

        .designation-form__button {
            border: 1px solid #173c63;
            background: #173c63;
            color: #ffffff;
        }

        .designation-form__cancel {
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #001b33;
            text-decoration: none;
        }

        @media (max-width: 767px) {
            .designation-form {
                max-width: none;
                padding: 24px 18px;
            }

            .designation-form__grid {
                grid-template-columns: 1fr;
                row-gap: 18px;
            }

            .designation-form__field--wide {
                grid-column: auto;
            }

            .designation-form__actions {
                flex-direction: column;
            }

            .designation-form__button,
            .designation-form__cancel {
                width: 100%;
            }
        }

        .dark .designation-form {
            border-color: #334155;
            background: #111827;
        }

        .dark .designation-form__field {
            color: #ffffff;
        }

        .dark .designation-form__control {
            border-color: #475569;
            background-color: #1f2937;
            color: #ffffff;
        }

        .dark .designation-form__cancel {
            border-color: #475569;
            background: #111827;
            color: #ffffff;
        }
    </style>

    <div class="space-y-4">
        <div>
            <h2 class="text-xl font-bold text-gray-950 dark:text-white">Edit Designation</h2>
        </div>

        @if ($errors->any())
            <div class="max-w-3xl rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ __('Please fix the highlighted fields and try again.') }}
            </div>
        @endif

        <form method="post" action="{{ route('designations.update', $designation) }}" class="designation-form">
            @csrf
            @method('put')

            <div class="designation-form__grid">
                <label class="designation-form__field">
                    <span>Name <span class="designation-form__required">*</span></span>
                    <input name="name" type="text" value="{{ old('name', $designation->name) }}" class="designation-form__control">
                    @error('name') <span class="designation-form__error">{{ $message }}</span> @enderror
                </label>

                <label class="designation-form__field designation-form__field--wide">
                    <span>Description</span>
                    <textarea name="description" rows="4" class="designation-form__control">{{ old('description', $designation->description) }}</textarea>
                    @error('description') <span class="designation-form__error">{{ $message }}</span> @enderror
                </label>
            </div>

            <div class="designation-form__actions">
                <button class="designation-form__button">Update</button>
                <a href="{{ \App\Filament\Pages\Hostel\Users\Designations::getUrl(panel: 'admin') }}" class="designation-form__cancel">Cancel</a>
            </div>
        </form>
    </div>
</x-filament-panels::page>
