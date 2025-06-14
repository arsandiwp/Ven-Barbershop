<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PrivilegeSeeder extends Seeder
{
    public function run()
    {
        \DB::table('privileges')->truncate();

        \DB::table('privileges')->insert(array(
            array(
                'name' => 'User - List Paginate',
                'description' => 'UserController@getPaginate',
                'created_at' => now(),
                'updated_at' => NULL,
            ),
            array(
                'name' => 'User - Detail',
                'description' => 'UserController@get',
                'created_at' => now(),
                'updated_at' => NULL,
            ),
            array(
                'name' => 'User - Create',
                'description' => 'UserController@create',
                'created_at' => now(),
                'updated_at' => NULL,
            ),
            array(
                'name' => 'User - Update',
                'description' => 'UserController@update',
                'created_at' => now(),
                'updated_at' => NULL,
            ),
            array(
                'name' => 'User - Delete',
                'description' => 'UserController@delete',
                'created_at' => now(),
                'updated_at' => NULL,
            ),
            array(
                'name' => 'User - Restore',
                'description' => 'UserController@restore',
                'created_at' => now(),
                'updated_at' => NULL,
            ),
            array(
                'name' => 'Role - List Paginate',
                'description' => 'RoleController@getPaginate',
                'created_at' => now(),
                'updated_at' => NULL,
            ),
            array(
                'name' => 'Role - Detail',
                'description' => 'RoleController@get',
                'created_at' => now(),
                'updated_at' => NULL,
            ),
            array(
                'name' => 'Role - Create',
                'description' => 'RoleController@create',
                'created_at' => now(),
                'updated_at' => NULL,
            ),
            array(
                'name' => 'Role - Update',
                'description' => 'RoleController@update',
                'created_at' => now(),
                'updated_at' => NULL,
            ),
            array(
                'name' => 'Role - Delete',
                'description' => 'RoleController@delete',
                'created_at' => now(),
                'updated_at' => NULL,
            ),
            array(
                'name' => 'Role - Restore',
                'description' => 'RoleController@restore',
                'created_at' => now(),
                'updated_at' => NULL,
            ),
            array(
                'name' => 'Privilege - List Paginate',
                'description' => 'PrivilegeController@getPaginate',
                'created_at' => now(),
                'updated_at' => NULL,
            )
        ));
    }
}
