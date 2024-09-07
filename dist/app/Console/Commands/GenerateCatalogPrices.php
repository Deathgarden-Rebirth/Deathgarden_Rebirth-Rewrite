<?php

namespace App\Console\Commands;

use App\Classes\Config\CatalogPriceCategory;
use App\Classes\Config\CatalogPriceConfig;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use function PHPUnit\Framework\stringEndsWith;

class GenerateCatalogPrices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-catalog-prices {catalogFile} {destinationFile}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change the price sof items in the given catalog';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $catalogPath = $this->argument('catalogFile');
        $destinationPath = $this->argument('destinationFile');

        if (!file_exists($catalogPath)) {
            $this->error('Catalog file does not exist');
            return;
        }

        if(is_dir($destinationPath)) {
            $this->error('Destination needs to be a file.');
            return;
        }

        if (!Str::endsWith($catalogPath, '.json')) {
            $this->error('Catalog file is not a JSON file.');
        }

        $catalog = json_decode(file_get_contents($catalogPath), true);

        foreach ($catalog['result'] as &$item) {
            $this->info('Processing Item: ' . $item['displayName']);
            try {
                if (
                    static::checkForVambrace($item) ||
                    static::checkForWeapon($item) ||
                    static::checkForPower($item) ||
                    static::checkForPerk($item) ||
                    static::checkForSkin($item)
                )
                    continue;
            } catch (Exception $ex) {
                $this->error($ex->getMessage());
            }
        }

        file_put_contents($destinationPath, json_encode($catalog, JSON_PRETTY_PRINT));
    }

    /**
     * @param array $item
     * @return bool
     * @throws Exception
     */
    protected static function checkForWeapon(array &$item): bool
    {
        if (!static::checkIfTagExists($item['metaData']['gameplayTags'], 'Weapon.'))
            return false;

        // Skip if there are no default costs set
        if(count($item['defaultCost']) === 0)
            return false;

        $itemLevel = static::getLevel($item['displayName']);

        if($itemLevel === false)
            throw new Exception('Failed to get Item level for Item '. $item['displayName']);

        $item['defaultCost'] = CatalogPriceConfig::GetCategoryPriceForLevel(CatalogPriceCategory::Weapon, $itemLevel);
        return true;
    }

    /**
     * @param array $item
     * @return bool
     * @throws Exception
     */
    protected static function checkForVambrace(array &$item): bool
    {
        if (!static::checkIfTagExists($item['metaData']['gameplayTags'], 'Weapon.ICR'))
            return false;

        // Skip if there are no default costs set
        if(count($item['defaultCost']) === 0)
            return false;

        $itemLevel = static::getLevel($item['displayName']);

        if($itemLevel === false)
            throw new Exception('Failed to get Item level for Item '. $item['displayName']);

        $item['defaultCost'] = CatalogPriceConfig::GetCategoryPriceForLevel(CatalogPriceCategory::Vambrace, $itemLevel);
        return true;
    }

    /**
     * @param array $item
     * @return bool
     * @throws Exception
     */
    protected static function checkForPower(array &$item): bool
    {
        if (!static::checkIfTagExists($item['metaData']['gameplayTags'], 'Ability.'))
            return false;

        // Skip if there are no default costs set
        if(count($item['defaultCost']) === 0)
            return false;

        $itemLevel = static::getLevel($item['displayName']);

        if($itemLevel === false)
            throw new Exception('Failed to get Item level for Item '. $item['displayName']);

        $item['defaultCost'] = CatalogPriceConfig::GetCategoryPriceForLevel(CatalogPriceCategory::Powers, $itemLevel);
        return true;
    }

    /**
     * @param array $item
     * @return bool
     * @throws Exception
     */
    protected static function checkForPerk(array &$item): bool
    {
        if (!static::checkIfTagExists($item['metaData']['gameplayTags'], 'Accessory.Perk'))
            return false;

        // Skip if there are no default costs set
        if(count($item['defaultCost']) === 0)
            return false;

        $itemLevel = static::getLevel($item['displayName']);

        if($itemLevel === false)
            throw new Exception('Failed to get Item level for Item '. $item['displayName']);

        $item['defaultCost'] = CatalogPriceConfig::GetCategoryPriceForLevel(CatalogPriceCategory::Perks, $itemLevel);
        return true;
    }

    /**
     * @param array $item
     * @return bool
     * @throws Exception
     */
    protected static function checkForSkin(array &$item): bool
    {
        if (!static::checkIfTagExists($item['metaData']['gameplayTags'], 'Customization.'))
            return false;

        // Skip if there are no default costs set
        if(count($item['defaultCost']) === 0)
            return false;

        $item['defaultCost'] = CatalogPriceConfig::GetCategoryPriceForLevel(CatalogPriceCategory::Skins, $item['metaData']['quality']);
        return true;
    }

    protected static function checkIfTagExists(array &$tags, string $tag): bool {
        foreach ($tags as $gameplayTag) {
            if(str_starts_with($gameplayTag, $tag))
                return true;
        }
        return false;
    }

    protected static function getLevel(string $displayName): int|false
    {
        $match = preg_match('/(_[0-9][0-9][0-9]_)/', $displayName, $matches);

        if($match === false || $match === 0)
            return false;

        return match($matches[0]) {
            '_001_' => 1,
            '_002_' => 2,
            '_003_' => 3,
            '_004_' => 4,
            '_005_' => 5,
            '_006_' => 6,
            '_007_' => 7,
            '_008_' => 8,
            '_009_' => 9,
            '_010_' => 10,
            default => false,
        };
    }
}
