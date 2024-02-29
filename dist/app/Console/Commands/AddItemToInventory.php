<?php

namespace App\Console\Commands;

use App\Models\Game\CatalogItem;
use App\Models\User\User;
use Illuminate\Console\Command;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

class AddItemToInventory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'game:add-item-to-inventory {user} {item}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adds an item to the users inventory via Uuid (both spellings, with or without Dashes, work)';

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

        $itemId = Uuid::fromString($this->argument('item'))->toString();
        $itemToAdd = CatalogItem::findOrFail($itemId);

        try {
            $user->playerData()->inventory()->attach($itemToAdd);
            $this->info('Added Item ' . $itemToAdd->id.' ('.$itemToAdd->display_name.')');
        } catch (UniqueConstraintViolationException $e) {
            $this->warn('Could not add Item '.$itemToAdd->id.' ('.$itemToAdd->display_name.') because it already is inside the players inventory.');
        }
    }
}
