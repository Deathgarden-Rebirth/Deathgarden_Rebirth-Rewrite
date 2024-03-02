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
        Schema::create('match_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->boolean('enabled');
            $table->unsignedMediumInteger('weight');
            $table->string('asset_path');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('match_configurations');
    }
};
