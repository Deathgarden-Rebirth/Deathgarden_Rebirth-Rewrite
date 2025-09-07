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
        Schema::table('archived_player_progressions', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('played_character');
            $table->index('gained_experience');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('archived_player_progressions', function (Blueprint $table) {
            //
        });
    }
};
