<?php

use App\Enums\Game\Characters;
use App\Enums\Game\CharacterState;
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
        Schema::create('archived_player_progressions', function (Blueprint $table) {
            $table->id();

            // without constraints because we want it to persist when the related row gets deleted.
            $table->foreignUuid('user_id');
            $table->foreignUuid('archived_game_id');

            $table->boolean('has_quit');
            $table->enum('played_character', array_column(Characters::cases(), 'value'));
            $table->enum('character_state', array_column(CharacterState::cases(), 'value'));

            $table->integer('gained_experience');
            $table->json('experience_events');

            $table->integer('gained_currency_a');
            $table->integer('gained_currency_b');
            $table->integer('gained_currency_c');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('archived_player_progressions');
    }
};
