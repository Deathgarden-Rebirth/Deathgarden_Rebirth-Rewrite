<?php

namespace App\Console\Commands;

use App\Models\Admin\CurrencyMultipliers;
use App\Models\Game\Matchmaking\MatchConfiguration;
use App\Models\Game\Messages\News;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class SetCurrencyModifiers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:set-currency-modifiers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sets automated currency modifiers and news announcement for a timeframe';

    const DAILY_START_TIME = '00:00';

    const DAILY_END_TIME = '09:00';

    const MODIFIER_AMOUNT = 1.5;

    const GAME_NEWS_ID = '9f6a2801-c8c2-40ae-8d55-5dab3b601863';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $startTime = Carbon::today()->setTimeFromTimeString(static::DAILY_START_TIME);
        $endTime = Carbon::today()->setTimeFromTimeString(static::DAILY_END_TIME);

        if(Carbon::now()->between($startTime, $endTime)) {
            $this->setModifiers(static::MODIFIER_AMOUNT);
            $this->setGamenewsVisibility(true);
        }
        else {
            $this->setModifiers(1);
            $this->setGamenewsVisibility(false);
        }
    }

    private function setGamenewsVisibility(bool $enabled): void
    {
        $news = News::find(static::GAME_NEWS_ID);

        if($news === null)
            return;

        $news->enabled = $enabled;
        $news->save();
    }

    private function setModifiers(float $modifier): void {
        $config = CurrencyMultipliers::get();

        $config->currencyA = $modifier;
        $config->currencyB = $modifier;
        $config->currencyC = $modifier;

        $config->save();
    }
}
