<?php

namespace App\Classes\Matchmaking;

class MatchmakingPlayerCount
{
    public function __construct(
        public int $hunters = 0,
        public int $runners = 0,
    )
    {}

    public function getTotal(): int
    {
        return $this->runners + $this->runners;
    }

    /**
     * Function to determine which numbers of the array result in the sum given
     * Used for matchmaking to determine which player groups add up to the sum.
     * Because when there is a group of 3 and 4 players, we technically have enough players for a match,
     * but cannot create it because the groups cannot be split.
     *
     * @param array $array
     * @param int $sum
     * @param bool $returnFirstSet
     * @return array
     */
    public static function findSubsetsOfSum(array $array, int $sum, bool $returnFirstSet = false): array
    {
        $x = pow(2, count($array));
        $result = [];

        for ($i = 1; $i < $x; ++$i) {
            $subset = static::sumSubset($array, $i, $sum);
            if(count($subset) > 0) {
                if($returnFirstSet)
                    return $subset;
                $result[] = $subset;
            }
        }
        return $result;
    }

    private static function sumSubset(array $set, int $n, int $target): array
    {
        $x = new \SplFixedArray(count($set));
        $j = count($set) - 1;

        while ($n > 0) {
            $x[$j] = $n % 2;
            $n = floor($n / 2);
            --$j;
        }

        $sum = 0;

        for ($i = 0; $i < count($set); ++$i) {
            if($x[$i] === 1)
                $sum += $set[$i];
        }

        $result = [];
        if($sum == $target) {
            foreach ($x as $index => $value) {
                if ($value === 1)
                    $result[] = $set[$index];
            }
        }

        return $result;
    }
}