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
        Schema::create('challenge_player_data', function (Blueprint $table) {
            $table->foreignUuid('challenge_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('player_data_id')->constrained('player_data')->cascadeOnDelete()->cascadeOnUpdate();
            $table->unsignedInteger('progress')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('challenge_player_data');
    }
};
