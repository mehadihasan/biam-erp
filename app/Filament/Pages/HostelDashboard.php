<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class HostelDashboard extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-home';

    protected static string | \UnitEnum | null $navigationGroup = 'Hostel Management';

    protected static ?string $title = 'Dashboard';

    protected static ?string $navigationLabel = 'Dashboard';

    protected static ?string $slug = 'hostel/dashboard';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.hostel-dashboard';

    public function getViewData(): array
    {
        return [
            'kpis' => [
                ['label' => 'Total Rooms', 'value' => 120, 'icon' => 'heroicon-o-building-office-2', 'color' => 'text-amber-600'],
                ['label' => 'Available Rooms', 'value' => 43, 'icon' => 'heroicon-o-home-modern', 'color' => 'text-emerald-600'],
                ['label' => 'Active Bookings', 'value' => 68, 'icon' => 'heroicon-o-calendar-days', 'color' => 'text-sky-600'],
                ['label' => 'Pending Approvals', 'value' => 9, 'icon' => 'heroicon-o-clock', 'color' => 'text-orange-600'],
            ],
            'mealStats' => [
                ['label' => 'Breakfast', 'count' => 74],
                ['label' => 'Lunch', 'count' => 81],
                ['label' => 'Supper', 'count' => 69],
            ],
            'recentBookings' => [
                ['ref' => 'BKG-1024', 'guest' => 'Sajid Hasan', 'room' => 'A-204', 'checkin' => '2026-05-03', 'status' => 'confirmed'],
                ['ref' => 'BKG-1023', 'guest' => 'Nusrat Jahan', 'room' => 'B-110', 'checkin' => '2026-05-03', 'status' => 'pending'],
                ['ref' => 'BKG-1022', 'guest' => 'Tanjim Rahman', 'room' => 'C-309', 'checkin' => '2026-05-02', 'status' => 'checked_in'],
            ],
            'pendingApprovals' => [
                ['guest' => 'Arif Mahmud', 'room' => 'A-303', 'date' => '2026-05-04'],
                ['guest' => 'Shanta Akter', 'room' => 'B-209', 'date' => '2026-05-04'],
            ],
            'rooms' => [
                ['number' => 'A-101', 'type' => 'VIP', 'status' => 'available'],
                ['number' => 'A-102', 'type' => 'AC', 'status' => 'occupied'],
                ['number' => 'A-103', 'type' => 'AC', 'status' => 'maintenance'],
                ['number' => 'B-201', 'type' => 'Non-AC', 'status' => 'available'],
                ['number' => 'B-202', 'type' => 'Non-AC', 'status' => 'occupied'],
                ['number' => 'C-301', 'type' => 'VIP', 'status' => 'available'],
                ['number' => 'C-302', 'type' => 'AC', 'status' => 'occupied'],
                ['number' => 'C-303', 'type' => 'Non-AC', 'status' => 'maintenance'],
            ],
        ];
    }
}
