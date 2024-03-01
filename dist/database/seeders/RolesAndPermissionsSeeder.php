<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
       $viewLogPerm = Permission::findOrCreate(\App\Enums\Auth\Permissions::VIEW_LOG->value);
       $fileUploadPerm = Permission::findOrCreate(\App\Enums\Auth\Permissions::FILE_UPLOAD->value);

       $adminRole->givePermissionTo($viewLogPerm);
       $adminRole->givePermissionTo($fileUploadPerm)->save();
    }
}
