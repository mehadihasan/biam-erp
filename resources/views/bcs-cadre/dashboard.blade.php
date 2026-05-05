@extends('layouts.bcs-cadre')

@section('title', __('Cadre Dashboard'))

@section('content')
    <div class="bcs-card bcs-dash">
        <div class="bcs-card__title-row">
            <span class="bcs-card__icon" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                </svg>
            </span>
            <div class="bcs-card__titles">
                <h1 class="bcs-card__heading">{{ __('Cadre dashboard') }}</h1>
                <p class="bcs-card__sub">{{ __('You are signed in with your GEMS cadre reference.') }}</p>
            </div>
        </div>
        <p class="bcs-dash__meta">
            {{ __('Cadre Reference ID:') }} <strong>{{ $cadreReference }}</strong>
        </p>
        <p class="bcs-dash__lead">
            {{ __('This is a placeholder dashboard. Replace this view with your cadre home when you are ready.') }}
        </p>
    </div>
@endsection
