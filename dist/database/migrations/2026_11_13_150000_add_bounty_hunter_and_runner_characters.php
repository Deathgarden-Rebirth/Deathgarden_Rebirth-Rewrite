<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Adds The Bounty Hunter (Hunter.BountyHunter, HunterGroupE) and Runner (Runner.Runner, RunnerGroupG).
 *
 * The enum columns were created from the PHP enum cases, so existing databases need them widened.
 * The new values are ADDED to whatever the column currently allows: a database can hold values from
 * other branches (e.g. CircuitBreaker) that this branch's enums don't know, and those must survive.
 * Safe to re-run after a partial failure (MySQL ALTERs are not transactional).
 */
return new class extends Migration
{
    private const COLUMNS = [
        ['catalog_items', 'meta_group_type', ['HunterGroupE', 'RunnerGroupG']],
        ['character_data', 'character', ['BountyHunter', 'Runner']],
        ['player_data', 'last_hunter', ['BountyHunter']],
        ['player_data', 'last_runner', ['Runner']],
        ['archived_player_progressions', 'played_character', ['BountyHunter', 'Runner']],
    ];

    private function currentValues(string $table, string $column): array
    {
        $type = DB::selectOne(
            'SELECT COLUMN_TYPE AS t FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?',
            [$table, $column]
        )->t;
        preg_match_all("/'((?:[^'\\\\]|''|\\\\.)*)'/", $type, $m);

        return array_map(fn ($v) => str_replace("''", "'", $v), $m[1]);
    }

    private function setValues(string $table, string $column, array $values): void
    {
        Schema::table($table, function (Blueprint $t) use ($column, $values) {
            $t->enum($column, array_values(array_unique($values)))->change();
        });
    }

    public function up(): void
    {
        foreach (self::COLUMNS as [$table, $column, $new]) {
            $this->setValues($table, $column, [...$this->currentValues($table, $column), ...$new]);
        }
    }

    public function down(): void
    {
        // rows using the new values have to go before the columns can be narrowed again
        DB::table('catalog_items')->whereIn('meta_group_type', ['HunterGroupE', 'RunnerGroupG'])
            ->whereIn('id', ['fb72b18a-6cf2-23a3-3094-053789f4d0ae', '887d5078-e5f6-3a03-3ca4-61e2576436bc'])->delete();
        DB::table('character_data')->whereIn('character', ['BountyHunter', 'Runner'])->delete();
        DB::table('archived_player_progressions')->whereIn('played_character', ['BountyHunter', 'Runner'])->delete();
        DB::table('player_data')->where('last_hunter', 'BountyHunter')->update(['last_hunter' => 'Mass']);
        DB::table('player_data')->where('last_runner', 'Runner')->update(['last_runner' => 'Smoke']);

        foreach (self::COLUMNS as [$table, $column, $new]) {
            // HunterGroupE may be in use by another character (CircuitBreaker) - only drop it if nothing uses it
            $new = array_filter($new, fn ($v) => !DB::table($table)->where($column, $v)->exists());
            $this->setValues($table, $column, array_diff($this->currentValues($table, $column), $new));
        }
    }
};
