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
        Schema::create('character_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_data_id')->constrained('player_data')->onDelete('cascade');
            $table->enum('character', array_column([...\App\Enums\Game\Hunter::cases(), ...\App\Enums\Game\Runner::cases()], 'value'));

            $table->timestamps();

            $table->unique(['player_data_id', 'character']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('character_data');
    }
};
