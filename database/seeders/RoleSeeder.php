<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run()
    {
        \DB::table('roles')->truncate();

        \DB::table('roles')->insert(array(
            array(
                'id' => 1,
                'name' => 'Super Admin',
                'parent_id' => NULL,
                'score' => NULL,
                'created_at' => now(),
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            array(
                'id' => 2,
                'name' => 'Barber',
                'parent_id' => NULL,
                'score' => NULL,
                'created_at' => now(),
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            array(
                'id' => 3,
                'name' => 'Customer',
                'parent_id' => NULL,
                'score' => NULL,
                'created_at' => now(),
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
        ));
    }
}
