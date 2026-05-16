@extends('layouts.bcs-cadre')

@section('title', __('New Booking'))

@section('content')
    @php
        $calculation = null;

        if ($checkInDate && $checkOutDate) {
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
    @endphp

    <div class="bcs-page">
        <a href="{{ route($portalRoutePrefix.'.booking') }}" class="bcs-room-detail-back">
            &larr; {{ __('Back to rooms') }}
        </a>

        <header class="bcs-page__header">
            <h1>{{ __('New Booking') }}</h1>
            <p>{{ __('Confirm your room reservation request.') }}</p>
        </header>

        <section class="bcs-panel">
            <form method="post" action="{{ route($portalRoutePrefix.'.bookings.store') }}" class="bcs-booking-form">
                @csrf
                <input type="hidden" name="room_id" value="{{ $room->id }}">

                <label>
                    <span>{{ __('Guest') }}</span>
                    <div class="bcs-readonly">{{ $cadreUser->name }}</div>
                </label>

                <label>
                    <span>{{ __('Room') }}</span>
                    <div class="bcs-readonly">{{ __('Room :number (:type)', ['number' => $room->room_number, 'type' => strtoupper(str_replace('_', '-', $room->room_type))]) }}</div>
                </label>

                <label data-bcs-date-field tabindex="0">
                    <span>{{ __('Check-in Date') }} <em>*</em></span>
                    <input data-bcs-date-input type="date" name="check_in_date" value="{{ $checkInDate }}" required>
                    @error('check_in_date') <span class="bcs-error">{{ $message }}</span> @enderror
                </label>

                <label data-bcs-date-field tabindex="0">
                    <span>{{ __('Check-out Date') }} <em>*</em></span>
                    <input data-bcs-date-input type="date" name="check_out_date" value="{{ $checkOutDate }}" required>
                    @error('check_out_date') <span class="bcs-error">{{ $message }}</span> @enderror
                </label>

                <label>
                    <span>{{ __('Adults') }}</span>
                    <select name="adults" required>
                        @for ($adult = 1; $adult <= 6; $adult++)
                            <option value="{{ $adult }}" @selected($adults === $adult)>
                                {{ trans_choice(':count adult|:count adults', $adult, ['count' => $adult]) }}
                            </option>
                        @endfor
                    </select>
                    @error('adults') <span class="bcs-error">{{ $message }}</span> @enderror
                </label>

                <label>
                    <span>{{ __('Number of Rooms') }}</span>
                    <select name="number_of_rooms" required>
                        @for ($roomCount = 1; $roomCount <= 5; $roomCount++)
                            <option value="{{ $roomCount }}" @selected($numberOfRooms === $roomCount)>
                                {{ trans_choice(':count room|:count rooms', $roomCount, ['count' => $roomCount]) }}
                            </option>
                        @endfor
                    </select>
                    @error('number_of_rooms') <span class="bcs-error">{{ $message }}</span> @enderror
                </label>

                <label class="bcs-booking-form__wide">
                    <span>{{ __('Notes') }}</span>
                    <input type="text" name="notes" value="{{ $notes }}">
                    @error('notes') <span class="bcs-error">{{ $message }}</span> @enderror
                </label>

                @if ($calculation)
                    <div class="bcs-booking-summary">
                        <div><strong>{{ __('Duration:') }}</strong> {{ $calculation['duration_nights'] }} {{ \Illuminate\Support\Str::plural('night', $calculation['duration_nights']) }}</div>
                        <div><strong>{{ __('Rent Multiplier:') }}</strong> {{ $calculation['rent_multiplier'] }}x</div>
                        <div><strong>{{ __('Base Rate:') }}</strong> BDT {{ number_format($calculation['base_rate'], 0) }}/night</div>
                        <div><strong>{{ __('Calculated Rent:') }}</strong> BDT {{ number_format($calculation['calculated_rent'], 0) }}</div>
                        <div><strong>{{ __('Booking Money (20%):') }}</strong> BDT {{ number_format($calculation['booking_money'], 0) }} - {{ __('NON-REFUNDABLE') }}</div>
                        <div><strong>{{ __('Total Rent:') }}</strong> BDT {{ number_format($calculation['total_rent'], 0) }}</div>
                    </div>
                @elseif ($checkInDate && $checkOutDate)
                    <div class="bcs-booking-summary">
                        {{ __('Please select a valid date range to calculate rent.') }}
                    </div>
                @endif

                <div class="bcs-form-grid__actions">
                    <button type="submit" class="bcs-action-btn">{{ __('Submit Booking') }}</button>
                    <a href="{{ route($portalRoutePrefix.'.booking') }}" class="bcs-link-btn">{{ __('Cancel') }}</a>
                </div>
            </form>
        </section>
    </div>

    @if ($successReference)
        <div class="bcs-success-modal" role="dialog" aria-modal="true" aria-labelledby="booking-success-title">
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
@endsection
