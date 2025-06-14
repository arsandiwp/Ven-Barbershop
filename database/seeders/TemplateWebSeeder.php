<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TemplateWeb;
use Illuminate\Support\Str;

class TemplateWebSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $datas = [
            ['title' => 'WMS HEJ', 'token' => Str::random(32)],
            ['title' => 'CRM HEJ', 'token' => Str::random(32)]
        ];

        foreach ($datas as $item) {
            TemplateWeb::create($item);
        }
    }
}
