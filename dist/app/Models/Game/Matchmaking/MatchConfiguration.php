<?php

namespace App\Models\Game\Matchmaking;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperMatchConfiguration
 */
class MatchConfiguration extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'enabled',
        'hunters',
        'runners',
        'weight',
        'asset_path',
    ];

    protected $attributes = [
        'enabled' => true,
        'weight' => 100,
        'hunters' => 1,
        'runners' => 5,
    ];

    /**
     * return the Available match configs for the given player counts
     *
     * @param int $runnerCount
     * @param int $hunterCount
     * @return Collection<MatchConfiguration>
     */
    public static function getAvailableMatchConfigs(int $runnerCount, int $hunterCount): Collection
    {
        return MatchConfiguration::where('runners', '<=', $runnerCount)
            ->where('hunters', '<=', $hunterCount)
            ->where('enabled', '=', true)
            ->get();
    }

    public static function selectRandomConfigByWeight(Collection &$collection): MatchConfiguration|null
    {
        $weightSum = $collection->sum('weight');
        $random = random_int(1, $weightSum);
        $selectWalk = 0;

        foreach ($collection as $config) {
            /** @var MatchConfiguration $config */
            if(($selectWalk + $config->weight) >= $random)
                return $config;
            else
                $selectWalk += $config->weight;
        }

        return null;
    }
}