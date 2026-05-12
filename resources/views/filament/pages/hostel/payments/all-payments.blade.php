<x-filament-panels::page>
    <style>
        .payment-button,
        .payment-save,
        .payment-cancel {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 38px;
            border-radius: 8px;
            padding: 0 18px;
            font-size: 14px;
            font-weight: 500;
            white-space: nowrap;
        }

        .payment-button,
        .payment-save {
            border: 1px solid #173c63;
            background: #173c63;
            color: #ffffff;
        }

        .payment-cancel {
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #001b33;
        }

        .payment-form-card {
            width: 100%;
            border: 1px solid #cbd5e1;
            border-radius: 7px;
            background: #ffffff;
            padding: 18px 16px 16px;
        }

        .payment-form-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
            width: 100%;
        }

        .payment-field {
            min-width: 0;
        }

        .payment-field--wide {
            grid-column: 1 / -1;
        }

        .payment-label {
            display: block;
            margin-bottom: 8px;
            color: #001b33;
            font-size: 15px;
            font-weight: 500;
            line-height: 1.25;
        }

        .payment-control {
            width: 100%;
            height: 38px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background-color: #f8fafc;
            padding: 7px 16px;
            color: #001b33;
            font-size: 14px;
            outline: none;
        }

        .payment-error {
            color: #dc2626;
            font-size: 12px;
        }

        .payment-badge {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            background: #e9edf3;
            padding: 4px 12px;
            color: #526783;
            font-size: 12px;
            font-weight: 600;
        }

        @media (max-width: 767px) {
            .payment-form-grid {
                grid-template-columns: 1fr;
            }

            .payment-button,
            .payment-save,
            .payment-cancel {
                width: 100%;
            }
        }

        .dark .payment-form-card {
            border-color: #334155;
            background: #111827;
        }

        .dark .payment-label {
            color: #ffffff;
        }

        .dark .payment-control {
            border-color: #475569;
            background-color: #1f2937;
            color: #ffffff;
        }

        .dark .payment-cancel {
            border-color: #475569;
            background: #111827;
            color: #ffffff;
        }
    </style>

    <div class="space-y-4">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-bold text-gray-950 dark:text-white">All Payments</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Manage hostel payments and transactions</p>
            </div>
            <button
                type="button"
                wire:click="togglePaymentForm"
                wire:loading.attr="disabled"
                wire:target="togglePaymentForm"
                class="payment-button hover:opacity-90"
            >
                <span class="mr-2 text-xl leading-none">+</span>
                Record Payment
            </button>
        </div>

        @if ($showPaymentForm)
            <form wire:submit="save" class="payment-form-card">
                <div class="payment-form-grid">
                    <div class="payment-field payment-field--wide">
                        <label class="payment-label" for="payment_booking_id">Select Booking</label>
                        <select id="payment_booking_id" wire:model.live="bookingId" class="payment-control">
                            <option value="">Select Booking...</option>
                            @foreach ($bookings as $booking)
                                <option value="{{ $booking->id }}">{{ $this->bookingRef($booking) }} - {{ $booking->user?->name ?: '-' }}</option>
                            @endforeach
                        </select>
                        @error('bookingId') <span class="payment-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="payment-field">
                        <label class="payment-label" for="payment_amount">Amount</label>
                        <input id="payment_amount" wire:model.live="amount" type="number" min="0" step="0.01" class="payment-control">
                        @error('amount') <span class="payment-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="payment-field">
                        <label class="payment-label" for="payment_type">Payment Type</label>
                        <select id="payment_type" wire:model.live="type" class="payment-control">
                            <option value="booking_money">Booking Money</option>
                            <option value="rent">Rent</option>
                            <option value="meal">Meal</option>
                        </select>
                        @error('type') <span class="payment-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="payment-field">
                        <label class="payment-label" for="payment_transaction_id">Transaction ID</label>
                        <input id="payment_transaction_id" wire:model.live="transactionId" type="text" placeholder="Transaction ID" class="payment-control">
                        @error('transactionId') <span class="payment-error">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mt-4 flex flex-wrap gap-3">
                    <button type="submit" class="payment-save">Save</button>
                    <button type="button" wire:click="cancel" class="payment-cancel">Cancel</button>
                </div>
            </form>
        @endif

        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm dark:divide-gray-800">
                    <thead class="bg-gray-50 dark:bg-gray-950/40">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs text-gray-500">Ref</th>
                            <th class="px-4 py-3 text-left text-xs text-gray-500">Booking</th>
                            <th class="px-4 py-3 text-left text-xs text-gray-500">Guest</th>
                            <th class="px-4 py-3 text-left text-xs text-gray-500">Amount</th>
                            <th class="px-4 py-3 text-left text-xs text-gray-500">Type</th>
                            <th class="px-4 py-3 text-left text-xs text-gray-500">Gateway</th>
                            <th class="px-4 py-3 text-left text-xs text-gray-500">Txn ID</th>
                            <th class="px-4 py-3 text-left text-xs text-gray-500">Status</th>
                            <th class="px-4 py-3 text-left text-xs text-gray-500">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse ($payments as $payment)
                            <tr>
                                <td class="px-4 py-3 font-mono text-xs">{{ $payment->ref }}</td>
                                <td class="px-4 py-3">{{ $payment->booking ? $this->bookingRef($payment->booking) : '-' }}</td>
                                <td class="px-4 py-3">{{ $payment->guest?->name ?: $payment->booking?->user?->name ?: '-' }}</td>
                                <td class="px-4 py-3">BDT {{ number_format((float) $payment->amount, 0) }}</td>
                                <td class="px-4 py-3"><span class="payment-badge">{{ str_replace('_', ' ', $payment->type) }}</span></td>
                                <td class="px-4 py-3">{{ $payment->gateway ?: '-' }}</td>
                                <td class="px-4 py-3">{{ $payment->transaction_id ?: '-' }}</td>
                                <td class="px-4 py-3">{{ $payment->status }}</td>
                                <td class="px-4 py-3">{{ ($payment->paid_at ?: $payment->created_at)?->format('d/m/Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-8 text-center text-gray-500">No payments found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>
