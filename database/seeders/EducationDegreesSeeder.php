<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\EducationDegree;

class EducationDegreesSeeder extends Seeder
{
    public function run(): void
    {
        $degrees = [
            [
                'title_ar' => 'دبلوم',
                'title_en' => 'Diploma',
            ],
            [
                'title_ar' => 'دبلوم عالي',
                'title_en' => 'Higher Diploma',
            ],
            [
                'title_ar' => 'بكالوريوس',
                'title_en' => 'Bachelor\'s',
            ],
            [
                'title_ar' => 'ماجستير',
                'title_en' => 'Master\'s',
            ],
            [
                'title_ar' => 'دكتوراه',
                'title_en' => 'Doctorate (PhD)',
            ],
            [
                'title_ar' => 'زمالة',
                'title_en' => 'Fellowship',
            ],
        ];

        foreach ($degrees as $degree) {
            EducationDegree::updateOrCreate(
                ['title_en' => $degree['title_en']],
                [
                    'uuid' => Str::uuid(),
                    'title_ar' => $degree['title_ar'],
                ]
            );
        }
    }
}
