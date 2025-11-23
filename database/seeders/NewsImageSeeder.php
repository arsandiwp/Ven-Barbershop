<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class NewsImageSeeder extends Seeder
{
    public function run(): void
    {
        \DB::table('news_images')->truncate();
        // \DB::table('news_images')->insert([
        //     [
        //         'news_id' => 1,
        //         'image_url' => 'https://via.placeholder.com/800x600.png?text=Opening+Promo',
        //         'created_at' => now(),
        //         'updated_at' => null,
        //     ],
        //     [
        //         'news_id' => 1,
        //         'image_url' => 'https://via.placeholder.com/800x600.png?text=Diskon+50%',
        //         'created_at' => now(),
        //         'updated_at' => null,
        //     ],
        //     [
        //         'news_id' => 2,
        //         'image_url' => 'https://via.placeholder.com/800x600.png?text=Cukur+Gratis',
        //         'created_at' => now(),
        //         'updated_at' => null,
        //     ],
        // ]);
    }
}
