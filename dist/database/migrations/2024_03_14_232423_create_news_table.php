<?php

use App\Enums\Game\Message\GameNewsRedirectMode;
use App\Enums\Game\Message\MessageType;
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
        Schema::create('news', function (Blueprint $table) {
            $table->uuid()->primary();
            $table->enum('message_type', array_column(MessageType::cases(), 'value'));
            $table->boolean('one_time_news');
            $table->boolean('should_quit_game');
            $table->boolean('one_match');
            $table->enum('redirect_mode', array_column(GameNewsRedirectMode::cases(), 'value'));
            $table->string('redirect_item')->nullable();
            $table->string('redirect_url')->nullable();
            $table->string('background_image')->nullable();
            $table->string('in_game_news_background_image')->nullable();
            $table->string('in_game_news_thumbnail')->nullable();
            $table->dateTime('from_date')->nullable();
            $table->dateTime('to_date')->nullable();
            $table->string('title');
            $table->text('body');
            $table->integer('display_x_times')->nullable();
            $table->integer('max_player_level')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};
