<?php

namespace App\Console\Commands;

use App\Models\Game\CatalogItem;
use App\Models\User\User;
use Illuminate\Console\Command;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Str;

class AddAllCatalogItems extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'game:unlock-all-items {user}';

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
        $catalogItemIds = CatalogItem::all('id');

        foreach ($catalogItemIds as $item) {
            try {
                $this->info('Attaching Item '.$item->id);
                $playerData->inventory()->attach($item->id);
            } catch(UniqueConstraintViolationException $e) {
                $this->warn('Skipping Item '.$item->id.'because it already is inside the players inventory.');
            }
        }
    }
}
