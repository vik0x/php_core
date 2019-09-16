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
        $permission = Permission::create(['name' => 'view users']);
        Permission::create(['name' => 'create users', 'parent_id' => $permission->id]);
        Permission::create(['name' => 'update users', 'parent_id' => $permission->id]);
        Permission::create(['name' => 'delete users', 'parent_id' => $permission->id]);

        // Roles
        $permission = Permission::create(['name' => 'view roles']);
        Permission::create(['name' => 'create roles', 'parent_id' => $permission->id]);
        Permission::create(['name' => 'update roles', 'parent_id' => $permission->id]);
        Permission::create(['name' => 'delete roles', 'parent_id' => $permission->id]);
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
    }
}