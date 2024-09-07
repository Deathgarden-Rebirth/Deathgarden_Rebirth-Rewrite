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
        Schema::create('bad_chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('host_user_id')->constrained('users');
            $table->foreignUuid('user_id')->constrained();
            $table->text('message');
            $table->boolean('handled')->default(false);
            $table->foreignUuid('handled_by_id')->nullable()->constrained('users');
            $table->text('consequences')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bad_chat_messages');
    }
};
