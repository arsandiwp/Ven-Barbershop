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
                'description' => 'Enjoy a complete haircut treatment including wash, hair vitamin, and simple styling for a clean, fresh look with no fuss.',
                'duration' => 60,
                'price' => 35000,
                'photo' => 'https://www.shutterstock.com/image-photo/skilled-barber-meticulously-styles-hair-600nw-2521942449.jpg',
                'status' => 'Published',
                'created_at' => now(),
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            array(
                'name' => 'Perming',
                'description' => 'Enjoy a soft, natural-looking perm that adds texture and volume, complete with a professional haircut to refine the shape for a stylish, effortless finish.',
                'duration' => 180,
                'price' => 250000,
                'photo' => 'https://captainbarbershop.id/wp-content/uploads/2024/07/Perming-Hairstyle.jpg',
                'status' => 'Published',
                'created_at' => now(),
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            array(
                'name' => 'Hairlight Full Block',
                'description' => 'Full block highlights for a bold, even color effect, completed with a professional haircut to enhance shape and create a clean, polished finish.',
                'duration' => 180,
                'price' => 250000,
                'photo' => 'https://i.pinimg.com/736x/01/ce/5a/01ce5ad33e8df06ecf568374109ccb37.jpg',
                'status' => 'Published',
                'created_at' => now(),
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            array(
                'name' => 'Hairlight',
                'description' => 'Soft, natural highlights to brighten and add dimension to your hair, completed with a professional haircut for a fresh and refined look.',
                'duration' => 120,
                'price' => 200000,
                'photo' => 'https://cdn.shopify.com/s/files/1/0639/1237/8602/files/Grey_Highlights_Men_2_480x480.jpg?v=1719998522',
                'status' => 'Published',
                'created_at' => now(),
                'updated_at' => NULL,
                'deleted_at' => NULL,
            )
        ));

    }
}
