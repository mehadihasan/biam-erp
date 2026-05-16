@extends('layouts.bcs-cadre')

@section('title', __('Meal Order'))

@section('content')
    @php
        $minimumOrderDate = now()->addDay()->toDateString();
        $mealTypeOptions = ['breakfast' => 'Breakfast', 'lunch' => 'Lunch', 'dinner' => 'Dinner'];
        $selectedMealTypes = old('meal_types', $editingOrder ? [$editingOrder->meal_type] : []);
        $mealQuantities = old('quantities', []);
    @endphp

    <div class="bcs-page">
        <header class="bcs-page__header bcs-page__header--icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m16 2-2.5 2.5M21 7l-2.5 2.5M9 11l10 10M5 2v7a4 4 0 0 0 4 4h0a4 4 0 0 0 4-4V2M9 2v20"></path></svg>
            <div>
                <h1>{{ __('Meal Order') }}</h1>
                <p>{{ __('Order your daily meals.') }}</p>
            </div>
        </header>

        <section class="bcs-panel">
            <form method="post" action="{{ $editingOrder ? route($portalRoutePrefix.'.meals.update', $editingOrder) : route($portalRoutePrefix.'.meals.store') }}" class="bcs-form-grid bcs-meal-form">
                @csrf
                @if ($editingOrder)
                    @method('put')
                @endif
                <div class="bcs-meal-types">
                    <label class="bcs-date-field" data-bcs-date-field tabindex="0">
                        <span class="bcs-meal-field-label">{{ __('Date') }} <em>*</em></span>
                        <input data-bcs-date-input type="date" name="order_date" value="{{ old('order_date', optional($editingOrder?->order_date)->toDateString() ?? $minimumOrderDate) }}" min="{{ $minimumOrderDate }}" required>
                    </label>
                    <span class="bcs-meal-field-label">{{ __('Meal Type') }} <em>*</em></span>
                    <div class="bcs-meal-options">
                        @foreach ($mealTypeOptions as $value => $label)
                            @php
                                $quantityValue = $mealQuantities[$value]
                                    ?? ($editingOrder?->meal_type === $value ? $editingOrder->quantity : 1);
                            @endphp
                            <div class="bcs-meal-option">
                                <label class="bcs-meal-option-label">
                                    <input
                                        type="checkbox"
                                        name="meal_types[]"
                                        value="{{ $value }}"
                                        @checked(in_array($value, $selectedMealTypes, true))
                                    >
                                    <span>{{ __($label) }}</span>
                                </label>
                                <input
                                    type="number"
                                    name="quantities[{{ $value }}]"
                                    value="{{ $quantityValue }}"
                                    min="1"
                                    max="20"
                                    placeholder="{{ __('Quantity') }}"
                                    aria-label="{{ __($label) }} {{ __('quantity') }}"
                                    class="bcs-meal-quantity"
                                >
                            </div>
                        @endforeach
                    </div>
                    @error('meal_types') <span class="bcs-error">{{ $message }}</span> @enderror
                    @foreach (array_keys($mealTypeOptions) as $value)
                        @error("quantities.$value") <span class="bcs-error">{{ $message }}</span> @enderror
                    @endforeach
                </div>
                <div class="bcs-form-grid__actions">
                    <button type="submit" class="bcs-action-btn bcs-action-btn--compact">
                        <span aria-hidden="true">+</span>
                        {{ $editingOrder ? __('Update Order') : __('Place Order') }}
                    </button>
                    @if ($editingOrder)
                        <a href="{{ route($portalRoutePrefix.'.meals') }}" class="bcs-link-btn">{{ __('Cancel') }}</a>
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
                            <th>{{ __('Meal Types') }}</th>
                            <th>{{ __('Qty') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Created') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                            <tr>
                                <td>{{ $order->display_ref }}</td>
                                <td>{{ $order->order_date->toDateString() }}</td>
                                <td>{{ $mealTypeOptions[$order->meal_type] ?? \Illuminate\Support\Str::headline($order->meal_type) }}</td>
                                <td>{{ $order->quantity }}</td>
                                <td><span class="bcs-status">{{ $order->status }}</span></td>
                                <td>{{ $order->created_at?->format('h:i A') ?? '-' }}</td>
                                <td class="bcs-row-actions">
                                    <form method="post" action="{{ route($portalRoutePrefix.'.meals.destroy', $order) }}">
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
