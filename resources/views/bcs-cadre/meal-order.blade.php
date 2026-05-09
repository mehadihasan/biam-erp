@extends('layouts.bcs-cadre')

@section('title', __('Meal Order'))

@section('content')
    <div class="bcs-page">
        <header class="bcs-page__header bcs-page__header--icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m16 2-2.5 2.5M21 7l-2.5 2.5M9 11l10 10M5 2v7a4 4 0 0 0 4 4h0a4 4 0 0 0 4-4V2M9 2v20"></path></svg>
            <div>
                <h1>{{ __('Meal Order') }}</h1>
                <p>{{ __('Order your daily meals.') }}</p>
            </div>
        </header>

        <section class="bcs-panel">
            <form method="post" action="{{ $editingOrder ? route('cadre.meals.update', $editingOrder) : route('cadre.meals.store') }}" class="bcs-form-grid">
                @csrf
                @if ($editingOrder)
                    @method('put')
                @endif
                <label>
                    <span>{{ __('Date') }} <em>*</em></span>
                    <input type="date" name="order_date" value="{{ old('order_date', optional($editingOrder?->order_date)->toDateString() ?? now()->toDateString()) }}" required>
                </label>
                <label>
                    <span>{{ __('Meal Type') }} <em>*</em></span>
                    <select name="meal_type" required>
                        @foreach (['breakfast' => 'Breakfast', 'lunch' => 'Lunch', 'dinner' => 'Dinner'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('meal_type', $editingOrder?->meal_type ?? 'breakfast') === $value)>{{ __($label) }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="bcs-form-grid__wide">
                    <span>{{ __('Menu Item') }} <em>*</em></span>
                    <select name="menu_item" required>
                        <option value="">{{ __('Select menu...') }}</option>
                        @foreach ($mealOptions as $item => $meta)
                            <option value="{{ $item }}" @selected(old('menu_item', $editingOrder?->menu_item) === $item)>{{ $item }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>{{ __('Qty') }}</span>
                    <input type="number" name="quantity" value="{{ old('quantity', $editingOrder?->quantity ?? 1) }}" min="1" max="20" required>
                </label>
                <div class="bcs-form-grid__actions">
                    <button type="submit" class="bcs-action-btn bcs-action-btn--compact">
                        <span aria-hidden="true">+</span>
                        {{ $editingOrder ? __('Update Order') : __('Place Order') }}
                    </button>
                    @if ($editingOrder)
                        <a href="{{ route('cadre.meals') }}" class="bcs-link-btn">{{ __('Cancel') }}</a>
                    @endif
                </div>
            </form>
        </section>

        <section class="bcs-table-card">
            <h2>{{ __('My Recent Orders') }}</h2>
            <div class="bcs-table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>{{ __('Ref') }}</th>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Meal') }}</th>
                            <th>{{ __('Qty') }}</th>
                            <th>{{ __('Total') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                            <tr>
                                <td>{{ $order->reference }}</td>
                                <td>{{ $order->order_date->toDateString() }}</td>
                                <td>{{ $order->meal_type }}</td>
                                <td>{{ $order->quantity }}</td>
                                <td>{{ __('BDT :amount', ['amount' => number_format($order->total)]) }}</td>
                                <td><span class="bcs-status">{{ $order->status }}</span></td>
                                <td class="bcs-row-actions">
                                    <a href="{{ route('cadre.meals', ['edit' => $order->id]) }}">{{ __('Edit') }}</a>
                                    <form method="post" action="{{ route('cadre.meals.destroy', $order) }}">
                                        @csrf
                                        @method('delete')
                                        <button type="submit">{{ __('Delete') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="bcs-table-empty">{{ __('No orders yet') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection
