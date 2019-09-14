<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username')->unique();
            $table->string('password');
            $table->string('email', 300)->unique();
            $table->string('mobile_number')->nullable();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->enum('type', array('root','admin','user'))->default('admin');
            $table->enum('status', array('active','suspended'))->default('active');
            $table->boolean('can_login')->default(1);
            $table->string('reset_password_token')->nullable();
            $table->softDeletes();
            $table->datetime('token_expiration_date')->nullable();
            $table->timestamps();
        });
        $users = [
            [
                'username' => 'root',
                'password' => bcrypt('123123'),
                'email' => 'vgutierrez@vetta.io',
                'first_name' => 'Vetta',
                'last_name' => 'Root',
                'type' => 'root',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'username' => 'admin',
                'password' => bcrypt('123123'),
                'email' => 'admin@vetta.io',
                'first_name' => 'admin',
                'last_name' => 'admin',
                'type' => 'admin',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'username' => 'admin2',
                'password' => bcrypt('123123'),
                'email' => 'admin2@vetta.io',
                'first_name' => 'admin2',
                'last_name' => 'admin2',
                'type' => 'admin',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];
        DB::table('users')->insert($users);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
