<x-filament-panels::page>
    <style>
        .inventory-form {
            width: 100%;
            max-width: 756px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: #ffffff;
            padding: 32px 28px 28px;
        }

        .inventory-form__grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            column-gap: 18px;
            row-gap: 20px;
        }

        .inventory-form__field {
            display: flex;
            min-width: 0;
            flex-direction: column;
            gap: 8px;
            color: #001b33;
            font-size: 16px;
            font-weight: 500;
            line-height: 1.25;
        }

        .inventory-form__field--wide {
            grid-column: 1 / -1;
        }

        .inventory-form__required {
            color: #dc2626;
        }

        .inventory-form__control {
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

        input.inventory-form__control {
            height: 42px;
        }

        .inventory-form__control:focus {
            border-color: #173c63;
            box-shadow: 0 0 0 1px #173c63;
        }

        .inventory-form__error {
            color: #dc2626;
            font-size: 12px;
            line-height: 1.35;
        }

        .inventory-form__actions {
            display: flex;
            gap: 14px;
            margin-top: 26px;
        }

        .inventory-form__button,
        .inventory-form__cancel {
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

        .inventory-form__button {
            border: 1px solid #173c63;
            background: #173c63;
            color: #ffffff;
        }

        .inventory-form__cancel {
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #001b33;
            text-decoration: none;
        }

        @media (max-width: 767px) {
            .inventory-form {
                max-width: none;
                padding: 24px 18px;
            }

            .inventory-form__grid {
                grid-template-columns: 1fr;
                row-gap: 18px;
            }

            .inventory-form__field--wide {
                grid-column: auto;
            }

            .inventory-form__actions {
                flex-direction: column;
            }

            .inventory-form__button,
            .inventory-form__cancel {
                width: 100%;
            }
        }

        .dark .inventory-form {
            border-color: #334155;
            background: #111827;
        }

        .dark .inventory-form__field {
            color: #ffffff;
        }

        .dark .inventory-form__control {
            border-color: #475569;
            background-color: #1f2937;
            color: #ffffff;
        }

        .dark .inventory-form__cancel {
            border-color: #475569;
            background: #111827;
            color: #ffffff;
        }
    </style>

    <div class="space-y-4">
        <div>
            <h2 class="text-xl font-bold text-gray-950 dark:text-white">Create Unit</h2>
        </div>

        @if ($errors->any())
            <div class="max-w-3xl rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ __('Please fix the highlighted fields and try again.') }}
            </div>
        @endif

        <form method="post" action="{{ route('inventory.units.store') }}" class="inventory-form">
            @csrf

            <div class="inventory-form__grid">
                <label class="inventory-form__field">
                    <span>Name <span class="inventory-form__required">*</span></span>
                    <input name="name" type="text" value="{{ old('name') }}" class="inventory-form__control">
                    @error('name') <span class="inventory-form__error">{{ $message }}</span> @enderror
                </label>

                <label class="inventory-form__field inventory-form__field--wide">
                    <span>Details</span>
                    <textarea name="details" rows="4" class="inventory-form__control">{{ old('details') }}</textarea>
                    @error('details') <span class="inventory-form__error">{{ $message }}</span> @enderror
                </label>
            </div>

            <div class="inventory-form__actions">
                <button class="inventory-form__button">Save</button>
                <a href="{{ \App\Filament\Pages\Inventory\Settings\Units::getUrl(panel: 'admin') }}" wire:navigate class="inventory-form__cancel">Cancel</a>
            </div>
        </form>
    </div>
</x-filament-panels::page>
