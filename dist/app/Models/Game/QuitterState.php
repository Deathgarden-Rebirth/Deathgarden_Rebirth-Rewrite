<?php

namespace App\Models\Game;

use App\Enums\Game\RewardType;
use App\Http\Responses\Api\General\Reward;
use App\Models\User\PlayerData;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @mixin IdeHelperQuitterState
 */
class QuitterState extends Model
{
    use HasFactory;

    protected $casts = [
        'stay_match_streak' => 'int',
        'stay_match_streak_previous' => 'int',
        'quits' => 'int',
        'quit_match_streak' => 'int',
        'quit_match_streak_previous' => 'int',
        'strikes_left' => 'int',
        'has_quit_once' => 'boolean',
        'strike_refresh_time' => 'datetime',
    ];

    protected $attributes = [
        'stay_match_streak' => 0,
        'stay_match_streak_previous' => 0,
        'quits' => 0,
        'quit_match_streak' => 0,
        'quit_match_streak_previous' => 0,
        'has_quit_once' => false,
    ];

    protected static function booted()
    {
        static::creating(function (QuitterState $quitterState) {
            $quitterState->strikes_left = config('quitter-state.max-available-strikes');
        });
    }

    public function checkStrikeRefresh(): void
    {
        if ($this->strike_refresh_time === null || $this->strikes_left === config('quitter-state.max-available-strikes'))
            return;

        while ($this->strike_refresh_time !== null) {
            $currentRefreshTime = $this->strike_refresh_time;

            if($this->strike_refresh_time->isBefore(Carbon::now())) {
                ++$this->strikes_left;
                $this->has_quit_once = false;

                if($this->strikes_left < config('quitter-state.max-available-strikes')) {
                    $this->strike_refresh_time = $currentRefreshTime->add(config('quitter-state.strike-refresh-time'));
                }
                else {
                    $this->strike_refresh_time = null;
                    $this->save();
                    break;
                }
            }
            else {
                $this->save();
                break;
            }
        }
    }

    public function addStayedMatch(PlayerData &$playerData): void {
        ++$this->stay_match_streak;
        $this->has_quit_once = false;
        $rewards = static::getReward($this->stay_match_streak);

        foreach ($rewards as $reward) {
            switch ($reward->id) {
                case 'CurrencyA':
                    $playerData->currency_a += $reward->amount;
                    break;
                case 'CurrencyB':
                    $playerData->currency_b += $reward->amount;
                    break;
                case 'CurrencyC':
                    $playerData->currency_c += $reward->amount;
                    break;
            }
        }
    }

    public function addQuitterPenalty(): void {
        if ($this->strikes_left === 0) {
            $this->stay_match_streak_previous = $this->stay_match_streak;
            $this->stay_match_streak = 0;
        }
        else {
            --$this->strikes_left;

            if($this->strike_refresh_time === null) {
                $this->strike_refresh_time = Carbon::now()->add(config('quitter-state.strike-refresh-time'));
            }
        }

        $this->has_quit_once = true;
        $this->save();
    }

    /**
     * @param int $stayMatchStreak
     * @return Reward[]
     */
    public static function getReward(int $stayMatchStreak): array {
        if($stayMatchStreak === 5) {
            return [
                new Reward(
                    RewardType::Currency,
                    50,
                    'CurrencyA',
                ),
            ];
        }
        else if($stayMatchStreak === 10) {
            return [
                new Reward(
                    RewardType::Currency,
                    100,
                    'CurrencyA',
                ),
                new Reward(
                    RewardType::Currency,
                    100,
                    'CurrencyB',
                ),
                new Reward(
                    RewardType::Currency,
                    50,
                    'CurrencyC',
                ),
            ];
        }
        else if($stayMatchStreak === 15) {
            return [
                new Reward(
                    RewardType::Currency,
                    150,
                    'CurrencyA',
                ),
                new Reward(
                    RewardType::Currency,
                    150,
                    'CurrencyB',
                ),
                new Reward(
                    RewardType::Currency,
                    100,
                    'CurrencyC',
                ),
            ];
        }
        else if($stayMatchStreak === 20) {
            return [
                new Reward(
                    RewardType::Currency,
                    200,
                    'CurrencyA',
                ),
                new Reward(
                    RewardType::Currency,
                    200,
                    'CurrencyB',
                ),
                new Reward(
                    RewardType::Currency,
                    150,
                    'CurrencyC',
                ),
            ];
        }
        else if($stayMatchStreak >= 25 && $stayMatchStreak % 5 === 0) {
            return [
                new Reward(
                    RewardType::Currency,
                    250,
                    'CurrencyA',
                ),
                new Reward(
                    RewardType::Currency,
                    250,
                    'CurrencyB',
                ),
                new Reward(
                    RewardType::Currency,
                    200,
                    'CurrencyC',
                ),
            ];
        }

        return [];
    }
}
