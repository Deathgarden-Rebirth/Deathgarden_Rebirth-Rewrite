<?php

namespace App\Enums\Auth;

use Spatie\Permission\Models\Role;

enum Roles: string
{
    case Admin = 'admin';

    case Moderator = 'moderator';

    case Playtester = 'playtester';

    public function getRole(): Role
    {
        return Role::findByName($this->value);
    }
}
