@extends('layouts.bcs-cadre')

@section('title', __('Booking & Reservation'))

@section('content')
    <div class="bcs-page">
        <header class="bcs-page__header">
            <h1>{{ __('Choose your room') }}</h1>
            <p>{{ __('Browse available rooms and submit your booking.') }}</p>
        </header>

        <form method="get" action="{{ route('cadre.booking') }}" class="bcs-search-card" aria-label="{{ __('Booking search') }}">
            <label class="bcs-search-field">
                <span class="bcs-search-field__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"></rect><path d="M16 2v4M8 2v4M3 10h18M8 14h4M8 18h8"></path></svg>
                </span>
                <div>
                    <strong>{{ $filters['check_in'] ? \Illuminate\Support\Carbon::parse($filters['check_in'])->format('m/d/Y') : __('Check-in') }}</strong>
                    <span>{{ $filters['check_in'] ? \Illuminate\Support\Carbon::parse($filters['check_in'])->format('l') : __('Select date') }}</span>
                </div>
                <input type="date" name="check_in" value="{{ $filters['check_in'] }}" aria-label="{{ __('Check-in date') }}">
            </label>

            <label class="bcs-search-field">
                <span class="bcs-search-field__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"></rect><path d="M16 2v4M8 2v4M3 10h18M8 14l2 2 4-4"></path></svg>
                </span>
                <div>
                    <strong>{{ $filters['check_out'] ? \Illuminate\Support\Carbon::parse($filters['check_out'])->format('m/d/Y') : __('Check-out') }}</strong>
                    <span>{{ $filters['check_out'] ? \Illuminate\Support\Carbon::parse($filters['check_out'])->format('l') : __('Select date') }}</span>
                </div>
                <input type="date" name="check_out" value="{{ $filters['check_out'] }}" aria-label="{{ __('Check-out date') }}">
            </label>

            <label class="bcs-search-field bcs-search-field--select">
                <span class="bcs-search-field__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 21V5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v16"></path><path d="M9 21v-6h6v6"></path><path d="M8 7h.01M16 7h.01M8 11h.01M16 11h.01"></path></svg>
                </span>
                <div>
                    <strong>{{ __('Room type') }}</strong>
                    <span>{{ __('Optional') }}</span>
                </div>
                <select name="room_type" aria-label="{{ __('Room type') }}">
                    <option value="">{{ __('All room types') }}</option>
                    <option value="vip" @selected($filters['room_type'] === 'vip')>{{ __('VIP') }}</option>
                    <option value="ac" @selected($filters['room_type'] === 'ac')>{{ __('AC') }}</option>
                    <option value="non_ac" @selected($filters['room_type'] === 'non_ac')>{{ __('Non-AC') }}</option>
                </select>
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
                            <a href="{{ \App\Filament\Pages\Hostel\Rooms\RoomDetail::urlForRoom($room->id) }}&cadre=1" class="bcs-card-btn bcs-card-btn--secondary">{{ __('Room details') }}</a>
                            <a
                                href="{{ \App\Filament\Pages\Hostel\Bookings\NewBooking::getUrl([
                                    'room_id' => $room->id,
                                    'check_in' => $filters['check_in'],
                                    'check_out' => $filters['check_out'],
                                    'cadre' => 1,
                                ], panel: 'admin') }}"
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
