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
       $userReadPerm = Permission::findOrCreate(Permissions::VIEW_USERS->value);
       $userEditPerm = Permission::findOrCreate(Permissions::EDIT_USERS->value);
       $viewMaintenanceMode = Permission::findOrCreate(Permissions::VIEW_MAINTENANCE->value);
       $inboxMailerPerm = Permission::findOrCreate(Permissions::INBOX_MAILER->value);

       $adminRole->givePermissionTo(
           $viewAdminAreaPerm,
           $viewLogPerm,
           $gameNewsPerm,
           $userReadPerm,
           $userEditPerm,
           $fileUploadPerm,
           $viewMaintenanceMode,
           $inboxMailerPerm,
       )->save();
    }
}
