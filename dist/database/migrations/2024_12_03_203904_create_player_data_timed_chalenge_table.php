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
        Schema::create('player_data_timed_challenge', function (Blueprint $table) {
            $table->foreignId('timed_challenge_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('player_data_id')->constrained('player_data')->cascadeOnDelete()->cascadeOnUpdate();
            $table->unsignedInteger('progress')->default(0);
            $table->boolean('claimed')->default(false);

            $table->timestamps();

            $table->unique(['timed_challenge_id', 'player_data_id'], 'unique_primary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_data_timed_challenge');
    }
};
