<?php

namespace Database\Seeders;

use App\Enums\AttachmentDocumentTypeEnum;
use App\Enums\AttachmentFileTypeEnum;
use App\Enums\AttachmentStorageEnum;
use App\Models\Attachment;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'title_en' => 'Graphics & Design',
                'title_ar' => 'التصميم الجرافيكي',
                'path' => 'certificate_provider_logos/design.png',
                'sub_categories' => [
                    'Logo Design',
                    'Business Cards & Stationery Design',
                    'Social Media Design',
                    'Packaging & Label Design',
                    'Infographic Design',
                    'Website Design',
                    'Flyer & Brochure Design',
                    'Brand Identity & Guidelines',
                    'UI/UX Design',
                    '3D Design & Rendering',
                ],
            ],
            [
                'title_en' => 'Digital Marketing',
                'title_ar' => 'التسويق الرقمي',
                'path' => 'certificate_provider_logos/marketing.png',
                'sub_categories' => [
                    'Social Media Marketing',
                    'Search Engine Optimization (SEO)',
                    'Marketing Strategy',
                    'Public Relations (PR)',
                    'Video Marketing',
                    'Search Engine Marketing (SEM)',
                    'E-commerce Marketing',
                    'Influencer Marketing',
                    'Email Marketing & Automation',
                    'Affiliate Marketing',
                    'Fractional CMO',
                ],
            ],
            [
                'title_en' => 'Video & Animation',
                'title_ar' => 'الفيديو والرسوم المتحركة',
                'path' => 'certificate_provider_logos/video.png',
                'sub_categories' => [
                    'Video Editing',
                    'Video Ads & Commercials',
                    'Social Media Videos',
                    'Logo Animation',
                    'Animated Videos',
                    'E-commerce Product Videos',
                    '3D Animation & Motion Graphics',
                ],
            ],
            [
                'title_en' => 'Programming & Tech',
                'title_ar' => 'البرمجة والتقنية',
                'path' => 'certificate_provider_logos/tech.png',
                'sub_categories' => [
                    'Website Development',
                    'Website Maintenance',
                    'E-commerce Development',
                    'Mobile App Development',
                    'Chatbot Development',
                    'Custom Software Development',
                    'Cybersecurity & Data Protection',
                    'AI & Machine Learning Solutions',
                    'Fractional CTO',
                ],
            ],
            [
                'title_en' => 'Business Consulting',
                'title_ar' => 'الاستشارات التجارية',
                'path' => 'certificate_provider_logos/business.png',
                'sub_categories' => [
                    'Market Research',
                    'Business Plans',
                    'Business Consulting',
                    'Virtual Assistant Services',
                    'Product Management',
                    'Sales Consulting & Management',
                    'E-commerce Consulting',
                    'Pricing Strategies',
                    'Operations Consulting',
                    'Brand Positioning & Rebranding',
                    'Customer Experience Optimization',
                    'Fractional COO',
                ],
            ],
            [
                'title_en' => 'Finance Consulting',
                'title_ar' => 'الاستشارات المالية',
                'path' => 'certificate_provider_logos/finance.png',
                'sub_categories' => [
                    'Accounting Services',
                    'Financial Planning & Services',
                    'Fundraising',
                    'Fractional CFO',
                ],
            ],
            [
                'title_en' => 'E-Commerce',
                'title_ar' => 'التجارة الإلكترونية',
                'path' => 'certificate_provider_logos/ecommerce.png',
                'sub_categories' => [
                    'E-commerce Store Setup',
                    'Product Listing & Optimization',
                    'Store Management',
                    'Amazon FBA Services',
                    'Shopify Development',
                    'Etsy Store Services',
                    'WooCommerce Development',
                    'Dropshipping Support',
                    'E-commerce SEO',
                    'E-commerce Marketing',
                    'E-commerce Product Videos',
                    'E-commerce Consulting',
                ],
            ],
            [
                'title_en' => 'Data',
                'title_ar' => 'البيانات',
                'path' => 'certificate_provider_logos/data.png',
                'sub_categories' => [
                    'Data Entry',
                    'Data Collection & Scraping',
                    'Data Visualization',
                    'Data Analytics',
                    'Data Science',
                    'Machine Learning',
                    'Data Engineering',
                    'Web Analytics',
                    'Statistical Analysis',
                    'Database Management',
                ],
            ],
            [
                'title_en' => 'Writing & Translation',
                'title_ar' => 'الكتابة والترجمة',
                'path' => 'certificate_provider_logos/writing.png',
                'sub_categories' => [
                    'Article & Blog Writing',
                    'Website Content',
                    'Copywriting',
                    'Technical Writing',
                    'Resume & Cover Letters',
                    'Proofreading & Editing',
                    'Book & eBook Writing',
                    'Scriptwriting',
                    'Transcription',
                    'Translation',
                ],
            ],
            [
                'title_en' => 'Music & Audio',
                'title_ar' => 'الموسيقى والصوت',
                'path' => 'certificate_provider_logos/audio.png',
                'sub_categories' => [
                    'Voice Over',
                    'Audio Editing & Post Production',
                    'Intros',
                    'Podcast Editing',
                    'Sound Design',
                ],
            ],
        ];

        foreach ($categories as $cat) {
            $category = Category::updateOrCreate(
                ['title_en' => $cat['title_en']],
                [
                    'uuid' => Str::uuid(),
                    'title_ar' => $cat['title_ar'],
                    'is_enabled' => true,
                ]
            );
            $attachmentPath = 'categorys/116/ZTFco7vRYR29BMNmLb1932hL398V8Fd4Qw4B1mYq.jpg';

            Attachment::updateOrCreate(
                [
                    'attachable_type' => Category::class,
                    'attachable_id' => $category->id,
                    'document_type' => AttachmentDocumentTypeEnum::LOGO->value,
                ],
                [
                    'user_id' => 1,
                    'path' => $attachmentPath,
                    'disk' => AttachmentStorageEnum::S3->value,
                    'type' => AttachmentFileTypeEnum::JPG->value,
                    'file_meta' => json_encode([
                        'original_name' => strtolower(str_replace([' & ', ' '], ['-', '-'], $cat['title_en'])) . '.jpg',
                        'mime_type' => 'image/jpeg',
                        'size' => rand(50000, 500000),
                    ]),
                ]
            );

            foreach ($cat['sub_categories'] as $subTitle) {
                $subCategory = SubCategory::updateOrCreate(
                    [
                        'title_en' => $subTitle,
                        'category_id' => $category->id,
                    ],
                    [
                        'uuid' => Str::uuid(),
                        'title_ar' => match ($subTitle) {
                        'Logo Design' => 'تصميم الشعارات',
                        'Business Cards & Stationery Design' => 'تصميم بطاقات العمل والقرطاسية',
                        'Social Media Design' => 'تصميم وسائل التواصل الاجتماعي',
                        'Packaging & Label Design' => 'تصميم التغليف والملصقات',
                        'Infographic Design' => 'تصميم الإنفوجرافيك',
                        'Website Design' => 'تصميم المواقع',
                        'Flyer & Brochure Design' => 'تصميم النشرات والكتيبات',
                        'Brand Identity & Guidelines' => 'الهوية البصرية والإرشادات',
                        'UI/UX Design' => 'تصميم واجهة وتجربة المستخدم',
                        '3D Design & Rendering' => 'تصميم ثلاثي الأبعاد والعرض',

                        'Social Media Marketing' => 'تسويق عبر وسائل التواصل الاجتماعي',
                        'Search Engine Optimization (SEO)' => 'تحسين محركات البحث (SEO)',
                        'Marketing Strategy' => 'استراتيجية التسويق',
                        'Public Relations (PR)' => 'العلاقات العامة (PR)',
                        'Video Marketing' => 'التسويق بالفيديو',
                        'Search Engine Marketing (SEM)' => 'تسويق عبر محركات البحث (SEM)',
                        'E-commerce Marketing' => 'التسويق للتجارة الإلكترونية',
                        'Influencer Marketing' => 'تسويق عبر المؤثرين',
                        'Email Marketing & Automation' => 'التسويق عبر البريد الإلكتروني والأتمتة',
                        'Affiliate Marketing' => 'التسويق بالعمولة',
                        'Fractional CMO' => 'مدير تسويق جزئي',

                        'Video Editing' => 'تحرير الفيديو',
                        'Video Ads & Commercials' => 'إعلانات الفيديو والإعلانات التجارية',
                        'Social Media Videos' => 'فيديوهات التواصل الاجتماعي',
                        'Logo Animation' => 'تحريك الشعارات',
                        'Animated Videos' => 'فيديوهات متحركة',
                        'E-commerce Product Videos' => 'فيديوهات منتجات التجارة الإلكترونية',
                        '3D Animation & Motion Graphics' => 'الرسوم المتحركة ثلاثية الأبعاد والموشن جرافيك',

                        'Website Development' => 'تطوير المواقع',
                        'Website Maintenance' => 'صيانة المواقع',
                        'E-commerce Development' => 'تطوير المتاجر الإلكترونية',
                        'Mobile App Development' => 'تطوير تطبيقات الجوال',
                        'Chatbot Development' => 'تطوير روبوتات الدردشة',
                        'Custom Software Development' => 'تطوير البرمجيات المخصصة',
                        'Cybersecurity & Data Protection' => 'الأمن السيبراني وحماية البيانات',
                        'AI & Machine Learning Solutions' => 'حلول الذكاء الاصطناعي وتعلم الآلة',
                        'Fractional CTO' => 'مدير تقني جزئي',

                        'Market Research' => 'أبحاث السوق',
                        'Business Plans' => 'خطط العمل',
                        'Business Consulting' => 'الاستشارات التجارية',
                        'Virtual Assistant Services' => 'خدمات المساعد الافتراضي',
                        'Product Management' => 'إدارة المنتجات',
                        'Sales Consulting & Management' => 'الاستشارات والمبيعات',
                        'E-commerce Consulting' => 'استشارات التجارة الإلكترونية',
                        'Pricing Strategies' => 'استراتيجيات التسعير',
                        'Operations Consulting' => 'استشارات العمليات',
                        'Brand Positioning & Rebranding' => 'تموضع العلامة التجارية وإعادة الترويج',
                        'Customer Experience Optimization' => 'تحسين تجربة العملاء',
                        'Fractional COO' => 'مدير عمليات جزئي',

                        'Accounting Services' => 'خدمات المحاسبة',
                        'Financial Planning & Services' => 'التخطيط والخدمات المالية',
                        'Fundraising' => 'جمع التبرعات',
                        'Fractional CFO' => 'مدير مالي جزئي',

                        'E-commerce Store Setup' => 'إعداد متجر إلكتروني',
                        'Product Listing & Optimization' => 'إدراج المنتجات وتحسينها',
                        'Store Management' => 'إدارة المتجر',
                        'Amazon FBA Services' => 'خدمات أمازون FBA',
                        'Shopify Development' => 'تطوير Shopify',
                        'Etsy Store Services' => 'خدمات متجر Etsy',
                        'WooCommerce Development' => 'تطوير WooCommerce',
                        'Dropshipping Support' => 'دعم دروبشيبينغ',
                        'E-commerce SEO' => 'تحسين محركات البحث للتجارة الإلكترونية',
                        'E-commerce Marketing' => 'تسويق التجارة الإلكترونية',
                        'E-commerce Product Videos' => 'فيديوهات منتجات التجارة الإلكترونية',
                        'E-commerce Consulting' => 'استشارات التجارة الإلكترونية',

                        'Data Entry' => 'إدخال البيانات',
                        'Data Collection & Scraping' => 'جمع البيانات واستخلاصها',
                        'Data Visualization' => 'تصوير البيانات',
                        'Data Analytics' => 'تحليلات البيانات',
                        'Data Science' => 'علم البيانات',
                        'Machine Learning' => 'تعلم الآلة',
                        'Data Engineering' => 'هندسة البيانات',
                        'Web Analytics' => 'تحليلات الويب',
                        'Statistical Analysis' => 'التحليل الإحصائي',
                        'Database Management' => 'إدارة قواعد البيانات',

                        'Article & Blog Writing' => 'كتابة المقالات والمدونات',
                        'Website Content' => 'محتوى المواقع',
                        'Copywriting' => 'كتابة الإعلانات',
                        'Technical Writing' => 'الكتابة التقنية',
                        'Resume & Cover Letters' => 'السير الذاتية ورسائل التقديم',
                        'Proofreading & Editing' => 'التدقيق اللغوي والتحرير',
                        'Book & eBook Writing' => 'كتابة الكتب والكتب الإلكترونية',
                        'Scriptwriting' => 'كتابة النصوص',
                        'Transcription' => 'النسخ',
                        'Translation' => 'الترجمة',

                        'Voice Over' => 'التعليق الصوتي',
                        'Audio Editing & Post Production' => 'تحرير الصوت وما بعد الإنتاج',
                        'Intros' => 'مقدمات الفيديو',
                        'Podcast Editing' => 'تحرير البودكاست',
                        'Sound Design' => 'تصميم الصوت',

                        default => $subTitle, // fallback if translation missing
                    },
                    'is_enabled' => true,
                ]
            );

            Attachment::updateOrCreate(
                [
                    'attachable_type' => SubCategory::class,
                    'attachable_id' => $subCategory->id,
                    'document_type' => AttachmentDocumentTypeEnum::LOGO->value,
                ],
                [
                    'user_id' => 1,
                    'path' => $attachmentPath,
                    'disk' => AttachmentStorageEnum::S3->value,
                    'type' => AttachmentFileTypeEnum::SVG->value,
                ]
            );
            }
        }
    }
}
