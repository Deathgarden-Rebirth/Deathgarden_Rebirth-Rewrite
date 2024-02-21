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
        Schema::table('character_data', function (Blueprint $table) {
            $table->after('character', function (Blueprint $table) {
                $table->unsignedInteger('experience')->default(0);
                $table->unsignedInteger('level')->default(1);
                $table->unsignedtinyInteger('prestige_level')->default(0);
                $table->unsignedinteger('readout_version')->default(1);
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('character_data', function (Blueprint $table) {
            $table->after('character', function (Blueprint $table) {
                $table->dropColumn('experience');
                $table->dropColumn('level');
                $table->dropColumn('prestige_level');
                $table->dropColumn('readout_version');
            });
        });
    }
};
