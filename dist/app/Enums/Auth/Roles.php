<?php

namespace App\Enums\Auth;

use Spatie\Permission\Models\Role;

enum Roles: string
{
    case ADMIN = 'admin';

    public function getRole(): Role
    {
        return Role::findByName($this->value);
    }
}
