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
        Schema::create('catalog_item_assignment', function (Blueprint $table) {
            $table->foreignUuid('catalog_item_id')->references('id')->on('catalog_items')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignUuid('assigned_item_id')->references('id')->on('catalog_items')->cascadeOnDelete()->cascadeOnUpdate();

            $table->primary(['catalog_item_id', 'assigned_item_id'], 'assigned_item_primary');
            $table->unique(['catalog_item_id', 'assigned_item_id'], 'assigned_item_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catalog_item_assignment');
    }
};
