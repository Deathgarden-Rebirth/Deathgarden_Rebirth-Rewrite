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
        Schema::create('player_reports', function (Blueprint $table) {
            $table->id();

            $table->foreignUuid('reported_user_id')->constrained('users');
            $table->foreignUuid('reporting_user_id')->constrained('users');
            $table->string('reason');
            $table->text('details');
            $table->uuid('match_id');
            $table->json('player_infos');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_reports');
    }
};
