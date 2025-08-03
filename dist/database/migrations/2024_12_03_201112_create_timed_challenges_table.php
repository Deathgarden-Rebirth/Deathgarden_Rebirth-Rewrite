<?php

use App\Enums\Game\ChallengeType;
use App\Enums\Game\Faction;
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
        Schema::create('timed_challenges', function (Blueprint $table) {
            $table->id();
            $table->enum('type', array_column(ChallengeType::cases(), 'value'));
            $table->string('blueprint_path');
            $table->enum('faction', array_column(Faction::cases(), 'value'));
            $table->integer('completion_value');
            $table->datetime('start_time');
            $table->datetime('end_time');
            $table->json('rewards')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timed_challenges');
    }
};
