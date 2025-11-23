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
                'name' => 'Nikol',
                'phone' => '',
                'address' => 'Yogyakarta, Kota Yogyakarta, Daerah Istimewa Yogyakarta',
                'photo' => 'https://www.tangerangkota.go.id/assets/storage/files/photos/145091-ea571676ce9b75b0730a5d56350ae93e-145091.jpeg',
                'email' => 'nikol@gmail.com',
                'password' => $this->userService->makePassword(),
                'status' => 'Active',
                'role_id' => 2,
                'created_at' => now(),
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            array(
                'id' => 3,
                'name' => 'Yafet',
                'phone' => '',
                'address' => 'Yogyakarta, Kota Yogyakarta, Daerah Istimewa Yogyakarta',
                'photo' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSy_zS1EQ-m7tjQpuhNM9cX71V1krqrLQat6f5vxeCUt9Aqv5jIeLvieIDZiXrPGEgWQOc&usqp=CAU',
                'email' => 'yafet@gmail.com',
                'password' => $this->userService->makePassword(),
                'status' => 'Active',
                'role_id' => 2,
                'created_at' => now(),
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            array(
                'id' => 4,
                'name' => 'Manto',
                'phone' => '',
                'address' => 'Yogyakarta, Kota Yogyakarta, Daerah Istimewa Yogyakarta',
                'photo' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRmIfgPvMn7htEkw80HEcGeKsyzZLyax1og0lF-T8KKJ7pckMFqYLTiLRzAUfUkah1IP6w&usqp=CAU',
                'email' => 'manto@gmail.com',
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
