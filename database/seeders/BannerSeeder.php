<?php

namespace Database\Seeders;

use App\Enums\AttachmentStorageEnum;
use App\Enums\BannerTypeEnum;
use App\Models\Attachment;
use App\Models\Banner;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        $banners = [
            // Main Banners
            [
                'key' => 'onboarding_welcome',
                'type' => BannerTypeEnum::MAIN->value,
                'title_ar' => 'صفحة الترحيب بال Onboarding',
                'title_en' => 'Onboarding Welcome Page',
                'description_ar' => 'اضغط إما كمستقل أو كصاحب عمل، وأستمتع بإتمام المهام لدينا خلال منصة متكامل يوفر عليك الكثير من الوقت والجهد.',
                'description_en' => 'Click either as a freelancer or as an employer, and enjoy completing tasks on our integrated platform that saves you a lot of time and effort.',
                'is_active' => true,
                'attachment' => [
                    'path' => 'banners/6/wuFNaOyRIVeAkg7jYViI3UvehBXJvKf12s2GjJbk.png',
                    'disk' => AttachmentStorageEnum::S3->value,
                ],
            ],
            [
                'key' => 'improve_skills_banner_1',
                'type' => BannerTypeEnum::MAIN->value,
                'title_ar' => 'بانر الرئيسية لتحسين (1)',
                'title_en' => 'Main Banner for Improvement (1)',
                'description_ar' => 'قم بتوظيف مستقلين يمتلكون مهارات بأحترافية وجودة عالية ، توفر لك بيئة آمنة وجودة لتحقيق احتياجاتك المهنيين.',
                'description_en' => 'Hire freelancers with professional and high-quality skills, providing you with a safe and quality environment to achieve your professional needs.',
                'is_active' => true,
                'attachment' => [
                    'path' => 'banners/6/wuFNaOyRIVeAkg7jYViI3UvehBXJvKf12s2GjJbk.png',
                    'disk' => AttachmentStorageEnum::S3->value,
                ],
            ],
            [
                'key' => 'improve_skills_banner_2',
                'type' => BannerTypeEnum::MAIN->value,
                'title_ar' => 'بانر الرئيسية لتحسين (2)',
                'title_en' => 'Main Banner for Improvement (2)',
                'description_ar' => 'اضغط مشروعك الآن وأحصل عروض من المستقلين المحترفين.',
                'description_en' => 'Post your project now and get offers from professional freelancers.',
                'is_active' => true,
                'attachment' => [
                    'path' => 'banners/6/wuFNaOyRIVeAkg7jYViI3UvehBXJvKf12s2GjJbk.png',
                    'disk' => AttachmentStorageEnum::S3->value,
                ],
            ],
        ];

        // Add category banners dynamically
        $categories = [
            ['title_en' => 'Graphics & Design', 'title_ar' => 'التصميم الجرافيكي'],
            ['title_en' => 'Digital Marketing', 'title_ar' => 'التسويق الرقمي'],
            ['title_en' => 'Video & Animation', 'title_ar' => 'الفيديو والرسوم المتحركة'],
            ['title_en' => 'Programming & Tech', 'title_ar' => 'البرمجة والتقنية'],
            ['title_en' => 'Business Consulting', 'title_ar' => 'الاستشارات التجارية'],
            ['title_en' => 'Finance Consulting', 'title_ar' => 'الاستشارات المالية'],
            ['title_en' => 'E-Commerce', 'title_ar' => 'التجارة الإلكترونية'],
            ['title_en' => 'Data', 'title_ar' => 'البيانات'],
            ['title_en' => 'Writing & Translation', 'title_ar' => 'الكتابة والترجمة'],
            ['title_en' => 'Music & Audio', 'title_ar' => 'الموسيقى والصوت'],
        ];

        foreach ($categories as $category) {
            $banners[] = [
                'key' => 'category_' . Str::slug($category['title_en']),
                'type' => BannerTypeEnum::CATEGORY->value,
                'title_ar' => 'بانر قسم ' . $category['title_ar'],
                'title_en' => $category['title_en'] . ' Section Banner',
                'description_ar' => 'قم بإعمالك التصميمات الفريدة المتاحة وجودة الخدمة الاحترافية لك في قسم ' . $category['title_ar'] . '.',
                'description_en' => 'Showcase your unique work with available professional service quality in ' . $category['title_en'] . ' section.',
                'is_active' => true,
                'attachment' => [
                    'path' => 'banners/6/wuFNaOyRIVeAkg7jYViI3UvehBXJvKf12s2GjJbk.png',
                    'disk' => AttachmentStorageEnum::S3->value,
                ],
            ];
        }

        foreach ($banners as $bannerData) {
            $attachmentData = $bannerData['attachment'];
            unset($bannerData['attachment']);

            $banner = Banner::updateOrCreate(
                ['key' => $bannerData['key']],
                array_merge($bannerData, ['uuid' => Str::uuid()])
            );

            Attachment::updateOrCreate(
                [
                    'attachable_type' => Banner::class,
                    'attachable_id' => $banner->id,
                ],
                [
                    'user_id' => 1,
                    'path' => $attachmentData['path'],
                    'disk' => $attachmentData['disk'],
                ]
            );
        }
    }
}
