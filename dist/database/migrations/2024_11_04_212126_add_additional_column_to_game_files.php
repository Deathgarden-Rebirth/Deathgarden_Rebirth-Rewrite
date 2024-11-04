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
        //Schema doesn't support tinyint
        DB::statement("ALTER TABLE `game_files` ADD COLUMN `is_additional` TINYINT UNSIGNED NOT NULL DEFAULT '0' AFTER `action`;");

        if (Schema::hasIndex('game_files', ['name', 'hash', 'version'])) {
            Schema::table('game_files', function (Blueprint $table) {
                $table->dropUnique(['name', 'hash', 'version']);
            });
        }

        Schema::table('game_files', function (Blueprint $table) {
            $table->renameColumn('name', 'filename');
            $table->unique(['filename', 'hash', 'patchline']);
        });
        
        Schema::table('game_files', function (Blueprint $table) {
            $table->string('name', 128)->nullable()->after('filename');
            $table->string('description', 255)->nullable()->after('name');
            
            $table->unique(['name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('game_files', function (Blueprint $table) {
            $table->dropUnique(['filename', 'hash', 'patchline']);
            $table->dropUnique(['name']);
            $table->dropColumn('is_additional');
            $table->dropColumn('name');
            $table->dropColumn('description');
            $table->renameColumn('filename', 'name');
            $table->unique(['name', 'hash', 'patchline']);
        });
    }
};
