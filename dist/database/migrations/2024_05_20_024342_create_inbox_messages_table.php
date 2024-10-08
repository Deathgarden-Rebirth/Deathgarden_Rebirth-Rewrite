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
        Schema::create('inbox_messages', function (Blueprint $table) {
            $table->id();
            $table->timestamp('received')->index()->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->foreignUuid('user_id')->constrained();
            $table->text('title');
            $table->text('body');
            $table->string('flag', 10);
            $table->string('tag', 30);
            $table->datetime('expire_at')->nullable();
            $table->string('origin')->nullable()->default(null);
            $table->json('claimable')->nullable();
            $table->boolean('has_claimed')->default(false);

            $table->unique(['user_id', 'received']);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inbox_messages');
    }
};
