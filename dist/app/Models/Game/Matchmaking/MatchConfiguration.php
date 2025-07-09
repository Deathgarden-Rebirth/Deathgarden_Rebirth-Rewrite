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

    public static function selectMatchConfig(Collection &$collection, int $runnerCount, int $hunterCount): MatchConfiguration|null
    {
        $filteredConfigs = new Collection();

        if ($hunterCount === 1 && $runnerCount >= 6){
            $filteredConfigs = $collection->filter(function (MatchConfiguration $config) use ($runnerCount, $hunterCount) {
                return $config->runners >= 6 && $config->hunters === $hunterCount;
            });
        }
        else if ($hunterCount === 1 && $runnerCount >= 4){
            $filteredConfigs = $collection->filter(function (MatchConfiguration $config) use ($runnerCount, $hunterCount) {
                return $config->runners === $runnerCount && $config->hunters === $hunterCount;
            });
        }

        if ($filteredConfigs->isNotEmpty())
            $collection = $filteredConfigs;

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
