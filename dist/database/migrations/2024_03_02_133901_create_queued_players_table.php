<?php

use App\Enums\Game\Matchmaking\MatchmakingSide;
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
        Schema::create('queued_players', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')->constrained()
                ->cascadeOnDelete()->cascadeOnUpdate();

            $table->enum('side',array_column(MatchmakingSide::cases(), 'value'))->index();

            $table->foreignId('queued_player_id')
                ->nullable()
                ->comment('Sets if the queued Player is part of another Players Party')
                ->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamps();

            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('queued_players');
    }
};
