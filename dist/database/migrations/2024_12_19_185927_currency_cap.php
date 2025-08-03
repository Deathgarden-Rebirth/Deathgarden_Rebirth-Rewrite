<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    const USER_MIGRATION_CURRENCY = 10000;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        \App\Models\User\PlayerData::query()->update([
            'currency_a' => self::USER_MIGRATION_CURRENCY,
            'currency_b' => self::USER_MIGRATION_CURRENCY,
            'currency_c' => self::USER_MIGRATION_CURRENCY,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
