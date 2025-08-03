<?php

namespace App\Console\Commands;

use App\Enums\Game\ItemGroupType;
use App\Enums\Game\ItemOrigin;
use App\Enums\Game\ItemQuality;
use App\Models\Game\CatalogItem;
use App\Models\Game\Challenge;
use App\Models\Game\PrestigeReward;
use App\Models\Game\PrestigeRewardItem;
use DB;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Type\Hexadecimal;
use Ramsey\Uuid\Uuid;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;

class ImportCatalog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-catalog';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports the template catalog into the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $catalog = json_decode(Storage::disk('local')->get('/catalog/catalog.json'), true)['result'];

        $this->info('Strting import of Catalog items...');

        foreach ($catalog as $item) {
            $uuid = Uuid::fromString($item['id']);

            $this->info('Importing Item '.$item['displayName']);

            // parse default costs
            $defaultCost['CurrencyC'] = $defaultCost['CurrencyB'] = $defaultCost['CurrencyA'] = null;
            foreach ($item['defaultCost'] as $cost) {
                $defaultCost[$cost['currencyId']] = $cost['price'];
            }

            $newItem = CatalogItem::findOrNew($uuid->toString());

            $newItem->id = $uuid->toString();
            $newItem->display_name = $item['displayName'];
            $newItem->initial_quantity = $item['initialQuantity'];
            $newItem->consumable = $item['consumable'];
            $newItem->default_cost_currency_a = $defaultCost['CurrencyA'];
            $newItem->default_cost_currency_b = $defaultCost['CurrencyB'];
            $newItem->default_cost_currency_c = $defaultCost['CurrencyC'];
            $newItem->purchasable = $item['purchasable'];
            $newItem->meta_min_player_level = $item['metaData']['minPlayerLevel'];
            $newItem->meta_min_character_level = $item['metaData']['minCharacterLevel'];
            $newItem->meta_is_unbreakable_fullset = $item['metaData']['isUnbreakableFullset'];
            $newItem->meta_origin = ItemOrigin::tryFrom($item['metaData']['origin']);
            $newItem->meta_quality = ItemQuality::tryFrom($item['metaData']['quality']);
            $newItem->meta_group_type = ItemGroupType::tryFrom($item['metaData']['groupType']);
            $newItem->save();
        }

        $this->info('Finsiehd importing items.');

        $this->info('Importing Challenges...');
        $this->importChallenges();

        $this->info('importing Relations...');

        foreach ($catalog as $item) {
            $uuid = Uuid::fromHexadecimal(new Hexadecimal($item['id']));
            $itemModel = CatalogItem::findOrFail($uuid->toString());

            $this->info('Processing Relations for item '.$itemModel->display_name);

            $itemModel->addGameplayTags($item['metaData']['gameplayTags']);
            static::checkForAutoUnlockItems($item, $itemModel);
            static::checkForBundleItems($item, $itemModel);
            static::checkForItemAssignments($item, $itemModel);
            static::checkForPrestigeRewards($item, $itemModel);
            $this->checkForRequiredChallenges($item, $itemModel);

            $itemModel->save();
        }
    }

    private function importChallenges(): void {
        $itemFiles = File::allFiles(resource_path('js/signatureChallenges'));

        foreach ($itemFiles as $item) {
            $data = json_decode(file_get_contents($item->getRealPath()), true);

            $iterator = new RecursiveArrayIterator($data);
            $recursive = new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::SELF_FIRST);

            foreach ($recursive as $key => $value) {
                if($key !== 'RequiredChallengesToComplete')
                    continue;

                $challengeId = Uuid::fromString($value[0]['ChallengeId']);

                $newChallenge = Challenge::findOrNew($challengeId->toString());

                $newChallenge->id = $challengeId->toString();
                $newChallenge->completion_value = $value[0]['ChallengeCompletionValue'];
                $newChallenge->asset_path = $value[0]['ChallengeAsset']['AssetPathName'];

                $this->info('Importing Signature Challenge: '.$value[0]['ChallengeId']);
                $newChallenge->save();
            }
        }

        $achievementChallenges = [
            [
                'id' => 'e51981b9-46be-e3d4-5c5c-41b2fcff310b',
                'value' => 500,
                'path' => '/Game/Challenges/Challenge_Deliver_Runner.Challenge_Deliver_Runner',
            ],
            [
                'id' => 'efab89e6-465d-1163-d62a-07b11048f2b6',
                'value' => 50,
                'path' => '/Game/Challenges/Challenge_JustPlay.Challenge_JustPlay',
            ],
            [
                'id' => '2caebb35-4d50-6d7c-43b9-41bc1da775a0',
                'value' => 10,
                'path' => '/Game/Challenges/Progression/General/Challenge_Exit_Runner.Challenge_Exit_Runner',
            ],
            [
                'id' => 'aad05b9d-4647-1dc8-11bb-e0ba91916ab7',
                'value' => 50,
                'path' => '/Game/Challenges/Challenge_Down_Hunter.Challenge_Down_Hunter',
            ],
            [
                'id' => 'ba2d4a54-45cb-7027-6a8f-5d9e1afce080',
                'value' => 100,
                'path' => '/Game/Challenges/Challenge_DroneCharger_Hunter.Challenge_DroneCharger_Hunter',
            ],
        ];

        foreach ($achievementChallenges as $challenge) {
            $newChallenge = Challenge::findOrNew($challenge['id']);

            $newChallenge->id = $challenge['id'];
            $newChallenge->completion_value = $challenge['value'];
            $newChallenge->asset_path = $challenge['path'];

            $this->info('Importing Achievement Challenge: '.$challenge['id']);
            $newChallenge->save();
        }

        $this->info('Finished importing challenges.');
    }

    private static function checkForAutoUnlockItems(array &$rawItem, CatalogItem &$itemModel)
    {
        $itemIds = [];

        foreach ($rawItem['metaData']['autoUnlockItemIds'] as $id) {
            $itemIds[] = Uuid::fromHexadecimal(new Hexadecimal($id))->toString();
        }
        $itemModel->autoUnlockItems()->sync($itemIds);
    }

    private static function checkForItemAssignments(array &$rawItem, CatalogItem &$itemModel)
    {
        $assignedItemIds = [];

        foreach ($rawItem['metaData']['itemAssignments'] as $id) {
            $assignedItemIds[] = Uuid::fromHexadecimal(new Hexadecimal($id))->toString();
        }
        $itemModel->itemAssignments()->sync($assignedItemIds);
    }

    private static function checkForBundleItems(array &$rawItem, CatalogItem &$item)
    {
        if(!array_key_exists('bundleItems', $rawItem['metaData']))
            return;

        $bundleItems = [];

        foreach ($rawItem['metaData']['bundleItems'] as $bundleItem) {
            $uuid = Uuid::fromHexadecimal(new Hexadecimal($bundleItem));
            $bundleItems[] = $uuid->toString();
            $item->meta_has_bundle_items = true;
            $item->has_reward_bundle_items = true;
        }
        $item->bundleItems()->sync($bundleItems);
    }

    private static function checkForPrestigeRewards(array &$rawItem, CatalogItem &$item)
    {
        foreach ($rawItem['metaData']['prestigeLevelRewards'] as $prestigeReward) {
            $rewards = [];

            $cost['CurrencyC'] = $cost['CurrencyB'] = $cost['CurrencyA'] = null;
            foreach ($prestigeReward['costs'] as $rewardCost) {
                $cost[$rewardCost['currencyId']] = $rewardCost['price'];
            }

            $newPrestigeReward = PrestigeReward::create([
                'catalog_item_id' => $item->id,
                'cost_currency_a' => $cost['CurrencyA'],
                'cost_currency_b' => $cost['CurrencyB'],
                'cost_currency_c' => $cost['CurrencyC'],
            ]);

            foreach ($prestigeReward['rewards'] as $reward) {
                $uuid = Uuid::fromHexadecimal(new Hexadecimal($reward['id']));

                $rewards[] = PrestigeRewardItem::create([
                    'catalog_item_id' => $uuid->toString(),
                    'amount' => $reward['amount'],
                ]);
            }

            $newPrestigeReward->rewardItems()->saveMany($rewards);

            $item->prestigeRewards()->save($newPrestigeReward);
        }
    }

    private function checkForRequiredChallenges(array &$rawItem, CatalogItem &$item)
    {
        $requiredChallengeIds = [];

        foreach ($rawItem['metaData']['requiredChallengesToComplete'] as $challengeId) {
            $challengeId = Uuid::fromString($challengeId);
            $foundChallenge = Challenge::find($challengeId->toString());

            if($foundChallenge === null) {
                $this->warn('Could not find Challenge with ID "'.$challengeId->toString().'" for item '.$item->display_name);
                continue;
            }

            $requiredChallengeIds[] = $challengeId->toString();
        }

        $item->requiredChallenges()->sync($requiredChallengeIds);
    }
}
