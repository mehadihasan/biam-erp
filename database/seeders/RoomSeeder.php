<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rooms = [
            ['room_number' => '101', 'floor' => 1, 'room_type' => 'vip', 'capacity' => 2, 'base_rate' => 2500, 'status' => 'occupied', 'description' => 'Corner suite with garden view'],
            ['room_number' => '102', 'floor' => 1, 'room_type' => 'vip', 'capacity' => 2, 'base_rate' => 2500, 'status' => 'occupied', 'description' => 'Premium VIP suite'],
            ['room_number' => '103', 'floor' => 1, 'room_type' => 'ac', 'capacity' => 2, 'base_rate' => 1500, 'status' => 'available', 'description' => 'Standard AC double room'],
            ['room_number' => '104', 'floor' => 1, 'room_type' => 'ac', 'capacity' => 2, 'base_rate' => 1500, 'status' => 'maintenance', 'description' => 'Under renovation'],
            ['room_number' => '105', 'floor' => 1, 'room_type' => 'non_ac', 'capacity' => 2, 'base_rate' => 800, 'status' => 'occupied', 'description' => 'Economy room'],
            ['room_number' => '201', 'floor' => 2, 'room_type' => 'ac', 'capacity' => 2, 'base_rate' => 1500, 'status' => 'available', 'description' => 'Standard AC room'],
            ['room_number' => '202', 'floor' => 2, 'room_type' => 'non_ac', 'capacity' => 2, 'base_rate' => 800, 'status' => 'available', 'description' => 'Economy room floor 2'],
            ['room_number' => '203', 'floor' => 2, 'room_type' => 'vip', 'capacity' => 2, 'base_rate' => 2500, 'status' => 'maintenance', 'description' => 'VIP suite floor 2'],
        ];

        foreach ($rooms as $room) {
            Room::query()->updateOrCreate(
                ['room_number' => $room['room_number']],
                $room,
            );
        }
    }
}
