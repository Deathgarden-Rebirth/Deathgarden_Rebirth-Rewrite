<?php

namespace App\Console\Commands;

use App\Classes\Character\HunterItemConfig\InquisitorItemConfig;
use App\Classes\Character\HunterItemConfig\PoacherItemConfig;
use App\Classes\Character\HunterItemConfig\StalkerItemConfig;
use App\Classes\Character\HunterItemConfig\VeteranItemConfig;
use App\Classes\Character\RunnerItemConfig\DashItemConfig;
use App\Classes\Character\RunnerItemConfig\FogItemConfig;
use App\Classes\Character\RunnerItemConfig\GhostItemConfig;
use App\Classes\Character\RunnerItemConfig\InkedItemConfig;
use App\Classes\Character\RunnerItemConfig\SawbonesItemConfig;
use App\Classes\Character\RunnerItemConfig\SwitchItemConfig;
use App\Helper\Uuid\UuidHelper;
use App\Models\User\User;
use Illuminate\Console\Command;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Str;

class AddAllGameplayItems extends Command
{
    const ITEM_CONFIG = [
        InquisitorItemConfig::class,
        PoacherItemConfig::class,
        StalkerItemConfig::class,
        VeteranItemConfig::class,
        DashItemConfig::class,
        FogItemConfig::class,
        GhostItemConfig::class,
        InkedItemConfig::class,
        SawbonesItemConfig::class,
        SwitchItemConfig::class,
    ];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'game:unlock-items {user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adds all character and gemaplay relevant items to your inventory';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user');

        if(Str::contains($userId, '-'))
            $user = User::find($userId);
        else
            $user = User::findBySteamID($userId);

        if ($user === null) {
            $this->error('User id "' . $userId . '" not found');
            return;
        }

        $playerData = $user->playerData();

        foreach (static::ITEM_CONFIG as $configClass) {
            $itemIds = $configClass::getAllItems();
            $itemIds = UuidHelper::convertFromHexToUuidCollecton($itemIds);
            foreach ($itemIds as $itemId) {
                try {
                    $this->info('Attaching Item '.$itemId.' from config '.$configClass);
                    $playerData->inventory()->attach($itemId->toString());
                } catch(UniqueConstraintViolationException $e) {
                    $this->warn('Skipping Item '.$itemId.' from config '.$configClass. 'because it alread is inside the players inventory.');
                }
            }
        }

        $this->info('Done filling inventory for '.$user->last_known_username);
    }
}
