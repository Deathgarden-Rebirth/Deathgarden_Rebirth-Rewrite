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
        Schema::create('catalog_item_challenge', function (Blueprint $table) {
            $table->foreignUuid('catalog_item_id')->references('id')->on('catalog_items')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignUuid('challenge_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();

            $table->primary(['catalog_item_id', 'challenge_id'], 'item_challenge_primary');
            $table->unique(['catalog_item_id', 'challenge_id'], 'item_challenge_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catalog_item_challenge');
    }
};
