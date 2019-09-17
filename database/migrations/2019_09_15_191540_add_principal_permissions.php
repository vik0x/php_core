<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;

class AddPrincipalPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Users
        $permission = Permission::create(['name' => 'view users', 'guard_name' => 'api']);
        Permission::create(['name' => 'create users', 'guard_name' => 'api', 'parent_id' => $permission->id]);
        Permission::create(['name' => 'update users', 'guard_name' => 'api', 'parent_id' => $permission->id]);
        Permission::create(['name' => 'delete users', 'guard_name' => 'api', 'parent_id' => $permission->id]);

        // Roles
        $permission = Permission::create(['name' => 'view roles', 'guard_name' => 'api']);
        Permission::create(['name' => 'create roles', 'guard_name' => 'api', 'parent_id' => $permission->id]);
        Permission::create(['name' => 'update roles', 'guard_name' => 'api', 'parent_id' => $permission->id]);
        Permission::create(['name' => 'delete roles', 'guard_name' => 'api', 'parent_id' => $permission->id]);
        app('cache')->forget('spatie.permission.cache');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Users
        Permission::findByName('create users')->delete();
        Permission::findByName('update users')->delete();
        Permission::findByName('delete users')->delete();
        Permission::findByName('view users')->delete();

        // Roles
        Permission::findByName('create roles')->delete();
        Permission::findByName('update roles')->delete();
        Permission::findByName('delete roles')->delete();
        Permission::findByName('view roles')->delete();
        app('cache')->forget('spatie.permission.cache');
    }
}
