<?php

namespace App\Console\Commands;

use App\Classes\Character\CharacterItemConfig;
use App\Enums\Game\Characters;
use App\Helper\Uuid\UuidHelper;
use App\Models\User\PlayerData;
use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class GiveAllUsersDefaultItems extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:give-all-users-default-items';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $playerDatas = PlayerData::all();

        foreach ($playerDatas as $playerData) {
            foreach (Characters::cases() as $case) {
                $this->info('Adding items for user ' . $playerData->user_id . ' for  Character ' . $case->value);
                $configClass = $case->getCharacter()->getItemConfigClass();
                $this->addItemsToUser($playerData, $configClass);
            }
        }
    }

    /**
     * @param PlayerData $user
     * @param class-string<CharacterItemConfig> $config
     * @return void
     */
    public function addItemsToUser(PlayerData &$playerData, string $config): void {
        $itemIds = [
            ...$config::getDefaultEquippedBonuses(),
            ...$config::getDefaultEquippedWeapons(),
            ...$config::getDefaultEquipment(),
            ...$config::getDefaultPowers(),
            ...$config::getDefaultWeapons(),
            ...$config::getDefaultEquippedPerks(),
        ];

        $itemIds = UuidHelper::convertFromHexToUuidCollecton($itemIds, true);
        try {
            $playerData->inventory()->syncWithoutDetaching($itemIds);
        } catch (QueryException $e) {
            Log::channel('single')->error($e->getMessage());
            $this->error('Exception: ' . $e->getMessage());
            $this->error("Failed to add items to inventory: " . json_encode($itemIds, JSON_PRETTY_PRINT));
        }
    }
}
