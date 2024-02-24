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
        $this->info("Multiple Users with name found, please select one:\n");

        $this->info("                   User Cloud ID                   Steam ID         Username\n");
        $this->info("[1]    9b692453-ecec-4cee-9743-07ca4c7f87ca    76561198241289194    Vari");

        foreach ($users as $index => $user) {
            $this->info('['.$index.']    ');
            $this->info($user->id.'    ');
            $this->info($user->steam_id.'    ');
            $this->info($user->last_known_username."\n");
        }

        $answer = $this->ask('Selected User: ', false);

        return $users[$answer];
    }
}
