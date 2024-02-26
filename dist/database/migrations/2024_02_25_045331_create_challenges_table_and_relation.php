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
        Schema::create('challenges', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedInteger('completion_value');
            $table->string('asset_path');
            $table->timestamps();
        });

        Schema::create('character_data_picked_challenge', function (Blueprint $table) {
            $table->id();
            $table->foreignId('character_data_id')->constrained('character_data')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignUuid('catalog_item_id')->constrained();
            $table->foreignUuid('challenge_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('character_data_picked_challenge');
        Schema::dropIfExists('challenges');
    }
};
