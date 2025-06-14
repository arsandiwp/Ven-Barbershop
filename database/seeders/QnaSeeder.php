<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Qna;

class QnaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $qnas = [
            ['question' => 'Pertanyaan 1 ?', 'answer' => 'Jawaban 1'],
            ['question' => 'Pertanyaan 2 ?', 'answer' => 'Jawaban 2'],
            ['question' => 'Pertanyaan 3 ?', 'answer' => 'Jawaban 3'],
            ['question' => 'Pertanyaan 4 ?', 'answer' => 'Jawaban 4'],
        ];

        foreach ($qnas as $qna) {
            Qna::create($qna);
        }
    }
}
