<x-filament-panels::page>
    <style>
        .invoice-select {
            width: min(538px, 100%);
            height: 44px;
            border: 1px solid #111827;
            border-radius: 9px;
            background: #f8fafc;
            padding: 8px 18px;
            color: #001b33;
            font-size: 16px;
            outline: none;
        }

        .invoice-card {
            width: min(806px, 100%);
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: #ffffff;
            padding: 38px;
            color: #001b33;
        }

        .invoice-logo {
            width: 56px;
            height: 56px;
            object-fit: contain;
        }

        .invoice-divider {
            border-top: 1px solid #cbd5e1;
            margin: 28px 0 18px;
        }

        .invoice-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
            padding: 7px 0;
            font-size: 16px;
        }

        .invoice-muted {
            color: #526783;
        }

        .invoice-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 44px;
            border-radius: 8px;
            border: 1px solid #173c63;
            background: #173c63;
            padding: 0 18px;
            color: #ffffff;
            font-size: 16px;
            font-weight: 500;
            text-decoration: none;
        }

        @media (max-width: 767px) {
            .invoice-card {
                padding: 24px 18px;
            }

            .invoice-top,
            .invoice-columns,
            .invoice-row {
                align-items: flex-start;
                flex-direction: column;
            }
        }

        @media print {
            body * {
                visibility: hidden;
            }

            #invoice-print-area,
            #invoice-print-area * {
                visibility: visible;
            }

            #invoice-print-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }

            .fi-sidebar,
            .fi-topbar,
            .invoice-actions {
                display: none !important;
            }
        }
    </style>

    @php
        $invoice = $this->invoice;
        $booking = $invoice['booking'] ?? null;
        $bookingRef = $booking ? $this->bookingRef($booking) : null;
    @endphp

    <div class="space-y-5">

        <select wire:model.live="bookingId" class="invoice-select">
            <option value="">Select Booking...</option>
            @foreach ($this->bookings as $option)
                <option value="{{ $option->id }}">{{ $this->bookingRef($option) }} — {{ $option->user?->name ?: '-' }}</option>
            @endforeach
        </select>

        @if ($invoice && $booking)
            <section id="invoice-print-area" class="invoice-card dark:border-gray-800 dark:bg-gray-900">
                <div class="invoice-top flex items-start justify-between gap-6">
                    <div class="flex items-center gap-4">
                        <img src="{{ asset('images/biam-logo.png') }}" alt="BIAM Foundation" class="invoice-logo">
                        <div>
                            <h2 class="text-xl font-bold">BIAM Foundation</h2>
                            <p class="invoice-muted">Bangladesh Institute of Admin &amp; Mgmt</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-lg font-semibold">Invoice No: INV-{{ $bookingRef }}</p>
                        <p class="invoice-muted">Date: {{ now()->format('d/m/Y') }}</p>
                    </div>
                </div>

                <div class="invoice-columns mt-8 flex justify-between gap-10">
                    <div>
                        <h3 class="font-semibold">Billed To:</h3>
                        <p class="mt-2">{{ $booking->user?->name ?: '-' }}</p>
                        <p class="invoice-muted">{{ $booking->user?->role ?: '-' }}</p>
                    </div>
                    <div>
                        <h3 class="font-semibold">Booking Details:</h3>
                        <p class="mt-2">Ref: {{ $bookingRef }}</p>
                        <p>Room: {{ $booking->room?->room_number ?: '-' }} ({{ ucfirst(str_replace('_', '-', $booking->room_type)) }})</p>
                        <p>Check-in: {{ $booking->check_in_date?->toDateString() ?: '-' }}</p>
                        <p>Check-out: {{ $booking->check_out_date?->toDateString() ?: '-' }}</p>
                        <p>Duration: {{ $invoice['duration'] }} {{ \Illuminate\Support\Str::plural('night', $invoice['duration']) }}</p>
                    </div>
                </div>

                <div class="invoice-divider"></div>

                <div>
                    <div class="invoice-row">
                        <span>Room Rate: BDT {{ number_format($invoice['room_rate'], 0) }}/night × {{ $invoice['duration'] }} {{ \Illuminate\Support\Str::plural('night', $invoice['duration']) }}</span>
                        <span>BDT {{ number_format($invoice['room_rate'] * $invoice['duration'], 0) }}</span>
                    </div>
                    <div class="invoice-row">
                        <span>Rent Multiplier: {{ $invoice['rent_multiplier'] }}x</span>
                    </div>
                    <div class="invoice-row font-bold">
                        <span>Subtotal</span>
                        <span>BDT {{ number_format($invoice['subtotal'], 0) }}</span>
                    </div>
                    @if ($invoice['meal_total'] > 0)
                        <div class="invoice-row">
                            <span>Meal Charges</span>
                            <span>BDT {{ number_format($invoice['meal_total'], 0) }}</span>
                        </div>
                    @endif
                    <div class="invoice-row invoice-muted">
                        <span>Booking Money Paid (non-refundable)</span>
                        <span>BDT {{ number_format($invoice['booking_money_paid'], 0) }}</span>
                    </div>
                    @if ($invoice['rent_paid'] > 0)
                        <div class="invoice-row invoice-muted">
                            <span>Rent Paid</span>
                            <span>BDT {{ number_format($invoice['rent_paid'], 0) }}</span>
                        </div>
                    @endif
                    @if ($invoice['meal_paid'] > 0)
                        <div class="invoice-row invoice-muted">
                            <span>Meal Paid</span>
                            <span>BDT {{ number_format($invoice['meal_paid'], 0) }}</span>
                        </div>
                    @endif
                    <div class="invoice-row text-lg font-bold">
                        <span>Balance Due</span>
                        <span>BDT {{ number_format($invoice['balance_due'], 0) }}</span>
                    </div>
                </div>

                <div class="invoice-divider"></div>
                <p class="text-center text-sm tracking-wide invoice-muted">Thank you for staying at BIAM Foundation</p>
            </section>

            <div class="invoice-actions flex flex-wrap gap-3">
                <button type="button" onclick="window.print()" class="invoice-action">
                    <x-filament::icon icon="heroicon-o-printer" class="mr-2 h-5 w-5" />
                    Print
                </button>
                <a href="{{ route('hostel.invoices.download', $booking) }}" class="invoice-action">
                    Download PDF
                </a>
            </div>
        @endif
    </div>
</x-filament-panels::page>
