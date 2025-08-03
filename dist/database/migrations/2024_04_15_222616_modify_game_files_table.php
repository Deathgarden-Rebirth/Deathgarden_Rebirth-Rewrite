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
        if (Schema::hasIndex('game_files', ['name', 'hash', 'version'])) {
            Schema::table('game_files', function (Blueprint $table) {
                $table->dropUnique(['name', 'hash', 'version']);
            });
        }

        //Schema doesn't support tinyint
        DB::statement("ALTER TABLE `game_files` CHANGE `version` `patchline` TINYINT UNSIGNED NOT NULL DEFAULT '1';");
        

        Schema::table('game_files', function (Blueprint $table) {
            $table->unique(['name', 'hash', 'patchline']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasIndex('game_files', ['name', 'hash', 'patchline'])) {
            Schema::table('game_files', function (Blueprint $table) {
                $table->dropUnique(['name', 'hash', 'patchline']);
            });
        }
        Schema::table('game_files', function (Blueprint $table) {
            $table->integer('patchline')->default(1)->change();
        });
        Schema::table('game_files', function (Blueprint $table) {
            $table->renameColumn('patchline', 'version');
        });
        Schema::table('game_files', function (Blueprint $table) {
            $table->unique(['name', 'hash', 'version']);
        });
    }
};
