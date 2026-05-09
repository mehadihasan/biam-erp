<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->json('images')->nullable()->after('description');
        });

        if (Schema::hasColumn('rooms', 'picture_path')) {
            foreach (DB::table('rooms')->whereNotNull('picture_path')->get() as $row) {
                $paths = [$row->picture_path];
                DB::table('rooms')->where('id', $row->id)->update([
                    'images' => json_encode($paths),
                ]);
            }

            Schema::table('rooms', function (Blueprint $table) {
                $table->dropColumn('picture_path');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->string('picture_path')->nullable()->after('description');
        });

        foreach (DB::table('rooms')->get() as $row) {
            $decoded = json_decode($row->images ?? '[]', true);
            $first = is_array($decoded) ? ($decoded[0] ?? null) : null;
            DB::table('rooms')->where('id', $row->id)->update([
                'picture_path' => $first,
            ]);
        }

        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn('images');
        });
    }
};
