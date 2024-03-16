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
        Schema::table('users', function (Blueprint $table) {
            $table->after('last_known_username', function (Blueprint $table) {
                $table->string('avatar_small')->nullable();
                $table->string('avatar_medium')->nullable();
                $table->string('avatar_full')->nullable();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('avatar_small');
            $table->dropColumn('avatar_medium');
            $table->dropColumn('avatar_full');
        });
    }
};
