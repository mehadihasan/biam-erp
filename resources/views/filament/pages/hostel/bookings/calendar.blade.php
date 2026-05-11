<x-filament-panels::page>
    <style>
        .booking-calendar-shell {
            overflow: hidden;
            border: 1px solid #d7dee8;
            border-radius: 8px;
            background: #ffffff;
        }

        .booking-calendar-toolbar {
            display: grid;
            grid-template-columns: 40px 1fr 40px;
            align-items: center;
            gap: 12px;
            padding: 18px 16px;
        }

        .booking-calendar-nav {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            border: 0;
            border-radius: 6px;
            background: transparent;
            color: #001b33;
            font-size: 26px;
            line-height: 1;
        }

        .booking-calendar-title {
            text-align: center;
            color: #001b33;
            font-size: 18px;
            font-weight: 800;
        }

        .booking-calendar-legend {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            padding: 0 16px 12px;
            color: #334155;
            font-size: 13px;
        }

        .booking-calendar-legend span {
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .booking-calendar-dot {
            width: 12px;
            height: 12px;
            border-radius: 4px;
        }

        .booking-calendar-dot--vip {
            background: #f5ad18;
        }

        .booking-calendar-dot--ac {
            background: #5d9ff0;
        }

        .booking-calendar-dot--non-ac {
            background: #9aa3af;
        }

        .booking-calendar-grid {
            display: grid;
            min-width: 980px;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            border-top: 1px solid #cbd5e1;
        }

        .booking-calendar-weekday {
            border-right: 1px solid #cbd5e1;
            background: #e7ebf0;
            padding: 9px 8px;
            text-align: center;
            color: #52647a;
            font-size: 12px;
            font-weight: 700;
        }

        .booking-calendar-weekday:nth-child(7n) {
            border-right: 0;
        }

        .booking-calendar-day {
            min-height: 116px;
            border-right: 1px solid #cbd5e1;
            border-top: 1px solid #cbd5e1;
            padding: 10px 8px;
            background: #ffffff;
        }

        .booking-calendar-day:nth-child(7n) {
            border-right: 0;
        }

        .booking-calendar-day--muted {
            background: #fbfcfe;
        }

        .booking-calendar-day__number {
            margin-bottom: 8px;
            color: #001b33;
            font-size: 13px;
            font-weight: 600;
        }

        .booking-calendar-day--muted .booking-calendar-day__number {
            color: #94a3b8;
        }

        .booking-calendar-event {
            overflow: hidden;
            height: 18px;
            margin-bottom: 4px;
            border-radius: 4px;
            padding: 2px 5px;
            color: #001b33;
            font-size: 10px;
            line-height: 14px;
            white-space: nowrap;
            text-overflow: ellipsis;
        }

        .booking-calendar-event--vip {
            background: #f5ad18;
        }

        .booking-calendar-event--ac {
            background: #5d9ff0;
        }

        .booking-calendar-event--non-ac {
            background: #9aa3af;
        }

        .booking-calendar-more {
            color: #52647a;
            font-size: 10px;
        }

        .booking-calendar-empty {
            padding: 24px 16px;
            color: #64748b;
            font-size: 14px;
        }

        @media (max-width: 768px) {
            .booking-calendar-toolbar {
                grid-template-columns: 34px 1fr 34px;
                padding: 14px 12px;
            }

            .booking-calendar-title {
                font-size: 16px;
            }

            .booking-calendar-grid {
                min-width: 760px;
            }

            .booking-calendar-day {
                min-height: 96px;
            }
        }
    </style>

    <div class="space-y-4">
        <div>
            <h2 class="text-xl font-bold text-gray-950 dark:text-white">Booking Calendar</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Monthly room booking view</p>
        </div>

        <div class="booking-calendar-shell">
            <div class="booking-calendar-toolbar">
                <button type="button" wire:click="previousMonth" class="booking-calendar-nav" aria-label="Previous month">&lsaquo;</button>
                <div class="booking-calendar-title">{{ $this->monthLabel() }}</div>
                <button type="button" wire:click="nextMonth" class="booking-calendar-nav" aria-label="Next month">&rsaquo;</button>
            </div>

            <div class="booking-calendar-legend">
                <span><i class="booking-calendar-dot booking-calendar-dot--vip"></i> VIP</span>
                <span><i class="booking-calendar-dot booking-calendar-dot--ac"></i> AC</span>
                <span><i class="booking-calendar-dot booking-calendar-dot--non-ac"></i> Non-AC</span>
            </div>

            @if ($this->rooms->isEmpty())
                <div class="booking-calendar-empty">No rooms found.</div>
            @else
                <div class="overflow-x-auto">
                    <div class="booking-calendar-grid">
                        @foreach (['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $dayName)
                            <div class="booking-calendar-weekday">{{ $dayName }}</div>
                        @endforeach

                        @foreach ($this->calendarDays() as $day)
                            @php
                                $bookings = $this->bookingsForDay($day);
                                $visibleBookings = $bookings->take(3);
                                $hiddenCount = max(0, $bookings->count() - $visibleBookings->count());
                            @endphp

                            <div class="booking-calendar-day {{ $day->isSameMonth($this->monthStart()) ? '' : 'booking-calendar-day--muted' }}">
                                <div class="booking-calendar-day__number">{{ $day->day }}</div>

                                @foreach ($visibleBookings as $booking)
                                    <div
                                        class="{{ $this->bookingTypeClass($booking) }}"
                                        title="Room {{ $booking->room?->room_number }} - {{ $this->roomTypeLabel($booking->room?->room_type ?? $booking->room_type) }}"
                                    >
                                        {{ $booking->room?->room_number ?? '-' }}
                                    </div>
                                @endforeach

                                @if ($hiddenCount > 0)
                                    <div class="booking-calendar-more">+{{ $hiddenCount }} more</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>
