<?php

namespace App\Console\Commands;

use App\Classes\Character\CharacterItemConfig;
use App\Enums\Game\Characters;
use App\Models\User\User;
use Illuminate\Console\Command;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Str;

class AddCharacterToUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'game:add-characters-to-user {user} {characters?*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adds a character to the users Inventory';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user');
        $characters = $this->argument('characters');

        if(Str::contains($userId, '-'))
            $user = User::find($userId);
        else
            $user = User::findBySteamID($userId);

        if ($user === null) {
            $this->error('User id "' . $userId . '" not found');
            return;
        }

        if(count($characters) <= 0) {
            $characters = Characters::cases();
        }
        else {
            $foundCharacters = [];
            foreach ($characters as $character) {
                $parsed = Characters::tryFrom($character);
                if ($parsed === null && $character === 'Fog') {
                    $foundCharacters[] = Characters::Smoke;
                }
                else if($parsed === null) {
                    $this->error('Could not parse Character "'.$character.'", skipping');
                    continue;
                }
                $foundCharacters[] = $parsed;
            }

            $characters = $foundCharacters;
        }
        $playerData = $user->playerData();

        foreach ($characters as $character) {
            /** @var CharacterItemConfig $config */
            $config = $character->getCharacter()->getItemConfigClass();
            $characterId = $config::getCharacterId();

            try {
                $this->info('Attaching Character "'.$characterId->toString().'" ('.$character->value.')');
                $playerData->inventory()->attach($characterId->toString());
            } catch (UniqueConstraintViolationException $e) {
                $this->warn('Skipping Character '.$characterId.' ('.$character->value.') because it already is inside the players inventory.');
            }

        }
    }
}
