<?php

use App\Enums\Game\Characters;
use App\Enums\Game\Hunter;
use App\Enums\Game\ItemGroupType;
use App\Enums\Game\Runner;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Adds The Bounty Hunter (Hunter.BountyHunter, HunterGroupE) and Runner (Runner.Runner, RunnerGroupG).
 * The enum columns were created from the PHP enum cases, so existing databases need them widened.
 */
return new class extends Migration
{
    private const NEW_GROUPS = ['HunterGroupE', 'RunnerGroupG'];

    private const NEW_HUNTERS = ['BountyHunter'];

    private const NEW_RUNNERS = ['Runner'];

    private function values(array $cases, array $exclude = []): array
    {
        return array_values(array_diff(array_column($cases, 'value'), $exclude));
    }

    private function apply(bool $withNew): void
    {
        $ex = fn (array $new) => $withNew ? [] : $new;
        $characterExclude = $ex([...self::NEW_HUNTERS, ...self::NEW_RUNNERS]);

        Schema::table('catalog_items', function (Blueprint $table) use ($ex) {
            $table->enum('meta_group_type', $this->values(ItemGroupType::cases(), $ex(self::NEW_GROUPS)))->change();
        });

        Schema::table('character_data', function (Blueprint $table) use ($characterExclude) {
            $table->enum('character', $this->values([...Hunter::cases(), ...Runner::cases()], $characterExclude))->change();
        });

        Schema::table('player_data', function (Blueprint $table) use ($ex) {
            $table->enum('last_hunter', $this->values(Hunter::cases(), $ex(self::NEW_HUNTERS)))->change();
            $table->enum('last_runner', $this->values(Runner::cases(), $ex(self::NEW_RUNNERS)))->change();
        });

        Schema::table('archived_player_progressions', function (Blueprint $table) use ($characterExclude) {
            $table->enum('played_character', $this->values(Characters::cases(), $characterExclude))->change();
        });
    }

    public function up(): void
    {
        $this->apply(true);
    }

    public function down(): void
    {
        // rows using the new values have to go before the columns can be narrowed again
        DB::table('catalog_items')->whereIn('meta_group_type', self::NEW_GROUPS)->delete();
        DB::table('character_data')->whereIn('character', [...self::NEW_HUNTERS, ...self::NEW_RUNNERS])->delete();
        DB::table('archived_player_progressions')->whereIn('played_character', [...self::NEW_HUNTERS, ...self::NEW_RUNNERS])->delete();
        DB::table('player_data')->whereIn('last_hunter', self::NEW_HUNTERS)->update(['last_hunter' => Hunter::Mass->value]);
        DB::table('player_data')->whereIn('last_runner', self::NEW_RUNNERS)->update(['last_runner' => Runner::Smoke->value]);

        $this->apply(false);
    }
};
