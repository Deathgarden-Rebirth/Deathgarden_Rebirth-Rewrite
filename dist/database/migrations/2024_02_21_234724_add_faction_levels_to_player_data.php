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
        Schema::table('player_data', function (Blueprint $table) {
            $table->after('last_runner', function (Blueprint $table) {
                $table->unsignedInteger('hunter_faction_level')->default(1);
                $table->unsignedInteger('hunter_faction_experience')->default(0);

                $table->unsignedInteger('runner_faction_level')->default(1);
                $table->unsignedInteger('runner_faction_experience')->default(0);
                $table->unsignedInteger('readout_version')->default(1);
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('player_data', function (Blueprint $table) {
            $table->dropColumn('hunter_faction_level');
            $table->dropColumn('hunter_faction_experience');
            $table->dropColumn('runner_faction_level');
            $table->dropColumn('runner_faction_experience');
            $table->dropColumn('readout_version');
        });
    }
};
