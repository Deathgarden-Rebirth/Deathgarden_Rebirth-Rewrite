<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('quitter_states', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_data_id')->constrained('player_data')
                ->cascadeOnDelete()->cascadeOnUpdate();
            $table->unsignedInteger('stay_match_streak');
            $table->unsignedInteger('stay_match_streak_previous');
            $table->unsignedInteger('quits');
            $table->unsignedInteger('quit_match_streak');
            $table->unsignedInteger('quit_match_streak_previous');
            $table->unsignedInteger('strikes_left');
            $table->boolean('has_quit_once');
            $table->timestamp('strike_refresh_time')->nullable();
            $table->timestamps();

            $table->unique('player_data_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quitter_states');
    }
};
