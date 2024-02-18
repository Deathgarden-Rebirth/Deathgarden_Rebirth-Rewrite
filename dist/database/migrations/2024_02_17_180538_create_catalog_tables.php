<?php

use App\Enums\Game\ItemGroupType;
use App\Enums\Game\ItemOrigin;
use App\Enums\Game\ItemQuality;
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
        Schema::create('catalog_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('display_name', 255);
            $table->integer('initial_quantity');
            $table->boolean('consumable');
            $table->integer('default_cost_currency_a')->nullable()->default(null);
            $table->integer('default_cost_currency_b')->nullable()->default(null);
            $table->integer('default_cost_currency_c')->nullable()->default(null);
            $table->boolean('purchasable');

            // Metadata
            $table->integer('meta_min_player_level');
            $table->integer('meta_min_character_level');
            $table->boolean('meta_is_unbreakable_fullset');
            $table->enum('meta_origin', array_column(ItemOrigin::cases(), 'value'));
            $table->enum('meta_quality', array_column(ItemQuality::cases(), 'value'));
            $table->enum('meta_group_type', array_column(ItemGroupType::cases(), 'value'));
            $table->foreignUuid('meta_following_item')->nullable();
            $table->foreignUuid('meta_prerequisite_item')->nullable();
            $table->boolean('meta_has_bundle_items');
            $table->boolean('has_reward_bundle_items');

            $table->timestamps();
        });

        Schema::table('catalog_items', function (Blueprint $table) {
            $table->foreign('meta_following_item')->references('id')->on('catalog_items')->cascadeOnDelete();
            $table->foreign('meta_prerequisite_item')->references('id')->on('catalog_items')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catalog_items');
    }
};
