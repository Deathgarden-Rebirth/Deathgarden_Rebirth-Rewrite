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
        Schema::create('game_files', function (Blueprint $table) {
            $table->id();
            $table->string('name', 128);
            $table->string('game_path');
            $table->string('hash', 64); // SHA-256 hash is 64 characters long
            $table->integer('version')->default(1); // Default version is 1
            $table->tinyInteger('action')->default(1); //add = 1, delete = 2
            $table->timestamps();

            $table->unique(['name', 'hash', 'version']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_files');
    }
};
