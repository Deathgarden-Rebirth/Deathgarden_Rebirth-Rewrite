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
        Schema::create('catalog_item_player_data', function (Blueprint $table) {
            $table->foreignUuid('catalog_item_id')->constrained();
            $table->foreignId('player_data_id')->constrained('player_data');
            $table->timestamps();

            $table->unique(['catalog_item_id', 'player_data_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catalog_item_player_data');
    }
};
