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
        Schema::create('prestige_rewards', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('catalog_item_id')->constrained();
            $table->integer('amount');
        });

        Schema::create('catalog_prestige_rewards', function (Blueprint $table) {
            $table->foreignUuid('catalog_item_id')->references('id')->on('catalog_items')->cascadeOnDelete()->cascadeOnUpdate();
            $table->integer('cost_currency_a')->nullable()->default(null);
            $table->integer('cost_currency_b')->nullable()->default(null);
            $table->integer('cost_currency_c')->nullable()->default(null);
            $table->foreignId('prestige_reward_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catalog_prestige_rewards');
        Schema::dropIfExists('prestige_rewards');
    }
};
