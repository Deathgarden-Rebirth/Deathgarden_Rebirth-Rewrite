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
        Schema::create('picked_challenges', function (Blueprint $table) {
            $table->uuid('id')->unique();
            $table->foreignId('character_data_id')->constrained('character_data')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignUuid('catalog_item_id')->constrained();
            $table->string('asset_path');
            $table->unsignedInteger('completion_value');
            $table->unsignedInteger('progress');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('picked_challenges');
    }
};
