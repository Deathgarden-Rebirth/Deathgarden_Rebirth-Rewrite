<?php

namespace App\Console\Commands;

use App\Enums\Auth\Roles;
use App\Models\User\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class AddRoleToUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:addRoleToUser {role} {user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adds a Role to a User. User can be the steamID64 or internal UUID';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $roleToAdd = $this->argument('role');
        $role = Roles::tryFrom($roleToAdd);

        if($role === null) {
            $this->error('Could not find role "'.$roleToAdd.'".');
            return;
        }

        $userId = $this->argument('user');

        if(Str::contains($userId, '-'))
            $user = User::find($userId);
        else
            $user = User::findBySteamID($userId);

        if ($user === null) {
            $this->error('User id "' . $userId . '" not found');
            return;
        }

        if($user->hasRole($role)){
            $this->warn('User "'.$user->id.'" already has role "'.$roleToAdd.'"');
            return;
        }


        $user->assignRole($role);
        $this->info('User "'.$user->id.'" now has the role "'.$roleToAdd.'"');
    }
}
