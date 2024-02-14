<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create default Roles and Permissions
        $adminRole = Role::create(['name' => \App\Enums\Auth\Roles::ADMIN]);
        $viewLogPerm = Permission::create(['name' => \App\Enums\Auth\Permissions::VIEW_LOG]);
        $viewLogPerm->save();

        $adminRole->givePermissionTo($viewLogPerm)->save();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

    }
};
