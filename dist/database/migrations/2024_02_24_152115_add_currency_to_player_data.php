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
                $table->unsignedInteger('currency_a');
                $table->unsignedInteger('currency_b');
                $table->unsignedInteger('currency_c');
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('player_data', function (Blueprint $table) {
            $table->dropColumn('currency_a');
            $table->dropColumn('currency_b');
            $table->dropColumn('currency_c');
        });
    }
};
