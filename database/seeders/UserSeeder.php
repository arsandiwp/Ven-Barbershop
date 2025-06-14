<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\UserService;

class UserSeeder extends Seeder
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('users')->truncate();

        \DB::table('users')->insert(array(
            array(
                'id' => 1,
                'name' => 'Super Admin',
                'phone' => '+6282293429191',
                'address' => 'Yogyakarta, Kota Yogyakarta, Daerah Istimewa Yogyakarta',
                'photo' => 'https://static1.squarespace.com/static/59d2f85703596eacb7278fd7/t/5a9ea054ec212d0cc09dd25a/1520345467954/Michelle+Foo+profile-circle.png',
                'email' => 'super@solen.id',
                'password' => $this->userService->makePassword(),
                'status' => 'Active',
                'role_id' => 1,
                'created_at' => now(),
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            array(
                'id' => 2,
                'name' => 'Admin',
                'phone' => '+6282293429192',
                'address' => 'Yogyakarta, Kota Yogyakarta, Daerah Istimewa Yogyakarta',
                'photo' => 'https://static1.squarespace.com/static/59d2f85703596eacb7278fd7/t/5a9ea054ec212d0cc09dd25a/1520345467954/Michelle+Foo+profile-circle.png',
                'email' => 'admin@solen.id',
                'password' => $this->userService->makePassword(),
                'status' => 'Active',
                'role_id' => 2,
                'created_at' => now(),
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            // array(
            //     'id' => 3,
            //     'name' => 'User',
            //     'email' => 'user@solen.id',
            //     'password' => $this->userService->makePassword(),
            //     'status' => 'Pending',
            //     'role_id' => 3,
            //     'created_at' => now(),
            //     'updated_at' => NULL,
            //     'deleted_at' => NULL,
            // ),
        ));
    }
}
