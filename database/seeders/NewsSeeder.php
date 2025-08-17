<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('news')->truncate();
        \DB::table('news')->insert(array(
            array(
                'title' => 'Grand Opening Barbershop',
                'date' => now(),
                'status' => 'Published',
                'description' => 'Kami dengan bangga membuka cabang baru barbershop kami!',
                'image_url' => 'https://static1.squarespace.com/static/59d2f85703596eacb7278fd7/t/5a9ea054ec212d0cc09dd25a/1520345467954/Michelle+Foo+profile-circle.png',
                'custom_url' => 'grand-opening-barbershop',
                'created_at' => now(),
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            array(
                'title' => 'Promo Cukur Gratis',
                'date' => now()->subDays(10),
                'status' => 'Draft',
                'description' => 'Ikuti promo menarik cukur gratis bagi pelanggan pertama!',
                'image_url' => 'https://static1.squarespace.com/static/59d2f85703596eacb7278fd7/t/5a9ea054ec212d0cc09dd25a/1520345467954/Michelle+Foo+profile-circle.png',
                'custom_url' => 'promo-cukur-gratis',
                'created_at' => now(),
                'updated_at' => NULL,
                'deleted_at' => NULL,
            )
        ));
    }
}
