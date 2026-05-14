<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('feedback', function (Blueprint $table): void {
            if (! Schema::hasColumn('feedback', 'guest_id')) {
                $table->foreignId('guest_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('feedback', 'submitter_type')) {
                $table->string('submitter_type', 30)->default('cadre')->after('cadre_reference')->index();
            }
        });

        Schema::create('feedback_ratings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('feedback_id')->constrained('feedback')->cascadeOnDelete();
            $table->string('category', 100);
            $table->string('rating', 20);
            $table->timestamps();

            $table->unique(['feedback_id', 'category']);
            $table->index(['category', 'rating']);
        });

        DB::table('feedback')
            ->whereNotNull('options')
            ->orderBy('id')
            ->select(['id', 'options', 'created_at', 'updated_at'])
            ->chunkById(100, function ($feedbackItems): void {
                foreach ($feedbackItems as $feedback) {
                    $options = json_decode((string) $feedback->options, true);

                    if (! is_array($options)) {
                        continue;
                    }

                    foreach ($options as $option) {
                        if (! is_string($option) || $option === '') {
                            continue;
                        }

                        DB::table('feedback_ratings')->insertOrIgnore([
                            'feedback_id' => $feedback->id,
                            'category' => $option,
                            'rating' => 'Good',
                            'created_at' => $feedback->created_at ?? now(),
                            'updated_at' => $feedback->updated_at ?? now(),
                        ]);
                    }
                }
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback_ratings');

        Schema::table('feedback', function (Blueprint $table): void {
            if (Schema::hasColumn('feedback', 'guest_id')) {
                $table->dropConstrainedForeignId('guest_id');
            }

            if (Schema::hasColumn('feedback', 'submitter_type')) {
                $table->dropColumn('submitter_type');
            }
        });
    }
};
