<?php

namespace App\Console\Commands;

use App\Enums\Auth\Roles;
use App\Models\User\User;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class AddRoleToUserByName extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permission:addRoleToUserByName {role} {userName}';

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
        $roleToAdd = $this->argument('role');
        $role = Roles::tryFrom($roleToAdd);

        if($role === null) {
            $this->error('Could not find role "'.$roleToAdd.'".');
            return;
        }

        $userName = $this->argument('userName');

        $foundUsers = User::where('last_known_username', 'LIKE', '%'.$userName.'%')->get(['id', 'last_known_username', 'steam_id']);

        if($foundUsers->count() === 0) {
            $this->error('No user found with username "'.$userName.'"');
            return;
        }

        if($foundUsers->count() > 1)
            $user = $this->displayUserSelection($foundUsers);
        else
            $user = $foundUsers[0];

        if($user->hasRole($role)){
            $this->warn('User "'.$user->id.'" ('.$user->last_known_username.') already has role "'.$roleToAdd.'"');
            return;
        }

        $user->assignRole($role);
        $this->info('User "'.$user->id.'" now has the role "'.$roleToAdd.'"');
    }

    /**
     *
     * @param \Illuminate\Database\Eloquent\Collection|User[] $users
     * @return User|false
     */
    private function displayUserSelection(\Illuminate\Database\Eloquent\Collection $users): User|false
    {
        $this->info("Multiple Users with name found, please select one:");

        $this->info("                   User Cloud ID                   Steam ID         Username");
        foreach ($users as $index => $user) {
            $outString = '['.$index.']    '.$user->id.'    '.$user->steam_id.'    '.$user->last_known_username;
            $this->info($outString);
        }

        $answer = $this->ask('Selected User: ', false);

        return $users[$answer];
    }
}
