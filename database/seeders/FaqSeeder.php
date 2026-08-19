<?php

namespace Database\Seeders;

use App\Enums\UserTypesEnum;
use App\Models\Faq;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            [
                'question_ar' => 'كيف يمكنني توظيف مستقلين؟',
                'question_en' => 'How can I hire freelancers?',
                'answer_ar' => 'اضغط إما كمستقل أو كصاحب عمل، وأستمتع بإتمام المهام لدينا خلال منصة متكامل يوفر عليك الكثير من الوقت والجهد.',
                'answer_en' => 'Click either as a freelancer or as an employer, and enjoy completing tasks on our integrated platform that saves you a lot of time and effort.',
                'type' => UserTypesEnum::SEEKER->value,
                'is_active' => true,
            ],
            [
                'question_ar' => 'كيف يمكنني تحسين مهاراتي؟',
                'question_en' => 'How can I improve my skills?',
                'answer_ar' => 'قم بتوظيف مستقلين يمتلكون مهارات بأحترافية وجودة عالية ، توفر لك بيئة آمنة وجودة لتحقيق احتياجاتك المهنيين.',
                'answer_en' => 'Hire freelancers with professional and high-quality skills, providing you with a safe and quality environment to achieve your professional needs.',
                'type' => UserTypesEnum::PROVIDER->value,
                'is_active' => true,
            ],
            [
                'question_ar' => 'كيف يمكنني نشر مشروعي؟',
                'question_en' => 'How can I post my project?',
                'answer_ar' => 'اضغط مشروعك الآن وأحصل عروض من المستقلين المحترفين.',
                'answer_en' => 'Post your project now and get offers from professional freelancers.',
                'type' => UserTypesEnum::SEEKER->value,
                'is_active' => true,
            ],
            [
                'question_ar' => 'كيف يمكنني الحصول على عملاء؟',
                'question_en' => 'How can I get clients?',
                'answer_ar' => 'قم بإعمالك التصميمات الفريدة المتاحة وجودة الخدمة الاحترافية لك.',
                'answer_en' => 'Showcase your unique work with available professional service quality.',
                'type' => UserTypesEnum::PROVIDER->value,
                'is_active' => true,
            ],
        ];

        foreach ($faqs as $faqData) {
            Faq::updateOrCreate(
                ['question_ar' => $faqData['question_ar']],
                array_merge($faqData, ['uuid' => Str::uuid()])
            );
        }
    }
}
