<x-filament-panels::page>
    <style>
        .booking-form-card {
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: #ffffff;
            padding: 32px 28px 28px;
        }

        .booking-form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            column-gap: 28px;
            row-gap: 20px;
        }

        .booking-field {
            display: flex;
            min-width: 0;
            flex-direction: column;
            gap: 8px;
            color: #001b33;
            font-size: 16px;
            font-weight: 500;
        }

        .booking-control {
            width: 100%;
            min-width: 0;
            height: 42px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background-color: #f8fafc;
            padding: 8px 16px;
            color: #001b33;
            font-size: 16px;
            outline: none;
        }

        textarea.booking-control {
            min-height: 66px;
            height: auto;
        }

        .booking-note {
            color: #526783;
            font-size: 14px;
            font-weight: 400;
        }

        .booking-required,
        .booking-error {
            color: #dc2626;
        }

        .booking-error {
            font-size: 12px;
            font-weight: 400;
        }

        .booking-summary {
            margin-top: 30px;
            border-radius: 8px;
            background: #e5e9ef;
            padding: 18px 16px;
            color: #001b33;
            font-size: 14px;
            line-height: 1.7;
        }

        .booking-actions {
            display: flex;
            gap: 14px;
            margin-top: 24px;
        }

        .booking-primary,
        .booking-cancel {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 42px;
            border-radius: 8px;
            padding: 0 26px;
            font-size: 16px;
            font-weight: 500;
        }

        .booking-primary {
            border: 1px solid #173c63;
            background: #173c63;
            color: #ffffff;
        }

        .booking-cancel {
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #001b33;
            text-decoration: none;
        }

        @media (max-width: 767px) {
            .booking-form-card {
                padding: 24px 18px;
            }

            .booking-form-grid {
                grid-template-columns: 1fr;
                row-gap: 18px;
            }

            .booking-actions {
                flex-direction: column;
            }

            .booking-primary,
            .booking-cancel {
                width: 100%;
            }
        }
    </style>

    <div class="space-y-4">
        <div>
            <h2 class="text-xl font-bold text-gray-950 dark:text-white">New Booking</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Create a new room reservation</p>
        </div>

        <form wire:submit="save" class="booking-form-card dark:border-gray-800 dark:bg-gray-900">
            <div class="booking-form-grid">
                <div class="space-y-5">
                    <label class="booking-field">
                        <span>Guest <span class="booking-required">*</span></span>
                        <select wire:model.live="selectedGuestId" name="guest_id" class="booking-control">
                            <option value="">Select guest...</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">
                                    {{ $user->name }}{{ $user->designation?->name ? ' - ' . $user->designation->name : ' - No Designation' }}
                                </option>
                            @endforeach
                        </select>
                        @error('selectedGuestId') <span class="booking-error">{{ $message }}</span> @enderror
                    </label>

                    <label class="booking-field">
                        <span>Room Type</span>
                        <select wire:model.live="roomType" name="room_type" class="booking-control">
                            <option value="vip">VIP</option>
                            <option value="ac">AC</option>
                            <option value="non_ac">Non_AC</option>
                        </select>
                        @error('roomType') <span class="booking-error">{{ $message }}</span> @enderror
                    </label>

                    <label class="booking-field">
                        <span>Room <span class="booking-required">*</span></span>
                        <select wire:model.live="selectedRoomId" name="room_id" class="booking-control">
                            <option value="">Select room...</option>
                            @foreach ($rooms as $room)
                                <option value="{{ $room->id }}">
                                    {{ $room->room_number }} - BDT {{ number_format((float) $room->base_rate, 0) }}/night
                                </option>
                            @endforeach
                        </select>
                        @error('selectedRoomId') <span class="booking-error">{{ $message }}</span> @enderror
                    </label>

                    <label class="booking-field">
                        <span>Number of Rooms</span>
                        <input wire:model.live="numberOfRooms" name="number_of_rooms" type="number" min="1" class="booking-control">
                        @error('numberOfRooms') <span class="booking-error">{{ $message }}</span> @enderror
                    </label>
                </div>

                <div class="space-y-5">
                    <label class="booking-field">
                        <span>Check-in Date <span class="booking-required">*</span></span>
                        <input wire:model.live="checkInDate" name="check_in" type="date" class="booking-control">
                        <span class="booking-note">Check-in Time: 2:00 PM - Fixed by BIAM Policy</span>
                        @error('checkInDate') <span class="booking-error">{{ $message }}</span> @enderror
                    </label>

                    <label class="booking-field">
                        <span>Check-out Date <span class="booking-required">*</span></span>
                        <input wire:model.live="checkOutDate" name="check_out" type="date" class="booking-control">
                        <span class="booking-note">Check-out Time: 12:00 Noon - Fixed by BIAM Policy</span>
                        @error('checkOutDate') <span class="booking-error">{{ $message }}</span> @enderror
                    </label>

                    <label class="booking-field">
                        <span>Notes</span>
                        <textarea wire:model.live="notes" name="notes" class="booking-control"></textarea>
                        @error('notes') <span class="booking-error">{{ $message }}</span> @enderror
                    </label>
                </div>
            </div>

            @if ($this->calculation)
                @php
                    $calculation = $this->calculation;
                    $multiplierLabel = match ($calculation['rent_multiplier']) {
                        1 => '1x (days 1-3)',
                        2 => '2x (days 4-7)',
                        default => '3x (days 8+)',
                    };
                @endphp

                <div class="booking-summary">
                    <div><strong>Duration:</strong> {{ $calculation['duration_nights'] }} {{ \Illuminate\Support\Str::plural('night', $calculation['duration_nights']) }}</div>
                    <div><strong>Rent Multiplier:</strong> {{ $multiplierLabel }}</div>
                    <div><strong>Base Rate:</strong> BDT {{ number_format($calculation['base_rate'], 0) }}/night</div>
                    <div><strong>Calculated Rent:</strong> BDT {{ number_format($calculation['calculated_rent'], 0) }}</div>
                    <div><strong>Booking Money (20%):</strong> BDT {{ number_format($calculation['booking_money'], 0) }} - NON-REFUNDABLE</div>
                    <div><strong>Total Rent:</strong> BDT {{ number_format($calculation['total_rent'], 0) }}</div>
                </div>
            @elseif ($selectedRoomId && $checkInDate && $checkOutDate)
                <div class="booking-summary">
                    Please select a valid date range to calculate rent.
                </div>
            @endif

            <div class="booking-actions">
                <button class="booking-primary">Create Booking</button>
                <a href="{{ \App\Filament\Pages\Hostel\Bookings\AllBookings::getUrl(panel: 'admin') }}" class="booking-cancel">Cancel</a>
            </div>
        </form>
    </div>
</x-filament-panels::page>
