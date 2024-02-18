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
        Schema::create('catalog_item_meta_gameplay_tags', function (Blueprint $table) {
            $table->foreignUuid('catalog_item_id')->references('id')->on('catalog_items')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('gameplay_tag', 255);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catalog_item_meta_gameplay_tags');
    }
};
