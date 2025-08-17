<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('services')->truncate();

        \DB::table('services')->insert(array(
            array(
                'name' => 'Haircut',
                'description' => 'Professional haircut with styling.',
                'duration' => 30,
                'price' => 50000,
                'photo' => 'https://static1.squarespace.com/static/59d2f85703596eacb7278fd7/t/5a9ea054ec212d0cc09dd25a/1520345467954/Michelle+Foo+profile-circle.png',
                'created_at' => now(),
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            array(
                'name' => 'Beard Trim',
                'description' => 'Detailed beard trimming and shaping.',
                'duration' => 20,
                'price' => 35000,
                'photo' => 'https://static1.squarespace.com/static/59d2f85703596eacb7278fd7/t/5a9ea054ec212d0cc09dd25a/1520345467954/Michelle+Foo+profile-circle.png',
                'created_at' => now(),
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            array(
                'name' => 'Hair Wash',
                'description' => 'Hair wash with massage and conditioning.',
                'duration' => 15,
                'price' => 25000,
                'photo' => 'https://static1.squarespace.com/static/59d2f85703596eacb7278fd7/t/5a9ea054ec212d0cc09dd25a/1520345467954/Michelle+Foo+profile-circle.png',
                'created_at' => now(),
                'updated_at' => NULL,
                'deleted_at' => NULL,
            )
        ));

    }
}
