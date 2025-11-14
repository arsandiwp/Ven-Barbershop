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
                'phone' => '+6282293429168',
                'address' => 'Yogyakarta, Kota Yogyakarta, Daerah Istimewa Yogyakarta',
                'photo' => 'https://media.istockphoto.com/id/1192884194/id/vektor/admin-masuk-pada-ikon-laptop-vektor-stok.jpg?s=612x612&w=0&k=20&c=tkPYtuUM7Mvw2p2iHtv-BjSTadq9mdF1WemBKlooMJs=',
                'email' => 'super@admin.id',
                'password' => $this->userService->makePassword(),
                'status' => 'Active',
                'role_id' => 1,
                'created_at' => now(),
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            array(
                'id' => 2,
                'name' => 'Nikol Yafet Manto',
                'phone' => '',
                'address' => 'Yogyakarta, Kota Yogyakarta, Daerah Istimewa Yogyakarta',
                'photo' => 'https://www.1stformationsblog.co.uk/wp-content/uploads/2023/09/Shutterstock_2302169201-1024x512.jpg',
                'email' => 'nikol@gmail.com',
                'password' => $this->userService->makePassword(),
                'status' => 'Active',
                'role_id' => 2,
                'created_at' => now(),
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
        ));
    }
}
