<?php

use App\Enums\Launcher\FileAction;
use App\Models\GameFile;
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
        if (Schema::hasIndex('game_files', ['filename', 'hash', 'patchline'])) {
            Schema::table('game_files', function (Blueprint $table) {
                $table->dropUnique(['filename', 'hash', 'patchline']);
            });
        }

        if (Schema::hasIndex('game_files', ['name'])) {
            Schema::table('game_files', function (Blueprint $table) {
                $table->dropUnique(['name']);
            });
        }

        Schema::table('game_files', function (Blueprint $table) {
            $table->foreignId('child_id')->nullable()->after('id')->constrained('game_files')->cascadeOnDelete();
            $table->unique(['filename', 'hash', 'patchline', 'child_id']);
            $table->unique(['name', 'child_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove all child records
        GameFile::whereNotNull('child_id')->each(function ($gameFile) {
            $gameFile->delete();
        });

        Schema::table('game_files', function (Blueprint $table) {
            $table->dropUnique(['filename', 'hash', 'patchline', 'child_id']);
            $table->dropUnique(['name', 'child_id']);
            $table->dropForeign('game_files_child_id_foreign');
            $table->dropColumn('child_id');
            $table->unique(['filename', 'hash', 'patchline']);
            $table->unique('name');
        });
    }
};
