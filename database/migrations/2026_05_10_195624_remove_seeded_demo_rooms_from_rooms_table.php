<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $demoRooms = [
            '101' => 'Corner suite with garden view',
            '102' => 'Premium VIP suite',
            '103' => 'Standard AC double room',
            '104' => 'Under renovation',
            '105' => 'Economy room',
            '201' => 'Standard AC room',
            '202' => 'Economy room floor 2',
            '203' => 'VIP suite floor 2',
        ];

        foreach ($demoRooms as $roomNumber => $description) {
            DB::table('rooms')
                ->where('room_number', $roomNumber)
                ->where('description', $description)
                ->delete();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
