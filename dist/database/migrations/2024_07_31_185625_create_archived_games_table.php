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
        Schema::create('archived_games', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->enum('dominant_faction',array_column(\App\Enums\Game\Faction::cases(), 'value'));

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('archived_games');
    }
};
