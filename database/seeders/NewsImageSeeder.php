<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NewsImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('news_images')->truncate();
        \DB::table('news_images')->insert(array(
            array(
                'news_id' => 1,
                'image_url' => 'https://static1.squarespace.com/static/59d2f85703596eacb7278fd7/t/5a9ea054ec212d0cc09dd25a/1520345467954/Michelle+Foo+profile-circle.png',
                'created_at' => now(),
                'updated_at' => NULL,
            ),
            array(
                'news_id' => 1,
                'image_url' => 'https://static1.squarespace.com/static/59d2f85703596eacb7278fd7/t/5a9ea054ec212d0cc09dd25a/1520345467954/Michelle+Foo+profile-circle.png',
                'created_at' => now(),
                'updated_at' => NULL,
            ),
            array(
                'news_id' => 2,
                'image_url' => 'https://static1.squarespace.com/static/59d2f85703596eacb7278fd7/t/5a9ea054ec212d0cc09dd25a/1520345467954/Michelle+Foo+profile-circle.png',
                'created_at' => now(),
                'updated_at' => NULL,
            )
        ));
    }
}
