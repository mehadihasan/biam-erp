@extends('layouts.bcs-cadre')

@section('title', __('Billing'))

@section('content')
    <div class="bcs-page">
        <header class="bcs-page__header bcs-page__header--icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"></rect><path d="M3 10h18"></path></svg>
            <div>
                <h1>{{ __('Billing') }}</h1>
                <p>{{ __('Your invoices and payment history.') }}</p>
            </div>
        </header>

        <section class="bcs-billing-stats">
            <article>
                <span>{{ __('Total Bookings') }}</span>
                <strong>{{ $totalBookings }}</strong>
            </article>
            <article>
                <span>{{ __('Outstanding') }}</span>
                <strong>{{ __('BDT :amount', ['amount' => number_format($outstanding)]) }}</strong>
            </article>
            <article>
                <span>{{ __('Paid') }}</span>
                <strong class="bcs-paid">{{ __('BDT :amount', ['amount' => number_format($paid)]) }}</strong>
            </article>
        </section>

        <section class="bcs-table-card">
            <h2>{{ __('Bookings & Charges') }}</h2>
            <div class="bcs-table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>{{ __('Ref') }}</th>
                            <th>{{ __('Check-in') }}</th>
                            <th>{{ __('Check-out') }}</th>
                            <th>{{ __('Days') }}</th>
                            <th>{{ __('Total Rent') }}</th>
                            <th>{{ __('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($charges as $charge)
                            <tr>
                                <td>{{ $charge['reference'] }}</td>
                                <td>{{ $charge['check_in'] }}</td>
                                <td>{{ $charge['check_out'] }}</td>
                                <td>{{ $charge['days'] }}</td>
                                <td>{{ __('BDT :amount', ['amount' => number_format($charge['total'])]) }}</td>
                                <td><span class="bcs-status">{{ $charge['status'] }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="bcs-table-empty">{{ __('No billing data yet') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection
