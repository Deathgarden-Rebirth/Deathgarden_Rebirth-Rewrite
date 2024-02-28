<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tables = [
        'character_data_equipped_perks',
        'character_data_equipped_weapons',
        'character_data_equipment',
        'character_data_equipped_bonuses',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->tables as $table)

        Schema::create($table, function (Blueprint $table) {
            $table->id();
            $table->foreignId('character_data_id')->constrained('character_data')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignUuid('catalog_item_id')->constrained()->cascadeOnDelete()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ($this->tables as $table) {
            Schema::dropIfExists($table);
        }
    }
};
