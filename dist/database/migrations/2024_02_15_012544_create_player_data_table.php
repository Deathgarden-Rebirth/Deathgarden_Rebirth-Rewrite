<?php

use App\Enums\Game\Faction;
use App\Enums\Game\Hunter;
use App\Enums\Game\Runner;
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
        Schema::create('player_data', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')->constrained();

            $table->enum('last_faction', array_column(Faction::cases(), 'value'));
            $table->enum('last_hunter', array_column(Hunter::cases(), 'value'));
            $table->enum('last_runner', array_column(Runner::cases(), 'value'));

            $table->boolean('has_played_tutorial');
            $table->boolean('has_played_dg_1');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_data');
    }
};
