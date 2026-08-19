<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\EducationMajor;

class EducationMajorsSeeder extends Seeder
{
    public function run(): void
    {
        $educationMajors = [
            [
                'title_ar' => 'الفنون والعلوم الإنسانية والاجتماعية',
                'title_en' => 'Arts, Humanities & Social Sciences',
            ],
            [
                'title_ar' => 'إدارة الأعمال والاقتصاد',
                'title_en' => 'Business & Economics',
            ],
            [
                'title_ar' => 'العلوم والتكنولوجيا والهندسة والرياضيات',
                'title_en' => 'Science, Technology, Engineering & Math (STEM)',
            ],
            [
                'title_ar' => 'الصحة والطب',
                'title_en' => 'Health & Medicine',
            ],
            [
                'title_ar' => 'التعليم',
                'title_en' => 'Education',
            ],
            [
                'title_ar' => 'الفنون الإبداعية والتصميم',
                'title_en' => 'Creative Arts & Design',
            ],
            [
                'title_ar' => 'القانون والحكومة والخدمة العامة',
                'title_en' => 'Law, Government & Public Service',
            ],
            [
                'title_ar' => 'البيئة والزراعة',
                'title_en' => 'Environment & Agriculture',
            ],
        ];

        foreach ($educationMajors as $major) {
            EducationMajor::updateOrCreate(
                ['title_en' => $major['title_en']],
                ['title_ar' => $major['title_ar']]
            );
        }
    }
}
