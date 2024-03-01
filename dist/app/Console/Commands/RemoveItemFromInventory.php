<?php

namespace App\Console\Commands;

use App\Models\Game\CatalogItem;
use App\Models\User\User;
use Illuminate\Console\Command;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

class RemoveItemFromInventory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'game:remove-item-from-inventory {user} {item}';

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

        $itemId = Uuid::fromString($this->argument('item'))->toString();
        $itemToRemove = CatalogItem::findOrFail($itemId);

        $foundItem = $user->playerData()->inventory()->find($itemToRemove->id);

        if($foundItem === null)
            $this->warn('Item '.$itemToRemove->id.' ('.$itemToRemove->display_name.') already not in Inventory');
        else {
            $user->playerData()->inventory()->detach($itemToRemove);
            $this->info('Removed Item ' . $itemToRemove->id.' ('.$itemToRemove->display_name.')');
        }
    }
}
