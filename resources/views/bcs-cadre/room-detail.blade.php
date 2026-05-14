@extends('layouts.bcs-cadre')

@section('title', __('Room :number', ['number' => $room->room_number]))

@section('content')
    @php
        $typeLabel = match ($room->room_type) {
            'vip' => 'VIP',
            'ac' => 'AC',
            'non_ac' => 'Non-AC',
            default => ucfirst(str_replace('_', ' ', $room->room_type)),
        };
        $imageUrl = $room->listingThumbnailUrl();
        $baseRate = (float) $room->base_rate;
        $calculation = null;

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
    @endphp

    <div class="bcs-room-detail-page">
        <a href="{{ route('cadre.booking') }}" class="bcs-room-detail-back">
            &larr; {{ __('Back to rooms') }}
        </a>

        <div class="bcs-room-detail-layout">
            <div class="bcs-room-detail-media">
                <img src="{{ $imageUrl }}" alt="{{ __('Room :number', ['number' => $room->room_number]) }}">
            </div>

            <section class="bcs-room-detail-card">
                <div class="bcs-room-detail-heading">
                    <div>
                        <h1>{{ __('Room :number', ['number' => $room->room_number]) }}</h1>
                        <p>
                            <span aria-hidden="true">▦</span> {{ __('Capacity :capacity', ['capacity' => $room->capacity]) }}
                            <span aria-hidden="true">⌖</span> {{ __('Floor :floor', ['floor' => $room->floor]) }}
                        </p>
                    </div>
                    <span class="bcs-room-detail-badge">{{ $typeLabel }}</span>
                </div>

                <p class="bcs-room-detail-description">{{ $room->description ?: __('Available hostel room') }}</p>

                <div class="bcs-room-detail-price">
                    {{ __('BDT :amount', ['amount' => number_format($baseRate, 0)]) }}
                    <span>{{ __('/ night') }}</span>
                </div>

                <div class="bcs-room-detail-divider"></div>

                <form
                    method="post"
                    action="{{ route('cadre.bookings.store') }}"
                    class="bcs-room-booking"
                    data-room-price="{{ $baseRate }}"
                >
                    @csrf
                    <input type="hidden" name="room_id" value="{{ $room->id }}">
                    <input type="hidden" name="adults" value="{{ $adults }}">
                    <input type="hidden" name="number_of_rooms" value="{{ $numberOfRooms }}">
                    <input type="hidden" name="return_to" value="room_detail">

                    <h2>{{ __('Book this room') }}</h2>

                    <div class="bcs-room-booking-grid">
                        <label>
                            <span>{{ __('Check-in') }}</span>
                            <input data-room-check-in type="date" name="check_in_date" value="{{ $checkInDate }}" required>
                        </label>

                        <label>
                            <span>{{ __('Check-out') }}</span>
                            <input data-room-check-out type="date" name="check_out_date" value="{{ $checkOutDate }}" required>
                        </label>
                    </div>

                    @if ($calculation)
                        <div class="bcs-booking-summary bcs-room-rent-summary">
                            <div><strong>{{ __('Duration:') }}</strong> <span data-room-duration>{{ $calculation['duration_nights'] }} {{ \Illuminate\Support\Str::plural('night', $calculation['duration_nights']) }}</span></div>
                            <div><strong>{{ __('Rent Multiplier:') }}</strong> <span data-room-multiplier>{{ $calculation['rent_multiplier'] }}x</span></div>
                            <div><strong>{{ __('Base Rate:') }}</strong> <span data-room-base-rate>{{ __('BDT :amount/night', ['amount' => number_format($calculation['base_rate'], 0)]) }}</span></div>
                            <div><strong>{{ __('Calculated Rent:') }}</strong> <span data-room-calculated-rent>{{ __('BDT :amount', ['amount' => number_format($calculation['calculated_rent'], 0)]) }}</span></div>
                            <div><strong>{{ __('Booking Money (20%):') }}</strong> <span data-room-booking-money>{{ __('BDT :amount', ['amount' => number_format($calculation['booking_money'], 0)]) }} - {{ __('NON-REFUNDABLE') }}</span></div>
                            <div><strong>{{ __('Total Rent:') }}</strong> <span data-room-total-rent>{{ __('BDT :amount', ['amount' => number_format($calculation['total_rent'], 0)]) }}</span></div>
                        </div>
                    @else
                        <div class="bcs-booking-summary bcs-room-rent-summary">
                            {{ __('Please select a valid date range to calculate rent.') }}
                        </div>
                    @endif

                    <div class="bcs-room-price-row" hidden>
                        <span data-room-price-label>{{ __('1 night × BDT :amount', ['amount' => number_format($baseRate, 0)]) }}</span>
                        <strong data-room-price-total>{{ __('BDT :amount', ['amount' => number_format($baseRate, 0)]) }}</strong>
                    </div>

                    @if ($errors->any())
                        <div class="bcs-room-detail-error">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <button type="submit" class="bcs-room-booking-button">
                        <span aria-hidden="true">♧</span>
                        {{ __('Room Booking') }}
                    </button>
                </form>
            </section>
        </div>
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
                    <a href="{{ route('cadre.booking') }}" class="bcs-modal-btn bcs-modal-btn--secondary">{{ __('Book another') }}</a>
                    <a href="{{ route('cadre.booking') }}" class="bcs-modal-btn bcs-modal-btn--primary">{{ __('Done') }}</a>
                </div>
            </div>
        </div>
    @endif

    <script>
        (() => {
            const form = document.querySelector('[data-room-price]');
            if (! form) {
                return;
            }

            const price = Number(form.dataset.roomPrice || 0);
            const checkIn = form.querySelector('[data-room-check-in]');
            const checkOut = form.querySelector('[data-room-check-out]');
            const label = form.querySelector('[data-room-price-label]');
            const total = form.querySelector('[data-room-price-total]');
            const durationLabel = form.querySelector('[data-room-duration]');
            const multiplierLabel = form.querySelector('[data-room-multiplier]');
            const baseRateLabel = form.querySelector('[data-room-base-rate]');
            const calculatedRentLabel = form.querySelector('[data-room-calculated-rent]');
            const bookingMoneyLabel = form.querySelector('[data-room-booking-money]');
            const totalRentLabel = form.querySelector('[data-room-total-rent]');
            const formatter = new Intl.NumberFormat('en-US', { maximumFractionDigits: 0 });

            const updatePrice = () => {
                const start = checkIn.value ? new Date(`${checkIn.value}T00:00:00`) : null;
                const end = checkOut.value ? new Date(`${checkOut.value}T00:00:00`) : null;
                const diff = start && end ? Math.round((end - start) / 86400000) : 1;
                const nights = Math.max(1, diff);
                const multiplier = nights <= 3 ? 1 : nights <= 7 ? 2 : 3;
                const amount = nights * price * multiplier;
                const bookingMoney = amount * 0.20;

                if (durationLabel) {
                    durationLabel.textContent = `${nights} ${nights === 1 ? 'night' : 'nights'}`;
                    multiplierLabel.textContent = `${multiplier}x`;
                    baseRateLabel.textContent = `BDT ${formatter.format(price)}/night`;
                    calculatedRentLabel.textContent = `BDT ${formatter.format(amount)}`;
                    bookingMoneyLabel.textContent = `BDT ${formatter.format(bookingMoney)} - NON-REFUNDABLE`;
                    totalRentLabel.textContent = `BDT ${formatter.format(amount)}`;
                }

                label.textContent = `${nights} ${nights === 1 ? 'night' : 'nights'} × BDT ${formatter.format(price)}`;
                total.textContent = `BDT ${formatter.format(amount)}`;
            };

            checkIn.addEventListener('change', updatePrice);
            checkOut.addEventListener('change', updatePrice);
            updatePrice();
        })();
    </script>
@endsection
