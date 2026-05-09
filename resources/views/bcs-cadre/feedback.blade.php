@extends('layouts.bcs-cadre')

@section('title', __('Feedback'))

@section('content')
    <div class="bcs-page">
        <header class="bcs-page__header bcs-page__header--icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 15a4 4 0 0 1-4 4H7l-4 4V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z"></path></svg>
            <div>
                <h1>{{ __('Feedback') }}</h1>
                <p>{{ __('Share your experience or report an issue.') }}</p>
            </div>
        </header>

        <section class="bcs-panel">
            <form method="post" action="{{ $editingFeedback ? route('cadre.feedback.update', $editingFeedback) : route('cadre.feedback.store') }}" class="bcs-feedback-form">
                @csrf
                @if ($editingFeedback)
                    @method('put')
                @endif

                <div class="bcs-checkbox-grid">
                    @foreach ($feedbackOptions as $option)
                        <label class="bcs-checkbox">
                            <input
                                type="checkbox"
                                name="options[]"
                                value="{{ $option }}"
                                @checked(in_array($option, old('options', $editingFeedback?->options ?? []), true))
                            >
                            <span>{{ $option }}</span>
                        </label>
                    @endforeach
                </div>

                <div class="bcs-feedback-actions">
                    <button type="submit" class="bcs-action-btn bcs-action-btn--compact">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m22 2-7 20-4-9-9-4Z"></path><path d="M22 2 11 13"></path></svg>
                        {{ $editingFeedback ? __('Update Feedback') : __('Submit Feedback') }}
                    </button>
                    @if ($editingFeedback)
                        <a href="{{ route('cadre.feedback') }}" class="bcs-link-btn">{{ __('Cancel') }}</a>
                    @endif
                </div>
            </form>
        </section>

        <section class="bcs-table-card">
            <h2>{{ __('Recent Feedback') }}</h2>
            <div class="bcs-table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>{{ __('Selected Options') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($feedbackItems as $item)
                            <tr>
                                <td>{{ implode(', ', $item->options ?? []) }}</td>
                                <td><span class="bcs-status">{{ $item->status }}</span></td>
                                <td>{{ $item->created_at->toDateString() }}</td>
                                <td class="bcs-row-actions">
                                    <a href="{{ route('cadre.feedback', ['edit' => $item->id]) }}">{{ __('Edit') }}</a>
                                    <form method="post" action="{{ route('cadre.feedback.destroy', $item) }}">
                                        @csrf
                                        @method('delete')
                                        <button type="submit">{{ __('Delete') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="bcs-table-empty">{{ __('No feedback yet') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection
