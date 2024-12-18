<?php

namespace App\Console\Commands;

use App\Classes\Factory\TimedChallengeFactory;
use App\Enums\Game\ChallengeType;
use App\Enums\Game\Faction;
use App\Enums\Game\RewardType;
use App\Exceptions\TimedChallengeFactoryException;
use App\Http\Responses\Api\General\Reward;
use App\Models\Game\TimedChallenge;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class GenerateTimedChallenges extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-timed-challenges';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the Daily and Weekly challenges for the upcoming day/week';

    const INTERVAL_HOUR = 21;
    const INTERVAL_MINUTE = 0;

    const WEEKLY_INTERVAL_DAY = 'tuesday';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dailyToday = Carbon::today();
        $dailyToday->setTime(static::INTERVAL_HOUR, static::INTERVAL_MINUTE);
        $dailyTomorrow = $dailyToday->copy()->addDay();
        $currentWeekly = Carbon::parse('last '.static::WEEKLY_INTERVAL_DAY);
        $currentWeekly->setTime(static::INTERVAL_HOUR, static::INTERVAL_MINUTE);
        $nextWeekly = $currentWeekly->copy()->addWeek();

        $dailys = [$dailyToday, $dailyTomorrow];
        $weeklys = [$currentWeekly, $nextWeekly];

        $log = Log::channel('challengeCreation');

        foreach ([Faction::Hunter, Faction::Runner] as $faction) {
            foreach ($dailys as $time) {
                $dailyExists = TimedChallenge::whereDate('start_time', $time)
                    ->where('type', ChallengeType::Daily)
                    ->where('faction', $faction)
                    ->exists();

                if(!$dailyExists) {
                    try {
                        $newDaily = TimedChallengeFactory::makeChallenge($time, $faction, ChallengeType::Daily);
                        $newDaily->save();
                    } catch (TimedChallengeFactoryException $e) {
                        $log->error($e->getMessage());
                    }
                }
            }

            foreach ($weeklys as $time) {
                $weeklyExists = TimedChallenge::whereDate('start_time', $time)
                    ->where('type', ChallengeType::Weekly)
                    ->where('faction', $faction)
                    ->exists();

                if(!$weeklyExists) {
                    try {
                        $newDaily = TimedChallengeFactory::makeChallenge($time, $faction, ChallengeType::Weekly);
                        $newDaily->save();
                    } catch (TimedChallengeFactoryException $e) {
                        $log->error($e->getMessage());
                    }
                }
            }
        }
    }
}
