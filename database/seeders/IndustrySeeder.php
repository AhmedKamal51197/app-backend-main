<?php

namespace Database\Seeders;

use App\Models\Industry;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * A class defined for the industry
 */
class IndustrySeeder extends Seeder
{
    public function run(): void
    {
        $industries = [
            ['en' => 'Real Estate', 'ar' => 'العقارات'],
            ['en' => 'Sports', 'ar' => 'الرياضة'],
            ['en' => 'Restaurants', 'ar' => 'المطاعم'],
            ['en' => 'Social Networking', 'ar' => 'الشبكات الاجتماعية'],
            ['en' => 'Healthcare', 'ar' => 'الرعاية الصحية'],
            ['en' => 'Education', 'ar' => 'التعليم'],
            ['en' => 'Finance', 'ar' => 'التمويل'],
            ['en' => 'E-commerce', 'ar' => 'التجارة الإلكترونية'],
            ['en' => 'Travel & Tourism', 'ar' => 'السفر والسياحة'],
            ['en' => 'Entertainment', 'ar' => 'الترفيه'],
            ['en' => 'Technology', 'ar' => 'التكنولوجيا'],
            ['en' => 'Automotive', 'ar' => 'السيارات'],
            ['en' => 'Logistics', 'ar' => 'الخدمات اللوجستية'],
            ['en' => 'Retail', 'ar' => 'البيع بالتجزئة'],
            ['en' => 'Construction', 'ar' => 'الإنشاءات'],
            ['en' => 'Media & Publishing', 'ar' => 'الإعلام والنشر'],
            ['en' => 'Agriculture', 'ar' => 'الزراعة'],
            ['en' => 'Legal', 'ar' => 'القانون'],
            ['en' => 'Telecommunications', 'ar' => 'الاتصالات'],
            ['en' => 'Fashion', 'ar' => 'الموضة'],
            ['en' => 'Gaming', 'ar' => 'الألعاب'],
            ['en' => 'Marketing & Advertising', 'ar' => 'التسويق والإعلان'],
            ['en' => 'Non-Profit', 'ar' => 'المنظمات غير الربحية'],
            ['en' => 'Energy & Utilities', 'ar' => 'الطاقة والمرافق'],
        ];

        foreach ($industries as $industry) {
            Industry::updateOrCreate(
                ['title_en' => $industry['en']],
                ['title_ar' => $industry['ar']]
            );
        }
    }
}
