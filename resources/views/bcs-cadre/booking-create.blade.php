@extends('layouts.bcs-cadre')

@section('title', __('New Booking'))

@section('content')
    @php
        $calculation = null;

        if ($room && $checkInDate && $checkOutDate) {
            try {
                $checkIn = \Illuminate\Support\Carbon::parse($checkInDate)->startOfDay();
                $checkOut = \Illuminate\Support\Carbon::parse($checkOutDate)->startOfDay();
                $duration = (int) $checkIn->diffInDays($checkOut, false);

                if ($duration > 0) {
                    $multiplier = match (true) {
                        $duration <= 3 => 1,
                        $duration <= 7 => 2,
                        default => 3,
                    };
                    $baseRate = (float) $room->base_rate;
                    $calculatedRent = $baseRate * $duration * $multiplier * max(1, $numberOfRooms);
                    $calculation = [
                        'duration_nights' => $duration,
                        'rent_multiplier' => $multiplier,
                        'base_rate' => $baseRate,
                        'calculated_rent' => $calculatedRent,
                        'booking_money' => $calculatedRent * 0.20,
                        'total_rent' => $calculatedRent,
                    ];
                }
            } catch (\Throwable) {
                $calculation = null;
            }
        }

        $minimumPayment = $calculation ? round((float) $calculation['total_rent'] * 0.20, 2) : 0;
    @endphp

    <style>
        .booking-form-card {
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: #ffffff;
            padding: 32px 28px 28px;
        }

        .booking-form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            column-gap: 28px;
            row-gap: 20px;
        }

        .booking-field-row,
        .booking-date-row {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px 28px;
        }

        .booking-full-width {
            grid-column: 1 / -1;
        }

        .booking-field {
            display: flex;
            min-width: 0;
            flex-direction: column;
            gap: 8px;
            color: #001b33;
            font-size: 16px;
            font-weight: 500;
        }

        .booking-control,
        .booking-readonly {
            width: 100%;
            min-width: 0;
            height: 42px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background-color: #f8fafc;
            padding: 8px 16px;
            color: #001b33;
            font-size: 16px;
            outline: none;
        }

        .booking-readonly {
            display: flex;
            align-items: center;
        }

        .booking-date-field,
        .booking-date-field .booking-control {
            cursor: pointer;
        }

        textarea.booking-control {
            min-height: 66px;
            height: auto;
        }

        .booking-note {
            color: #526783;
            font-size: 14px;
            font-weight: 400;
        }

        .booking-required,
        .booking-error {
            color: #dc2626;
        }

        .booking-error {
            font-size: 12px;
            font-weight: 400;
        }

        .booking-summary {
            margin-top: 30px;
            border-radius: 8px;
            background: #e5e9ef;
            padding: 18px 16px;
            color: #001b33;
            font-size: 14px;
            line-height: 1.7;
        }

        .booking-actions {
            display: flex;
            gap: 14px;
            margin-top: 24px;
        }

        .booking-primary,
        .booking-cancel {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 42px;
            border-radius: 8px;
            padding: 0 26px;
            font-size: 16px;
            font-weight: 500;
        }

        .booking-primary {
            border: 1px solid #173c63;
            background: #173c63;
            color: #ffffff;
        }

        .booking-primary:disabled {
            cursor: not-allowed;
            opacity: .6;
        }

        .booking-cancel {
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #001b33;
            text-decoration: none;
        }

        .booking-payment-modal,
        .booking-success-modal {
            position: fixed;
            inset: 0;
            z-index: 50;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background: rgba(15, 23, 42, 0.58);
        }

        .booking-payment-modal[hidden] {
            display: none;
        }

        .booking-payment-panel {
            width: min(520px, 100%);
            border: 1px solid #d6dde6;
            border-radius: 12px;
            background: #ffffff;
            padding: 28px 26px 24px;
            box-shadow: 0 20px 60px rgba(15, 23, 42, 0.24);
        }

        .booking-payment-title {
            margin: 0 0 18px;
            color: #1f2937;
            font-size: 22px;
            font-weight: 800;
        }

        .booking-payment-grid {
            display: grid;
            gap: 16px;
        }

        .booking-payment-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 22px;
        }

        @media (max-width: 767px) {
            .booking-form-card {
                padding: 24px 18px;
            }

            .booking-form-grid {
                grid-template-columns: 1fr;
                row-gap: 18px;
            }

            .booking-field-row,
            .booking-date-row {
                grid-template-columns: 1fr;
                gap: 18px;
            }

            .booking-actions,
            .booking-payment-actions {
                flex-direction: column;
            }

            .booking-primary,
            .booking-cancel {
                width: 100%;
            }

            .booking-payment-panel {
                padding: 24px 18px;
            }
        }
    </style>

    <div class="bcs-page">
        <a href="{{ route($portalRoutePrefix.'.booking') }}" class="bcs-room-detail-back">
            &larr; {{ __('Back to rooms') }}
        </a>

        <header class="bcs-page__header">
            <h1>{{ __('New Booking') }}</h1>
            <p>{{ __('Confirm your room reservation request.') }}</p>
        </header>

        <section class="bcs-panel">
            <form
                method="post"
                action="{{ route($portalRoutePrefix.'.bookings.store') }}"
                class="booking-form-card"
                data-cadre-booking-form
                data-minimum-payment="{{ $minimumPayment }}"
                data-open-payment-on-load="{{ $errors->has('payment_guest_name') || $errors->has('payment_amount') || $errors->has('payment_method') ? 'true' : 'false' }}"
            >
                @csrf

                <div class="booking-form-grid">
                    <div class="booking-field-row booking-full-width">
                        <label class="booking-field">
                            <span>{{ __('Guest') }} <span class="booking-required">*</span></span>
                            <div class="booking-readonly">{{ $cadreUser->name }}</div>
                        </label>

                        <label class="booking-field">
                            <span>{{ __('Ref. ID') }}</span>
                            <input type="text" name="ref" value="{{ $ref }}" class="booking-control">
                            @error('ref') <span class="booking-error">{{ $message }}</span> @enderror
                        </label>
                    </div>

                    <div class="booking-date-row booking-full-width">
                        <label class="booking-field booking-date-field" data-bcs-date-field tabindex="0">
                            <span>{{ __('Check-in Date') }} <span class="booking-required">*</span></span>
                            <input data-bcs-date-input data-booking-query="check_in" type="date" name="check_in_date" value="{{ $checkInDate }}" class="booking-control" required>
                            <span class="booking-note">{{ __('Check-in Time: 2:00 PM - Fixed by BIAM Policy') }}</span>
                            @error('check_in_date') <span class="booking-error">{{ $message }}</span> @enderror
                        </label>

                        <label class="booking-field booking-date-field" data-bcs-date-field tabindex="0">
                            <span>{{ __('Check-out Date') }} <span class="booking-required">*</span></span>
                            <input data-bcs-date-input data-booking-query="check_out" type="date" name="check_out_date" value="{{ $checkOutDate }}" class="booking-control" required>
                            <span class="booking-note">{{ __('Check-out Time: 12:00 Noon - Fixed by BIAM Policy') }}</span>
                            @error('check_out_date') <span class="booking-error">{{ $message }}</span> @enderror
                        </label>
                    </div>

                    <div class="booking-field-row booking-full-width">
                        <label class="booking-field">
                            <span>{{ __('Room') }} <span class="booking-required">*</span></span>
                            <select name="room_id" data-booking-query="room" class="booking-control" @disabled(! $checkInDate || ! $checkOutDate || ($availableRooms->isEmpty() && ! $selectedRoomId)) required>
                                @if (! $checkInDate || ! $checkOutDate)
                                    @if ($room)
                                        <option value="{{ $room->id }}" selected>
                                            {{ $room->room_number }} - {{ __('select dates to check availability') }}
                                        </option>
                                    @endif
                                    <option value="">{{ __('Select dates first...') }}</option>
                                @elseif ($availableRooms->isEmpty())
                                    @if ($room)
                                        <option value="{{ $room->id }}" selected>
                                            {{ $room->room_number }} - {{ __('not available for selected dates') }}
                                        </option>
                                    @endif
                                    <option value="">{{ __('No rooms available for selected dates') }}</option>
                                @else
                                    <option value="">{{ __('Select room...') }}</option>
                                    @if ($selectedRoomUnavailable && $room)
                                        <option value="{{ $room->id }}" selected>
                                            {{ $room->room_number }} - {{ __('not available for selected dates') }}
                                        </option>
                                    @endif
                                    @foreach ($availableRooms as $availableRoom)
                                        <option value="{{ $availableRoom->id }}" @selected($selectedRoomId === $availableRoom->id)>
                                            {{ $availableRoom->room_number }} - BDT {{ number_format((float) $availableRoom->base_rate, 0) }}/night
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @if ($checkInDate && $checkOutDate && $availableRooms->isEmpty())
                                <span class="booking-error">{{ __('No rooms have available bed/seats for the selected dates.') }}</span>
                            @endif
                            @if ($selectedRoomUnavailable)
                                <span class="booking-error">{{ __('The selected room is no longer available for these dates.') }}</span>
                            @endif
                            @error('room_id') <span class="booking-error">{{ $message }}</span> @enderror
                        </label>

                        <label class="booking-field">
                            <span>{{ __('Bed/Seat') }} <span class="booking-required">*</span></span>
                            <select name="number_of_rooms" data-booking-query="rooms" class="booking-control" @disabled(! $selectedRoomId || $bedSeatOptions === []) required>
                                @if (! $selectedRoomId)
                                    <option value="">{{ __('Select room first...') }}</option>
                                @elseif (! $checkInDate || ! $checkOutDate)
                                    <option value="">{{ __('Select dates first...') }}</option>
                                @elseif ($bedSeatOptions === [])
                                    <option value="">{{ __('No bed/seat available') }}</option>
                                @else
                                    @foreach ($bedSeatOptions as $bedSeat)
                                        <option value="{{ $bedSeat }}" @selected($numberOfRooms === $bedSeat)>{{ $bedSeat }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @if ($selectedRoomId && is_int($availableBedSeatCount))
                                @if ($availableBedSeatCount > 0)
                                    <span class="booking-note">{{ __('Available bed/seats: :seats', ['seats' => implode(', ', $bedSeatOptions)]) }}</span>
                                @else
                                    <span class="booking-error">{{ __('No bed/seat is available for this room.') }}</span>
                                @endif
                            @endif
                            @error('number_of_rooms') <span class="booking-error">{{ $message }}</span> @enderror
                        </label>
                    </div>

                    <label class="booking-field booking-full-width">
                        <span>{{ __('Notes') }}</span>
                        <textarea name="notes" class="booking-control">{{ $notes }}</textarea>
                        @error('notes') <span class="booking-error">{{ $message }}</span> @enderror
                    </label>
                </div>

                @if ($calculation)
                    <div class="booking-summary">
                        <div><strong>{{ __('Duration:') }}</strong> {{ $calculation['duration_nights'] }} {{ \Illuminate\Support\Str::plural('night', $calculation['duration_nights']) }}</div>
                        <div><strong>{{ __('Rent Multiplier:') }}</strong> {{ $calculation['rent_multiplier'] }}x</div>
                        <div><strong>{{ __('Base Rate:') }}</strong> BDT {{ number_format($calculation['base_rate'], 0) }}/night</div>
                        <div><strong>{{ __('Calculated Rent:') }}</strong> BDT {{ number_format($calculation['calculated_rent'], 0) }}</div>
                        <div><strong>{{ __('Booking Money (20%):') }}</strong> BDT {{ number_format($calculation['booking_money'], 0) }} - {{ __('NON-REFUNDABLE') }}</div>
                        <div><strong>{{ __('Total Rent:') }}</strong> BDT {{ number_format($calculation['total_rent'], 0) }}</div>
                    </div>
                @elseif ($selectedRoomId && $checkInDate && $checkOutDate)
                    <div class="booking-summary">
                        {{ __('Please select a valid date range to calculate rent.') }}
                    </div>
                @endif

                <div class="booking-actions">
                    <button type="button" class="booking-primary" data-open-payment @disabled(! $calculation || $selectedRoomUnavailable || ($selectedRoomId && $availableBedSeatCount === 0))>{{ __('Create Booking') }}</button>
                    <a href="{{ route($portalRoutePrefix.'.booking') }}" class="booking-cancel">{{ __('Cancel') }}</a>
                </div>

                <div class="booking-payment-modal" data-payment-modal role="dialog" aria-modal="true" aria-labelledby="booking-payment-title" hidden>
                    <div class="booking-payment-panel">
                        <h2 class="booking-payment-title" id="booking-payment-title">{{ __('Payment') }}</h2>

                        <div class="booking-payment-grid">
                            <label class="booking-field">
                                <span>{{ __('Guest Name') }} <span class="booking-required">*</span></span>
                                <input type="text" name="payment_guest_name" value="{{ $paymentGuestName }}" class="booking-control" readonly required>
                                @error('payment_guest_name') <span class="booking-error">{{ $message }}</span> @enderror
                            </label>

                            <label class="booking-field">
                                <span>{{ __('Amount') }} <span class="booking-required">*</span></span>
                                <input type="number" name="payment_amount" value="{{ $paymentAmount ?: ($calculation ? number_format((float) $calculation['booking_money'], 2, '.', '') : '') }}" min="{{ $minimumPayment }}" step="0.01" class="booking-control" required>
                                <span class="booking-note">{{ __('Minimum payment is 20% of total amount') }}</span>
                                @error('payment_amount') <span class="booking-error">{{ $message }}</span> @enderror
                            </label>

                            <label class="booking-field">
                                <span>{{ __('Payment Method') }} <span class="booking-required">*</span></span>
                                <select name="payment_method" class="booking-control" required>
                                    <option value="">{{ __('Select payment method...') }}</option>
                                    <option value="Card" @selected($paymentMethod === 'Card')>{{ __('Card') }}</option>
                                    <option value="Mobile Banking" @selected($paymentMethod === 'Mobile Banking')>{{ __('Mobile Banking') }}</option>
                                </select>
                                @error('payment_method') <span class="booking-error">{{ $message }}</span> @enderror
                            </label>
                        </div>

                        <div class="booking-payment-actions">
                            <button type="button" class="booking-cancel" data-close-payment>{{ __('Cancel') }}</button>
                            <button type="submit" class="booking-primary">{{ __('Pay') }}</button>
                        </div>
                    </div>
                </div>
            </form>
        </section>
    </div>

    @if ($successReference)
        <div class="booking-success-modal" role="dialog" aria-modal="true" aria-labelledby="booking-success-title">
            <div class="bcs-success-modal__panel">
                <div class="bcs-success-modal__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                        <path d="M20 6 9 17l-5-5"></path>
                    </svg>
                </div>
                <h2 id="booking-success-title">{{ __('Thank you!') }}</h2>
                <p>{{ __('Your booking request has been submitted successfully.') }}</p>
                <p class="bcs-success-modal__reference">{{ __('Reference:') }} <strong>{{ $successReference }}</strong></p>
                <div class="bcs-success-modal__actions">
                    <a href="{{ route($portalRoutePrefix.'.booking') }}" class="bcs-modal-btn bcs-modal-btn--secondary">{{ __('Book another') }}</a>
                    <a href="{{ route($portalRoutePrefix.'.booking') }}" class="bcs-modal-btn bcs-modal-btn--primary">{{ __('Done') }}</a>
                </div>
            </div>
        </div>
    @endif

    <script>
        document.querySelectorAll('[data-bcs-date-field]').forEach((field) => {
            const input = field.querySelector('[data-bcs-date-input]');

            const openDatePicker = () => {
                input?.focus();

                if (typeof input?.showPicker === 'function') {
                    try {
                        input.showPicker();
                    } catch (error) {}
                }
            };

            field.addEventListener('click', (event) => {
                if (event.target !== input) {
                    openDatePicker();
                }
            });
            field.addEventListener('keydown', (event) => {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    openDatePicker();
                }
            });
        });

        document.querySelectorAll('[data-booking-query]').forEach((field) => {
            field.addEventListener('change', () => {
                const url = new URL(window.location.href);
                const key = field.dataset.bookingQuery;

                if (field.value) {
                    url.searchParams.set(key, field.value);
                } else {
                    url.searchParams.delete(key);
                }

                if (key === 'check_in' || key === 'check_out' || key === 'room') {
                    url.searchParams.delete('rooms');
                }

                window.location.href = url.toString();
            });
        });

        document.querySelectorAll('[data-cadre-booking-form]').forEach((form) => {
            const modal = form.querySelector('[data-payment-modal]');
            const openButton = form.querySelector('[data-open-payment]');
            const closeButton = form.querySelector('[data-close-payment]');
            const amountInput = form.querySelector('[name="payment_amount"]');

            openButton?.addEventListener('click', () => {
                modal.hidden = false;
                amountInput?.focus();
            });

            closeButton?.addEventListener('click', () => {
                modal.hidden = true;
            });

            if (form.dataset.openPaymentOnLoad === 'true') {
                modal.hidden = false;
            }
        });
    </script>
@endsection
