<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Feature;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * A class defined for the feature seeder
 */
class FeatureSeeder extends Seeder
{
    public function run(): void
    {
        $featuresByCategory = [
            'Graphics & Design' => [
                ['en' => 'Logo Variations', 'ar' => 'أنواع الشعارات'],
                ['en' => 'Brand Guidelines', 'ar' => 'إرشادات العلامة التجارية'],
                ['en' => 'High-Resolution Files', 'ar' => 'ملفات عالية الجودة'],
                ['en' => 'Social Media Kit', 'ar' => 'مجموعة الوسائط الاجتماعية'],
                ['en' => 'Business Card Design', 'ar' => 'تصميم بطاقة العمل'],
                ['en' => 'Vector Files', 'ar' => 'ملفات فيكتور'],
                ['en' => 'Print-Ready Files', 'ar' => 'ملفات جاهزة للطباعة'],
                ['en' => 'Color Variants', 'ar' => 'ألوان متعددة'],
                ['en' => 'Typography Selection', 'ar' => 'اختيار الخطوط'],
                ['en' => '3D Mockups', 'ar' => 'نماذج ثلاثية الأبعاد'],
                ['en' => 'Source Files Included', 'ar' => 'يشمل الملفات المصدرية'],
                ['en' => 'Minimalist Design', 'ar' => 'تصميم بسيط'],
                ['en' => 'Flat Design', 'ar' => 'تصميم مسطح'],
                ['en' => 'Design Revisions', 'ar' => 'مراجعات التصميم'],
                ['en' => 'UI Kit', 'ar' => 'مجموعة واجهات المستخدم'],
                ['en' => 'Website Mockup', 'ar' => 'تصميم مبدأي للموقع'],
                ['en' => 'Packaging Mockup', 'ar' => 'نموذج تغليف'],
                ['en' => 'Adobe XD Files', 'ar' => 'ملفات Adobe XD'],
                ['en' => 'Figma Files', 'ar' => 'ملفات Figma'],
                ['en' => 'Illustration Set', 'ar' => 'مجموعة رسوم توضيحية'],
            ],

            'Digital Marketing' => [
                ['en' => 'SEO Optimization', 'ar' => 'تحسين محركات البحث'],
                ['en' => 'Keyword Research', 'ar' => 'بحث الكلمات المفتاحية'],
                ['en' => 'Email Campaign Setup', 'ar' => 'إعداد حملة بريد إلكتروني'],
                ['en' => 'A/B Testing', 'ar' => 'اختبار A/B'],
                ['en' => 'Analytics Integration', 'ar' => 'تكامل التحليلات'],
                ['en' => 'Ad Copywriting', 'ar' => 'كتابة الإعلانات'],
                ['en' => 'Marketing Strategy', 'ar' => 'استراتيجية التسويق'],
                ['en' => 'Lead Generation', 'ar' => 'توليد العملاء المحتملين'],
                ['en' => 'Hashtag Research', 'ar' => 'بحث الوسوم'],
                ['en' => 'Facebook Ads Setup', 'ar' => 'إعداد إعلانات فيسبوك'],
                ['en' => 'Google Ads Setup', 'ar' => 'إعداد إعلانات Google'],
                ['en' => 'Audience Targeting', 'ar' => 'استهداف الجمهور'],
                ['en' => 'Marketing Automation', 'ar' => 'أتمتة التسويق'],
                ['en' => 'Email Templates', 'ar' => 'قوالب البريد الإلكتروني'],
                ['en' => 'Content Strategy', 'ar' => 'استراتيجية المحتوى'],
                ['en' => 'Social Media Calendar', 'ar' => 'تقويم المحتوى الاجتماعي'],
                ['en' => 'Performance Report', 'ar' => 'تقرير الأداء'],
                ['en' => 'Landing Page Audit', 'ar' => 'مراجعة صفحة الهبوط'],
                ['en' => 'Campaign Report', 'ar' => 'تقرير الحملة'],
                ['en' => 'Retargeting Ads', 'ar' => 'إعلانات إعادة الاستهداف'],
            ],

            'Video & Animation' => [
                ['en' => 'Intro Animation', 'ar' => 'مقدمة متحركة'],
                ['en' => 'Outro Animation', 'ar' => 'نهاية متحركة'],
                ['en' => 'Text Animation', 'ar' => 'تحريك النص'],
                ['en' => 'Voice Over Included', 'ar' => 'يشمل تعليق صوتي'],
                ['en' => 'Background Music', 'ar' => 'موسيقى خلفية'],
                ['en' => 'Subtitles', 'ar' => 'الترجمة'],
                ['en' => '1080p Export', 'ar' => 'جودة 1080p'],
                ['en' => '4K Video Option', 'ar' => 'خيار فيديو 4K'],
                ['en' => 'Script Writing', 'ar' => 'كتابة النص'],
                ['en' => 'Storyboarding', 'ar' => 'رسم القصة'],
                ['en' => 'Stock Footage', 'ar' => 'مقاطع فيديو جاهزة'],
                ['en' => 'Motion Graphics', 'ar' => 'رسوم متحركة'],
                ['en' => 'Logo Animation', 'ar' => 'تحريك الشعار'],
                ['en' => 'Explainer Video', 'ar' => 'فيديو توضيحي'],
                ['en' => 'Visual Effects', 'ar' => 'تأثيرات بصرية'],
                ['en' => 'Green Screen', 'ar' => 'خلفية خضراء'],
                ['en' => 'Custom Animation Style', 'ar' => 'نمط رسوم متحركة مخصص'],
                ['en' => 'Sound Design', 'ar' => 'تصميم الصوت'],
                ['en' => 'Revisions', 'ar' => 'مراجعات'],
                ['en' => 'Fast Delivery', 'ar' => 'تسليم سريع'],
            ],

            'Programming & Tech' => [
                ['en' => 'Source Code Included', 'ar' => 'يشمل الكود المصدري'],
                ['en' => 'Bug Fixing', 'ar' => 'إصلاح الأخطاء'],
                ['en' => 'Performance Optimization', 'ar' => 'تحسين الأداء'],
                ['en' => 'Custom Backend', 'ar' => 'خلفية مخصصة'],
                ['en' => 'Database Integration', 'ar' => 'تكامل قاعدة البيانات'],
                ['en' => 'API Integration', 'ar' => 'تكامل API'],
                ['en' => 'Hosting Setup', 'ar' => 'إعداد الاستضافة'],
                ['en' => 'Security Enhancements', 'ar' => 'تحسينات الأمان'],
                ['en' => 'Responsive UI', 'ar' => 'واجهة مستخدم متجاوبة'],
                ['en' => 'Admin Panel', 'ar' => 'لوحة التحكم'],
                ['en' => 'Code Comments', 'ar' => 'تعليقات على الكود'],
                ['en' => 'Testing & QA', 'ar' => 'الاختبار وضمان الجودة'],
                ['en' => 'Documentation', 'ar' => 'التوثيق'],
                ['en' => 'Version Control (Git)', 'ar' => 'التحكم بالإصدار (Git)'],
                ['en' => 'Mobile Compatibility', 'ar' => 'متوافق مع الجوال'],
                ['en' => 'Deployment Guide', 'ar' => 'دليل النشر'],
                ['en' => 'Scalable Architecture', 'ar' => 'بنية قابلة للتوسع'],
                ['en' => 'Multi-language Support', 'ar' => 'دعم لغات متعددة'],
                ['en' => 'Third-party Integration', 'ar' => 'تكامل مع طرف ثالث'],
                ['en' => 'CMS Integration', 'ar' => 'تكامل مع نظام إدارة المحتوى'],
            ],

            'Business Consulting' => [
                ['en' => 'Business Model Canvas', 'ar' => 'نموذج عمل'],
                ['en' => 'SWOT Analysis', 'ar' => 'تحليل SWOT'],
                ['en' => 'Market Segmentation', 'ar' => 'تقسيم السوق'],
                ['en' => 'Financial Forecast', 'ar' => 'التوقع المالي'],
                ['en' => 'Business Plan Outline', 'ar' => 'مخطط خطة العمل'],
                ['en' => 'Pitch Deck', 'ar' => 'عرض تقديمي للمستثمر'],
                ['en' => 'Competitor Research', 'ar' => 'بحث المنافسين'],
                ['en' => 'Revenue Streams', 'ar' => 'مصادر الإيرادات'],
                ['en' => 'Go-to-Market Strategy', 'ar' => 'استراتيجية دخول السوق'],
                ['en' => 'Investor Brief', 'ar' => 'موجز للمستثمر'],
                ['en' => 'Growth Strategy', 'ar' => 'استراتيجية النمو'],
                ['en' => 'Process Improvement', 'ar' => 'تحسين العمليات'],
                ['en' => 'Business Audit', 'ar' => 'تدقيق تجاري'],
                ['en' => 'Customer Journey Mapping', 'ar' => 'رسم رحلة العميل'],
                ['en' => 'Organizational Chart', 'ar' => 'الهيكل التنظيمي'],
                ['en' => 'OKR/KPI Planning', 'ar' => 'تخطيط OKR/KPI'],
                ['en' => 'Team Restructuring', 'ar' => 'إعادة هيكلة الفريق'],
                ['en' => 'Consultation Session', 'ar' => 'جلسة استشارية'],
                ['en' => 'E-commerce Consulting', 'ar' => 'استشارات التجارة الإلكترونية'],
                ['en' => 'SaaS Consulting', 'ar' => 'استشارات SaaS'],
            ],

            'Finance Consulting' => [
                ['en' => 'Accounting Reports', 'ar' => 'تقارير المحاسبة'],
                ['en' => 'Financial Planning', 'ar' => 'التخطيط المالي'],
                ['en' => 'Budget Preparation', 'ar' => 'إعداد الميزانية'],
                ['en' => 'Cash Flow Analysis', 'ar' => 'تحليل التدفق النقدي'],
                ['en' => 'Fundraising Strategy', 'ar' => 'استراتيجية جمع الأموال'],
                ['en' => 'Investment Analysis', 'ar' => 'تحليل الاستثمارات'],
                ['en' => 'Tax Advisory', 'ar' => 'استشارات ضريبية'],
                ['en' => 'Bookkeeping', 'ar' => 'مسك الدفاتر'],
                ['en' => 'Financial Forecasting', 'ar' => 'التنبؤ المالي'],
                ['en' => 'Debt Management', 'ar' => 'إدارة الديون'],
                ['en' => 'KPI Tracking', 'ar' => 'تتبع مؤشرات الأداء'],
                ['en' => 'Profitability Analysis', 'ar' => 'تحليل الربحية'],
                ['en' => 'Risk Assessment', 'ar' => 'تقييم المخاطر'],
                ['en' => 'Payroll Services', 'ar' => 'خدمات الرواتب'],
                ['en' => 'Audit Preparation', 'ar' => 'التحضير للتدقيق'],
                ['en' => 'Finance SOPs', 'ar' => 'إجراءات مالية قياسية'],
                ['en' => 'Compliance Advisory', 'ar' => 'استشارات الامتثال'],
                ['en' => 'Investor Reporting', 'ar' => 'تقارير المستثمرين'],
                ['en' => 'Valuation Reports', 'ar' => 'تقارير التقييم'],
                ['en' => 'Fractional CFO Support', 'ar' => 'دعم المدير المالي الجزئي'],
            ],

            'E-Commerce' => [
                ['en' => 'Store Setup', 'ar' => 'إعداد المتجر'],
                ['en' => 'Product Upload', 'ar' => 'تحميل المنتجات'],
                ['en' => 'Product Optimization', 'ar' => 'تحسين المنتج'],
                ['en' => 'SEO for Products', 'ar' => 'تحسين محركات البحث للمنتجات'],
                ['en' => 'Shopify Customization', 'ar' => 'تخصيص Shopify'],
                ['en' => 'WooCommerce Integration', 'ar' => 'تكامل WooCommerce'],
                ['en' => 'Payment Gateway Integration', 'ar' => 'تكامل بوابة الدفع'],
                ['en' => 'Order Management', 'ar' => 'إدارة الطلبات'],
                ['en' => 'Store Migration', 'ar' => 'نقل المتجر'],
                ['en' => 'Email Marketing Setup', 'ar' => 'إعداد التسويق بالبريد'],
                ['en' => 'Abandoned Cart Recovery', 'ar' => 'استعادة سلة التسوق'],
                ['en' => 'Inventory Management', 'ar' => 'إدارة المخزون'],
                ['en' => 'Analytics Dashboard', 'ar' => 'لوحة تحليلات'],
                ['en' => 'Upsell/Cross-sell Setup', 'ar' => 'إعداد البيع المتقاطع'],
                ['en' => 'Conversion Rate Optimization', 'ar' => 'تحسين معدل التحويل'],
                ['en' => 'Store Speed Optimization', 'ar' => 'تحسين سرعة المتجر'],
                ['en' => 'Multilingual Store Setup', 'ar' => 'إعداد متجر متعدد اللغات'],
                ['en' => 'Subscription Products', 'ar' => 'منتجات الاشتراك'],
                ['en' => 'Customer Chat Integration', 'ar' => 'تكامل محادثة العملاء'],
                ['en' => 'Dropshipping Configuration', 'ar' => 'إعداد دروبشيبينغ'],
            ],

            'Data' => [
                ['en' => 'Data Collection', 'ar' => 'جمع البيانات'],
                ['en' => 'Data Scraping', 'ar' => 'استخلاص البيانات'],
                ['en' => 'Data Cleaning', 'ar' => 'تنظيف البيانات'],
                ['en' => 'Excel Dashboard', 'ar' => 'لوحة Excel'],
                ['en' => 'SQL Query Writing', 'ar' => 'كتابة استعلامات SQL'],
                ['en' => 'Data Visualization', 'ar' => 'تصوير البيانات'],
                ['en' => 'Google Data Studio Setup', 'ar' => 'إعداد Google Data Studio'],
                ['en' => 'Python Data Scripts', 'ar' => 'سكريبتات بيانات Python'],
                ['en' => 'Data Entry Forms', 'ar' => 'نماذج إدخال البيانات'],
                ['en' => 'Database Design', 'ar' => 'تصميم قاعدة البيانات'],
                ['en' => 'Web Analytics Setup', 'ar' => 'إعداد تحليلات الويب'],
                ['en' => 'Machine Learning Models', 'ar' => 'نماذج تعلم الآلة'],
                ['en' => 'ETL Pipelines', 'ar' => 'خطوط معالجة ETL'],
                ['en' => 'Data Engineering', 'ar' => 'هندسة البيانات'],
                ['en' => 'Statistical Reports', 'ar' => 'تقارير إحصائية'],
                ['en' => 'Survey Data Analysis', 'ar' => 'تحليل بيانات الاستبيان'],
                ['en' => 'BigQuery Projects', 'ar' => 'مشاريع BigQuery'],
                ['en' => 'Data Mining', 'ar' => 'تنقيب البيانات'],
                ['en' => 'Custom API Reports', 'ar' => 'تقارير API مخصصة'],
                ['en' => 'Power BI Dashboards', 'ar' => 'لوحات Power BI'],
            ],

            'Writing & Translation' => [
                ['en' => 'Blog Articles', 'ar' => 'مقالات المدونة'],
                ['en' => 'Product Descriptions', 'ar' => 'وصف المنتجات'],
                ['en' => 'Technical Documentation', 'ar' => 'الوثائق التقنية'],
                ['en' => 'Proofreading', 'ar' => 'التدقيق اللغوي'],
                ['en' => 'Copywriting', 'ar' => 'كتابة الإعلانات'],
                ['en' => 'Press Release Writing', 'ar' => 'كتابة البيانات الصحفية'],
                ['en' => 'CV/Resume Writing', 'ar' => 'كتابة السيرة الذاتية'],
                ['en' => 'Cover Letters', 'ar' => 'خطابات التقديم'],
                ['en' => 'Email Writing', 'ar' => 'كتابة البريد الإلكتروني'],
                ['en' => 'Creative Story Writing', 'ar' => 'كتابة القصص الإبداعية'],
                ['en' => 'Translation (EN<>AR)', 'ar' => 'الترجمة (عربي <> إنجليزي)'],
                ['en' => 'Academic Writing', 'ar' => 'الكتابة الأكاديمية'],
                ['en' => 'eBook Writing', 'ar' => 'كتابة الكتب الإلكترونية'],
                ['en' => 'White Paper Writing', 'ar' => 'كتابة الأوراق البيضاء'],
                ['en' => 'User Manual Writing', 'ar' => 'كتابة كتيب المستخدم'],
                ['en' => 'Website Content', 'ar' => 'محتوى المواقع'],
                ['en' => 'Ad Copywriting', 'ar' => 'كتابة إعلانات'],
                ['en' => 'Meta Descriptions', 'ar' => 'وصف ميتا SEO'],
                ['en' => 'Script Writing', 'ar' => 'كتابة السيناريو'],
                ['en' => 'Transcription', 'ar' => 'النسخ'],
            ],

            'Music & Audio' => [
                ['en' => 'Voice Over', 'ar' => 'التعليق الصوتي'],
                ['en' => 'Audio Mixing', 'ar' => 'مزج الصوت'],
                ['en' => 'Audio Mastering', 'ar' => 'ماسترينغ الصوت'],
                ['en' => 'Podcast Editing', 'ar' => 'تحرير البودكاست'],
                ['en' => 'Background Music', 'ar' => 'الموسيقى الخلفية'],
                ['en' => 'Sound Effects', 'ar' => 'المؤثرات الصوتية'],
                ['en' => 'Intro/Outro Music', 'ar' => 'مقدمة ونهاية موسيقية'],
                ['en' => 'Music Composition', 'ar' => 'تأليف موسيقي'],
                ['en' => 'Instrumental Tracks', 'ar' => 'مقاطع موسيقية'],
                ['en' => 'DJ Drops', 'ar' => 'تعريف DJ'],
                ['en' => 'Vocal Tuning', 'ar' => 'ضبط الصوت'],
                ['en' => 'Audio Restoration', 'ar' => 'ترميم الصوت'],
                ['en' => 'Jingles', 'ar' => 'أغاني قصيرة'],
                ['en' => 'Karaoke Tracks', 'ar' => 'مسارات كاريوكي'],
                ['en' => 'Sound Branding', 'ar' => 'الهوية الصوتية'],
                ['en' => 'Studio Quality Output', 'ar' => 'إخراج بجودة الاستوديو'],
                ['en' => 'Loop Creation', 'ar' => 'إنشاء الحلقات'],
                ['en' => 'Music Sync', 'ar' => 'مزامنة الموسيقى'],
                ['en' => 'Script to Audio', 'ar' => 'تحويل النص إلى صوت'],
                ['en' => 'Audiobook Production', 'ar' => 'إنتاج الكتب الصوتية'],
            ]
        ];

        foreach ($featuresByCategory as $categoryTitle => $features) {
            $category = Category::where('title_en', $categoryTitle)->first();

            if (!$category) {
                echo "Category not found: $categoryTitle\n";
                continue;
            }

            foreach ($features as $feature) {
                Feature::updateOrCreate(
                    [
                        'category_id' => $category->id,
                        'title_en' => $feature['en'],
                    ],
                    [
                        'uuid' => Str::uuid(),
                        'title_ar' => $feature['ar'],
                    ]
                );
            }
        }
    }
}
