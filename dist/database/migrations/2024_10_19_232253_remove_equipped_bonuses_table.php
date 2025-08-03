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
        Schema::dropIfExists('character_data_equipped_bonuses');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('character_data_equipped_bonuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('character_data_id')->constrained('character_data')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignUuid('catalog_item_id')->constrained()->cascadeOnDelete()->cascadeOnDelete();
            $table->timestamps();
        });
    }
};
