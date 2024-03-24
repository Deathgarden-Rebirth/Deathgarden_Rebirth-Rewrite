<?php

namespace Database\Seeders;

use App\Enums\Auth\Permissions;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default Roles and Permissions
       $adminRole = Role::findOrCreate(\App\Enums\Auth\Roles::ADMIN->value);
       $viewAdminAreaPerm = Permission::findOrCreate(Permissions::ADMIN_AREA->value);
       $viewLogPerm = Permission::findOrCreate(Permissions::VIEW_LOG->value);
       $fileUploadPerm = Permission::findOrCreate(Permissions::FILE_UPLOAD->value);
       $gameNewsPerm = Permission::findOrCreate(Permissions::GAME_NEWS->value);
       $usersPerm = Permission::findOrCreate(Permissions::USER_MANAGEMENT->value);

       $adminRole->givePermissionTo($viewLogPerm);
       $adminRole->givePermissionTo($gameNewsPerm);
       $adminRole->givePermissionTo($viewAdminAreaPerm);
       $adminRole->givePermissionTo($usersPerm);
       $adminRole->givePermissionTo($fileUploadPerm)->save();
    }
}
