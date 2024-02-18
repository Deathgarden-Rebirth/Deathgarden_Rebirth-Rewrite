<?php

namespace App\Console\Commands;

use App\Enums\Game\ItemGroupType;
use App\Enums\Game\ItemOrigin;
use App\Enums\Game\ItemQuality;
use App\Models\Game\CatalogItem;
use App\Models\Game\PrestigeReward;
use App\Models\Game\PrestigeRewardItem;
use DB;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Type\Hexadecimal;
use Ramsey\Uuid\Uuid;

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
        DB::table('catalog_items')->delete();

        $catalog = json_decode(file_get_contents(resource_path('js/catalog/catalog.json')), true)['result'];

        $this->info('Strting import of Catalog items...');

        foreach ($catalog as $item) {
            $uuid = Uuid::fromHexadecimal(new Hexadecimal($item['id']));

            $this->info('Importing Item '.$item['displayName']);

            // parse default costs
            $defaultCost['CurrencyC'] = $defaultCost['CurrencyB'] = $defaultCost['CurrencyA'] = null;
            foreach ($item['defaultCost'] as $cost) {
                $defaultCost[$cost['currencyId']] = $cost['price'];
            }

            $newItem = new CatalogItem([
                'id' => $uuid->toString(),
                'display_name' => $item['displayName'],
                'initial_quantity' => $item['initialQuantity'],
                'consumable' => $item['consumable'],
                'default_cost_currency_a' => $defaultCost['CurrencyA'],
                'default_cost_currency_b' => $defaultCost['CurrencyB'],
                'default_cost_currency_c' => $defaultCost['CurrencyC'],
                'purchasable' => $item['purchasable'],
                'meta_min_player_level' => $item['metaData']['minPlayerLevel'],
                'meta_min_character_level' => $item['metaData']['minCharacterLevel'],
                'meta_is_unbreakable_fullset' => $item['metaData']['isUnbreakableFullset'],
                'meta_origin' => ItemOrigin::tryFrom($item['metaData']['origin']),
                'meta_quality' => ItemQuality::tryFrom($item['metaData']['quality']),
                'meta_group_type' => ItemGroupType::tryFrom($item['metaData']['groupType']),
            ]);

            $newItem->save();
        }

        $this->info('Finsiehd importing items.');
        $this->info('importing Relations...');

        foreach ($catalog as $item) {
            $uuid = Uuid::fromHexadecimal(new Hexadecimal($item['id']));
            $itemModel = CatalogItem::findOrFail($uuid->toString());

            $this->info('Processing Relations for item '.$itemModel->display_name);


            $itemModel->addGameplayTags($item['metaData']['gameplayTags']);
            $itemModel->addChallenges($item['metaData']['requiredChallengesToComplete']);
            static::checkForAutoUnlockItems($item, $itemModel);
            static::checkForBundleItems($item, $itemModel);
            static::checkForItemAssignments($item, $itemModel);
            static::checkForPrestigeRewards($item, $itemModel);

            $itemModel->save();
        }
    }

    private static function checkForAutoUnlockItems(array &$rawItem, CatalogItem &$itemModel)
    {
        foreach ($rawItem['metaData']['autoUnlockItemIds'] as $id) {
            $uuid = Uuid::fromHexadecimal(new Hexadecimal($id));
            $itemModel->autoUnlockItems()->attach($uuid->toString());
        }
    }

    private static function checkForItemAssignments(array &$rawItem, CatalogItem &$itemModel)
    {
        foreach ($rawItem['metaData']['itemAssignments'] as $id) {
            $uuid = Uuid::fromHexadecimal(new Hexadecimal($id));
            $itemModel->itemAssignments()->attach($uuid->toString());
        }
    }

    private static function checkForBundleItems(array &$rawItem, CatalogItem &$item)
    {
        if(!array_key_exists('bundleItems', $rawItem['metaData']))
            return;

        foreach ($rawItem['metaData']['bundleItems'] as $bundleItem) {
            $uuid = Uuid::fromHexadecimal(new Hexadecimal($bundleItem));
            $item->bundleItems()->attach($uuid->toString());
            $item->meta_has_bundle_items = true;
            $item->has_reward_bundle_items = true;
        }
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

                $newReward = PrestigeRewardItem::create([
                    'catalog_item_id' => $uuid->toString(),
                    'amount' => $reward['amount'],
                ]);
                $newReward->save();
                $rewards[] = $newReward;
            }

            $newPrestigeReward->save();
            $newPrestigeReward->rewardItems()->saveMany($rewards);

            $item->prestigeRewards()->save($newPrestigeReward);
        }
    }
}
