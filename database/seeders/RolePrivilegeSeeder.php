<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RolePrevilegeSeeder extends Seeder
{
    public function run()
    {
        \DB::table('role_privileges')->truncate();

        $total = \DB::table('privileges')->count('id');

        $role_privileges = [];
        $counter = 1;
        $privilege_id_counter = 1;
        for ($counter; $counter <= $total; $counter++) {
            array_push($role_privileges, [
                'id' => $counter,
                'role_id' => 1,
                'privilege_id' => $privilege_id_counter,
                'created_at' => now(),
                'updated_at' => NULL,
            ]);
            $privilege_id_counter += 1;
        }

        $role_privileges_admin = [];
        $privilege_id_counter = 1;
        for ($counter; $counter <= ($total * 2); $counter++) {
            array_push($role_privileges_admin, [
                'id' => $counter,
                'role_id' => 2,
                'privilege_id' => $privilege_id_counter,
                'created_at' => now(),
                'updated_at' => NULL,
            ]);
            $privilege_id_counter += 1;
        }


        \DB::table('role_privileges')->insert($role_privileges);
        \DB::table('role_privileges')->insert($role_privileges_admin);
    }
}
