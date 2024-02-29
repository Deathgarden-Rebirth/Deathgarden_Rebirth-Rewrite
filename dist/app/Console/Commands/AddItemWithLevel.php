<?php

namespace App\Console\Commands;

use App\Models\Game\CatalogItem;
use App\Models\User\User;
use Illuminate\Console\Command;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Str;

class AddItemWithLevel extends Command
{
    const ALLOWED_ITEM_TAGS = [
        'Accessory.Perk',
        'Weapon.ICR',
        'Weapon.AssaultRifle',
        'Weapon.Shotgun',
        'Weapon.Launcher',
        'Weapon.SniperRifle',
        'Weapon.Carbine',
        'Ability.Fade',
        'Ability.DropMine',
        'Ability.Strike',
        'Ability.SpawnTurret',
    ];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'game:add-items-with-level {user} {level}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adds all weapons and perks of this level to your inventory';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user');
        $level = $this->argument('level');

        if($level < 1 || $level > 10) {
            $this->error('Level is not between 1 and 10, exiting...');
            return;
        }

        $levelString = match ($level) {
            '1' => '_001_',
            '2' => '_002_',
            '3' => '_003_',
            '4' => '_004_',
            '5' => '_005_',
            '6' => '_006_',
            '7' => '_007_',
            '8' => '_008_',
            '9' => '_009_',
            '10' => '_010_',
            default => 'yeet',
        };

        if(Str::contains($userId, '-'))
            $user = User::find($userId);
        else
            $user = User::findBySteamID($userId);

        if ($user === null) {
            $this->error('User id "' . $userId . '" not found');
            return;
        }

        $playerData = $user->playerData();
        $catalogItemIds = CatalogItem::all();

        foreach ($catalogItemIds as $item) {
            $tags = $item->getGameplayTags();

            if(empty(array_intersect($tags, static::ALLOWED_ITEM_TAGS)))
                continue;

            if(Str::contains($item->display_name, $levelString)) {
                try {
                    $playerData->inventory()->attach($item->id);
                    $this->info('Attached Item ' . $item->id);
                } catch (UniqueConstraintViolationException $e) {
                    $this->warn('Skipping Item ' . $item->id . 'because it already is inside the players inventory.');
                }
            }
        }
    }
}
