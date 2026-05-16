@extends('layouts.bcs-cadre')

@section('title', __('Booking & Reservation'))

@section('content')
    <div class="bcs-page">
        <header class="bcs-page__header">
            <h1>{{ __('Choose your room') }}</h1>
            <p>{{ __('Browse available rooms and submit your booking.') }}</p>
        </header>

        <form method="get" action="{{ route($portalRoutePrefix.'.booking') }}" class="bcs-search-card" aria-label="{{ __('Booking search') }}">
            <label class="bcs-search-field" data-bcs-date-field tabindex="0">
                <span class="bcs-search-field__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"></rect><path d="M16 2v4M8 2v4M3 10h18M8 14h4M8 18h8"></path></svg>
                </span>
                <div class="bcs-search-field__content">
                    <input data-bcs-date-input type="date" name="check_in" value="{{ $filters['check_in'] }}" aria-label="{{ __('Check-in date') }}">
                    <span>{{ $filters['check_in'] ? \Illuminate\Support\Carbon::parse($filters['check_in'])->format('l') : __('Check-in date') }}</span>
                </div>
            </label>

            <label class="bcs-search-field" data-bcs-date-field tabindex="0">
                <span class="bcs-search-field__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"></rect><path d="M16 2v4M8 2v4M3 10h18M8 14l2 2 4-4"></path></svg>
                </span>
                <div class="bcs-search-field__content">
                    <input data-bcs-date-input type="date" name="check_out" value="{{ $filters['check_out'] }}" aria-label="{{ __('Check-out date') }}">
                    <span>{{ $filters['check_out'] ? \Illuminate\Support\Carbon::parse($filters['check_out'])->format('l') : __('Check-out date') }}</span>
                </div>
            </label>

            <label class="bcs-search-field bcs-search-field--select">
                <span class="bcs-search-field__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                </span>
                <div class="bcs-search-combo">
                    <select name="adults" aria-label="{{ __('Adults') }}">
                        @for ($adult = 1; $adult <= 6; $adult++)
                            <option value="{{ $adult }}" @selected((int) $filters['adults'] === $adult)>
                                {{ trans_choice(':count adult|:count adults', $adult, ['count' => $adult]) }}
                            </option>
                        @endfor
                    </select>
                    <select name="room_type" aria-label="{{ __('Room type') }}">
                        <option value="">{{ __('Room type') }}</option>
                        @foreach ($roomTypes as $roomType => $roomTypeLabel)
                            <option value="{{ $roomType }}" @selected($filters['room_type'] === $roomType)>
                                {{ $roomTypeLabel }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </label>

            <button type="submit" class="bcs-action-btn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.3-4.3"></path></svg>
                {{ __('SEARCH') }}
            </button>
        </form>

        <section class="bcs-room-grid">
            @forelse ($rooms as $room)
                @php
                    $typeLabel = match ($room->room_type) {
                        'vip' => 'VIP',
                        'ac' => 'AC',
                        'non_ac' => 'Non-AC',
                        default => ucfirst(str_replace('_', ' ', $room->room_type)),
                    };
                @endphp
                <article class="bcs-room-card">
                    <img src="{{ $room->listingThumbnailUrl() }}" alt="{{ __('Room :number', ['number' => $room->room_number]) }}">
                    <div class="bcs-room-card__body">
                        <div class="bcs-room-card__title">
                            <h2>{{ __('Room :number', ['number' => $room->room_number]) }}</h2>
                            <span>{{ $typeLabel }}</span>
                        </div>
                        <p class="bcs-room-card__meta">{{ __('Capacity :capacity', ['capacity' => $room->capacity]) }} &bull; {{ __('Floor :floor', ['floor' => $room->floor]) }}</p>
                        <p class="bcs-room-card__desc">{{ $room->description ?: __('Available hostel room') }}</p>
                        <p class="bcs-room-card__price">{{ __('BDT :amount / night', ['amount' => number_format((float) $room->base_rate, 0)]) }}</p>
                        <div class="bcs-room-card__actions">
                            <a
                                href="{{ route($portalRoutePrefix.'.rooms.show', [
                                    'room' => $room->id,
                                    'check_in' => $filters['check_in'],
                                    'check_out' => $filters['check_out'],
                                    'adults' => $filters['adults'],
                                    'room_type' => $filters['room_type'],
                                ]) }}"
                                class="bcs-card-btn bcs-card-btn--secondary"
                            >
                                {{ __('Room details') }}
                            </a>
                            <a
                                href="{{ route($portalRoutePrefix.'.bookings.new', [
                                    'room' => $room->id,
                                    'check_in' => $filters['check_in'],
                                    'check_out' => $filters['check_out'],
                                    'adults' => $filters['adults'],
                                ]) }}"
                                class="bcs-card-btn"
                            >
                                {{ __('Book now') }}
                            </a>
                        </div>
                    </div>
                </article>
            @empty
                <div class="bcs-empty">{{ __('No available rooms found.') }}</div>
            @endforelse
        </section>
    </div>
@endsection
