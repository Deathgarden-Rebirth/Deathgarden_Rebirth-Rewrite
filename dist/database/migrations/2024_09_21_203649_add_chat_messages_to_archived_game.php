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
        Schema::table('archived_games', function (Blueprint $table) {
            $table->json('chat_messages')->after('dominant_faction');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('archived_games', function (Blueprint $table) {
            $table->dropColumn('chat_messages');
        });
    }
};
