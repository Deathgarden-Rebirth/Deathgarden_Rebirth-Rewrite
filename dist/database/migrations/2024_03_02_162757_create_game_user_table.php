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
        Schema::create('game_user', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('game_id')->constrained();
            $table->foreignUuid('user_id')->constrained();
            $table->enum('side',array_column(MatchmakingSide::cases(), 'value'))->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_user');
    }
};
