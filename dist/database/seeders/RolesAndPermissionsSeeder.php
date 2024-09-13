<?php

namespace Database\Seeders;

use App\Enums\Auth\Permissions;
use App\Enums\Auth\Roles;
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
       $adminRole = Role::findOrCreate(Roles::Admin->value);
       $moderatorRole = Role::findOrCreate(Roles::Moderator->value);
       $playtesterRole = Role::findOrCreate(Roles::Playtester->value);

       $viewAdminAreaPerm = Permission::findOrCreate(Permissions::ADMIN_AREA->value);
       $viewLogPerm = Permission::findOrCreate(Permissions::VIEW_LOG->value);
       $fileUploadPerm = Permission::findOrCreate(Permissions::FILE_UPLOAD->value);
       $gameNewsPerm = Permission::findOrCreate(Permissions::GAME_NEWS->value);
       $userReadPerm = Permission::findOrCreate(Permissions::VIEW_USERS->value);
       $userEditPerm = Permission::findOrCreate(Permissions::EDIT_USERS->value);
       $userBansPerm = Permission::findOrCreate(Permissions::USER_BANS->value);
       $viewMaintenanceMode = Permission::findOrCreate(Permissions::VIEW_MAINTENANCE->value);
       $inboxMailerPerm = Permission::findOrCreate(Permissions::INBOX_MAILER->value);
       $chatReportsPerm = Permission::findOrCreate(Permissions::CHAT_REPORTS->value);
       $playerReportsPerm = Permission::findOrCreate(Permissions::PLAYER_REPORTS->value);
       $matchConfigPerm = Permission::findOrCreate(Permissions::MATCH_CONFIGIURATION->value);

       $adminRole->givePermissionTo(
           $viewAdminAreaPerm,
           $viewLogPerm,
           $fileUploadPerm,
           $gameNewsPerm,
           $userReadPerm,
           $userEditPerm,
           $userBansPerm,
           $viewMaintenanceMode,
           $inboxMailerPerm,
           $chatReportsPerm,
           $playerReportsPerm,
           $matchConfigPerm,
       )->save();

       $moderatorRole->givePermissionTo(
           $viewAdminAreaPerm,
           $userReadPerm,
           $gameNewsPerm,
           $inboxMailerPerm,
           $userBansPerm,
           $chatReportsPerm,
           $playerReportsPerm,
           $viewMaintenanceMode,
       )->save();

       $playtesterRole->givePermissionTo(
           $viewMaintenanceMode
       )->save();
    }
}
