<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class NewsSeeder extends Seeder
{
    public function run(): void
    {
        \DB::table('news')->truncate();
        \DB::table('news')->insert([
            [
                'title' => 'Promo Opening Cabang Baru!',
                'date' => now(),
                'status' => 'Published',
                'description' => 'Nikmati diskon 50% untuk semua layanan di minggu pertama pembukaan cabang baru Ven Barber.',
                'image_url' => 'https://png.pngtree.com/png-vector/20230523/ourmid/pngtree-up-to-50-off-sale-label-design-gold-color-vector-png-image_7106354.png',
                'custom_url' => 'promo-opening-cabang-baru',
                'created_at' => now(),
                'updated_at' => null,
                'deleted_at' => null,
            ],
            [
                'title' => 'Promo Cukur Gratis Spesial Pelanggan Baru',
                'date' => now()->subDays(5),
                'status' => 'Published',
                'description' => 'Dapatkan cukur gratis untuk 20 pelanggan pertama setiap hari selama promo berlangsung.',
                'image_url' => 'https://i.ytimg.com/vi/FQ6WyMOcLhU/hq720.jpg?sqp=-oaymwEhCK4FEIIDSFryq4qpAxMIARUAAAAAGAElAADIQj0AgKJD&rs=AOn4CLDF9rSgHQ8R-SqYYL9-A8C4l-0aMA',
                'custom_url' => 'promo-cukur-gratis',
                'created_at' => now(),
                'updated_at' => null,
                'deleted_at' => null,
            ],
        ]);
    }
}
