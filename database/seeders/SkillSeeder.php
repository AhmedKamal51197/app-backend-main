<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Skill;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SkillSeeder extends Seeder
{
    public function run(): void
    {
        $skillsByCategory = [
            'Graphics & Design' => [
                ['title_en' => 'Logo Design', 'title_ar' => 'تصميم الشعارات'],
                ['title_en' => 'Brand Identity', 'title_ar' => 'الهوية البصرية'],
                ['title_en' => 'Business Card Design', 'title_ar' => 'تصميم بطاقة العمل'],
                ['title_en' => 'Flyer Design', 'title_ar' => 'تصميم المنشورات'],
                ['title_en' => 'Brochure Design', 'title_ar' => 'تصميم الكتيبات'],
                ['title_en' => 'Social Media Post Design', 'title_ar' => 'تصميم منشورات التواصل الاجتماعي'],
                ['title_en' => 'Infographic Design', 'title_ar' => 'تصميم الإنفوجرافيك'],
                ['title_en' => 'UI Design', 'title_ar' => 'تصميم واجهة المستخدم'],
                ['title_en' => 'UX Design', 'title_ar' => 'تصميم تجربة المستخدم'],
                ['title_en' => 'Web Design', 'title_ar' => 'تصميم المواقع'],
                ['title_en' => 'Mobile App Design', 'title_ar' => 'تصميم التطبيقات'],
                ['title_en' => '3D Design', 'title_ar' => 'تصميم ثلاثي الأبعاد'],
                ['title_en' => '3D Rendering', 'title_ar' => 'الإظهار الثلاثي الأبعاد'],
                ['title_en' => 'T-shirt Design', 'title_ar' => 'تصميم التيشيرتات'],
                ['title_en' => 'Packaging Design', 'title_ar' => 'تصميم العبوات'],
                ['title_en' => 'Label Design', 'title_ar' => 'تصميم الملصقات'],
                ['title_en' => 'Illustration', 'title_ar' => 'الرسم التوضيحي'],
                ['title_en' => 'Typography Design', 'title_ar' => 'تصميم الخطوط'],
                ['title_en' => 'Photo Editing', 'title_ar' => 'تحرير الصور'],
                ['title_en' => 'Mockup Design', 'title_ar' => 'تصميم النماذج'],
            ],

            'Digital Marketing' => [
                ['title_en' => 'Facebook Ads', 'title_ar' => 'إعلانات فيسبوك'],
                ['title_en' => 'Instagram Marketing', 'title_ar' => 'تسويق إنستغرام'],
                ['title_en' => 'LinkedIn Ads', 'title_ar' => 'إعلانات لينكدإن'],
                ['title_en' => 'SEO Audit', 'title_ar' => 'تدقيق SEO'],
                ['title_en' => 'Google Ads', 'title_ar' => 'إعلانات جوجل'],
                ['title_en' => 'Keyword Research', 'title_ar' => 'البحث عن الكلمات المفتاحية'],
                ['title_en' => 'Content Strategy', 'title_ar' => 'استراتيجية المحتوى'],
                ['title_en' => 'Email Automation', 'title_ar' => 'أتمتة البريد الإلكتروني'],
                ['title_en' => 'Marketing Funnel', 'title_ar' => 'قُمع التسويق'],
                ['title_en' => 'Conversion Rate Optimization', 'title_ar' => 'تحسين معدل التحويل'],
                ['title_en' => 'Retargeting Ads', 'title_ar' => 'إعلانات إعادة الاستهداف'],
                ['title_en' => 'Influencer Outreach', 'title_ar' => 'التعاون مع المؤثرين'],
                ['title_en' => 'PR Campaigns', 'title_ar' => 'حملات العلاقات العامة'],
                ['title_en' => 'YouTube Marketing', 'title_ar' => 'تسويق اليوتيوب'],
                ['title_en' => 'Analytics Tracking', 'title_ar' => 'تتبع التحليلات'],
                ['title_en' => 'Social Media Strategy', 'title_ar' => 'استراتيجية التواصل الاجتماعي'],
                ['title_en' => 'A/B Testing', 'title_ar' => 'اختبارات A/B'],
                ['title_en' => 'Affiliate Setup', 'title_ar' => 'إعداد الشراكات التسويقية'],
                ['title_en' => 'CRM Setup', 'title_ar' => 'إعداد CRM'],
                ['title_en' => 'Marketing Automation', 'title_ar' => 'أتمتة التسويق'],
            ],

            'Video & Animation' => [
                ['title_en' => 'Video Editing', 'title_ar' => 'تحرير الفيديو'],
                ['title_en' => 'Motion Graphics', 'title_ar' => 'الرسوم المتحركة'],
                ['title_en' => '2D Animation', 'title_ar' => 'الرسوم المتحركة ثنائية الأبعاد'],
                ['title_en' => '3D Animation', 'title_ar' => 'الرسوم المتحركة ثلاثية الأبعاد'],
                ['title_en' => 'Whiteboard Animation', 'title_ar' => 'الرسوم التوضيحية على السبورة'],
                ['title_en' => 'Explainer Video', 'title_ar' => 'فيديو توضيحي'],
                ['title_en' => 'Social Media Reels', 'title_ar' => 'ريلز وسائل التواصل'],
                ['title_en' => 'VFX', 'title_ar' => 'المؤثرات البصرية'],
                ['title_en' => 'Video Ads', 'title_ar' => 'إعلانات الفيديو'],
                ['title_en' => 'Product Demos', 'title_ar' => 'عروض المنتجات'],
                ['title_en' => 'Kinetic Typography', 'title_ar' => 'الطباعة الحركية'],
                ['title_en' => 'Video Scripting', 'title_ar' => 'كتابة نصوص الفيديو'],
                ['title_en' => 'Cinematic Editing', 'title_ar' => 'التحرير السينمائي'],
                ['title_en' => 'Voice Syncing', 'title_ar' => 'مزامنة الصوت'],
                ['title_en' => 'Color Correction', 'title_ar' => 'تصحيح الألوان'],
                ['title_en' => 'Subtitling', 'title_ar' => 'إضافة الترجمة'],
                ['title_en' => 'YouTube Intros', 'title_ar' => 'مقدمات يوتيوب'],
                ['title_en' => 'Logo Animation', 'title_ar' => 'تحريك الشعارات'],
                ['title_en' => 'Screencasting', 'title_ar' => 'تسجيل الشاشة'],
                ['title_en' => 'E-commerce Product Videos', 'title_ar' => 'فيديوهات منتجات التجارة الإلكترونية'],
            ],

            'Programming & Tech' => [
                ['title_en' => 'Laravel', 'title_ar' => 'لارافيل'],
                ['title_en' => 'Node.js', 'title_ar' => 'نود جي إس'],
                ['title_en' => 'React Native', 'title_ar' => 'ريآكت نيتيف'],
                ['title_en' => 'Flutter', 'title_ar' => 'فلاتر'],
                ['title_en' => 'Next.js', 'title_ar' => 'نيكست جي إس'],
                ['title_en' => 'Docker', 'title_ar' => 'دوكر'],
                ['title_en' => 'AWS', 'title_ar' => 'خدمات أمازون السحابية'],
                ['title_en' => 'Firebase', 'title_ar' => 'فايربيس'],
                ['title_en' => 'GraphQL', 'title_ar' => 'جراف كيو إل'],
                ['title_en' => 'REST API', 'title_ar' => 'واجهات برمجة التطبيقات REST'],
                ['title_en' => 'PostgreSQL', 'title_ar' => 'بوستجري'],
                ['title_en' => 'MySQL', 'title_ar' => 'ماي إس كيو إل'],
                ['title_en' => 'MongoDB', 'title_ar' => 'مونغو دي بي'],
                ['title_en' => 'CI/CD Pipelines', 'title_ar' => 'خطوط تكامل وتسليم مستمر'],
                ['title_en' => 'Cybersecurity', 'title_ar' => 'الأمن السيبراني'],
                ['title_en' => 'Web Scraping', 'title_ar' => 'جمع البيانات من المواقع'],
                ['title_en' => 'DevOps', 'title_ar' => 'ديف أوبس'],
                ['title_en' => 'AI Chatbots', 'title_ar' => 'روبوتات الدردشة بالذكاء الاصطناعي'],
                ['title_en' => 'Machine Learning', 'title_ar' => 'تعلم الآلة'],
                ['title_en' => 'IoT Development', 'title_ar' => 'تطوير إنترنت الأشياء'],
            ],

            // Add these to the $skillsByCategory array inside SkillSeeder.php

            'Business Consulting' => [
                ['title_en' => 'Market Research', 'title_ar' => 'بحث السوق'],
                ['title_en' => 'Business Plans', 'title_ar' => 'خطط الأعمال'],
                ['title_en' => 'Product Management', 'title_ar' => 'إدارة المنتج'],
                ['title_en' => 'Operations Consulting', 'title_ar' => 'استشارات العمليات'],
                ['title_en' => 'Sales Consulting', 'title_ar' => 'استشارات المبيعات'],
                ['title_en' => 'Pricing Strategies', 'title_ar' => 'استراتيجيات التسعير'],
                ['title_en' => 'Customer Experience', 'title_ar' => 'تجربة العملاء'],
                ['title_en' => 'Brand Positioning', 'title_ar' => 'تموضع العلامة التجارية'],
                ['title_en' => 'Virtual Assistant Services', 'title_ar' => 'خدمات المساعد الافتراضي'],
                ['title_en' => 'E-commerce Consulting', 'title_ar' => 'استشارات التجارة الإلكترونية'],
                ['title_en' => 'Project Management', 'title_ar' => 'إدارة المشاريع'],
                ['title_en' => 'Change Management', 'title_ar' => 'إدارة التغيير'],
                ['title_en' => 'Leadership Coaching', 'title_ar' => 'تدريب القيادة'],
                ['title_en' => 'Business Process Improvement', 'title_ar' => 'تحسين عمليات الأعمال'],
                ['title_en' => 'SWOT Analysis', 'title_ar' => 'تحليل سوات'],
                ['title_en' => 'Competitive Analysis', 'title_ar' => 'تحليل المنافسين'],
                ['title_en' => 'Risk Management', 'title_ar' => 'إدارة المخاطر'],
                ['title_en' => 'Strategic Planning', 'title_ar' => 'التخطيط الاستراتيجي'],
                ['title_en' => 'KPI Development', 'title_ar' => 'تطوير مؤشرات الأداء الرئيسية'],
                ['title_en' => 'Business Model Innovation', 'title_ar' => 'ابتكار نموذج الأعمال'],
            ],

            'Finance Consulting' => [
                ['title_en' => 'Accounting Services', 'title_ar' => 'خدمات المحاسبة'],
                ['title_en' => 'Financial Planning', 'title_ar' => 'التخطيط المالي'],
                ['title_en' => 'Budgeting', 'title_ar' => 'إعداد الميزانية'],
                ['title_en' => 'Tax Planning', 'title_ar' => 'التخطيط الضريبي'],
                ['title_en' => 'Investment Analysis', 'title_ar' => 'تحليل الاستثمار'],
                ['title_en' => 'Fundraising', 'title_ar' => 'جمع الأموال'],
                ['title_en' => 'Cash Flow Management', 'title_ar' => 'إدارة التدفق النقدي'],
                ['title_en' => 'Risk Assessment', 'title_ar' => 'تقييم المخاطر'],
                ['title_en' => 'Financial Reporting', 'title_ar' => 'التقارير المالية'],
                ['title_en' => 'Payroll Management', 'title_ar' => 'إدارة الرواتب'],
                ['title_en' => 'Audit Preparation', 'title_ar' => 'تحضير التدقيق'],
                ['title_en' => 'Debt Management', 'title_ar' => 'إدارة الديون'],
                ['title_en' => 'Mergers & Acquisitions', 'title_ar' => 'الاندماجات والاستحواذات'],
                ['title_en' => 'Financial Modeling', 'title_ar' => 'النمذجة المالية'],
                ['title_en' => 'Compliance Management', 'title_ar' => 'إدارة الامتثال'],
                ['title_en' => 'Expense Analysis', 'title_ar' => 'تحليل المصروفات'],
                ['title_en' => 'Portfolio Management', 'title_ar' => 'إدارة المحافظ الاستثمارية'],
                ['title_en' => 'Corporate Finance', 'title_ar' => 'المالية الشركاتية'],
                ['title_en' => 'Tax Compliance', 'title_ar' => 'الامتثال الضريبي'],
                ['title_en' => 'Strategic Finance', 'title_ar' => 'المالية الاستراتيجية'],
            ],

            'E-Commerce' => [
                ['title_en' => 'Shopify Development', 'title_ar' => 'تطوير شوبيفاي'],
                ['title_en' => 'WooCommerce Development', 'title_ar' => 'تطوير ووكومرس'],
                ['title_en' => 'Product Listing Optimization', 'title_ar' => 'تحسين قوائم المنتجات'],
                ['title_en' => 'Amazon FBA', 'title_ar' => 'أمازون FBA'],
                ['title_en' => 'Dropshipping Support', 'title_ar' => 'دعم الدروبشيبينغ'],
                ['title_en' => 'Payment Gateway Integration', 'title_ar' => 'دمج بوابات الدفع'],
                ['title_en' => 'E-commerce SEO', 'title_ar' => 'SEO التجارة الإلكترونية'],
                ['title_en' => 'Store Management', 'title_ar' => 'إدارة المتجر'],
                ['title_en' => 'Inventory Management', 'title_ar' => 'إدارة المخزون'],
                ['title_en' => 'Customer Service', 'title_ar' => 'خدمة العملاء'],
                ['title_en' => 'Order Fulfillment', 'title_ar' => 'تنفيذ الطلبات'],
                ['title_en' => 'Product Photography', 'title_ar' => 'تصوير المنتجات'],
                ['title_en' => 'Affiliate Marketing', 'title_ar' => 'التسويق بالعمولة'],
                ['title_en' => 'Email Marketing', 'title_ar' => 'التسويق عبر البريد الإلكتروني'],
                ['title_en' => 'Social Media Advertising', 'title_ar' => 'الإعلان عبر وسائل التواصل'],
                ['title_en' => 'Marketplace Management', 'title_ar' => 'إدارة الأسواق الإلكترونية'],
                ['title_en' => 'Conversion Rate Optimization', 'title_ar' => 'تحسين معدل التحويل'],
                ['title_en' => 'UX for E-commerce', 'title_ar' => 'تجربة المستخدم للتجارة الإلكترونية'],
                ['title_en' => 'Analytics & Reporting', 'title_ar' => 'التحليلات والتقارير'],
                ['title_en' => 'Mobile Commerce', 'title_ar' => 'التجارة عبر الجوال'],
            ],

            'Data' => [
                ['title_en' => 'Data Entry', 'title_ar' => 'إدخال البيانات'],
                ['title_en' => 'Data Scraping', 'title_ar' => 'استخلاص البيانات'],
                ['title_en' => 'Data Visualization', 'title_ar' => 'تصوير البيانات'],
                ['title_en' => 'Data Analytics', 'title_ar' => 'تحليل البيانات'],
                ['title_en' => 'Machine Learning', 'title_ar' => 'تعلم الآلة'],
                ['title_en' => 'Data Engineering', 'title_ar' => 'هندسة البيانات'],
                ['title_en' => 'Database Management', 'title_ar' => 'إدارة قواعد البيانات'],
                ['title_en' => 'Big Data', 'title_ar' => 'البيانات الضخمة'],
                ['title_en' => 'Statistical Analysis', 'title_ar' => 'التحليل الإحصائي'],
                ['title_en' => 'Predictive Modeling', 'title_ar' => 'النمذجة التنبؤية'],
                ['title_en' => 'Business Intelligence', 'title_ar' => 'ذكاء الأعمال'],
                ['title_en' => 'SQL', 'title_ar' => 'لغة الاستعلام الهيكلية'],
                ['title_en' => 'Python for Data Science', 'title_ar' => 'بايثون لعلوم البيانات'],
                ['title_en' => 'R Programming', 'title_ar' => 'برمجة R'],
                ['title_en' => 'Excel Advanced', 'title_ar' => 'إكسل متقدم'],
                ['title_en' => 'Data Cleaning', 'title_ar' => 'تنظيف البيانات'],
                ['title_en' => 'Web Analytics', 'title_ar' => 'تحليلات الويب'],
                ['title_en' => 'Dashboard Development', 'title_ar' => 'تطوير لوحات التحكم'],
                ['title_en' => 'Data Governance', 'title_ar' => 'حوكمة البيانات'],
                ['title_en' => 'Data Warehousing', 'title_ar' => 'تخزين البيانات'],
            ],

            'Writing & Translation' => [
                ['title_en' => 'Article Writing', 'title_ar' => 'كتابة المقالات'],
                ['title_en' => 'Blog Writing', 'title_ar' => 'كتابة المدونات'],
                ['title_en' => 'Copywriting', 'title_ar' => 'الكتابة الإعلانية'],
                ['title_en' => 'Technical Writing', 'title_ar' => 'الكتابة التقنية'],
                ['title_en' => 'Proofreading', 'title_ar' => 'تصحيح النصوص'],
                ['title_en' => 'Editing', 'title_ar' => 'تحرير النصوص'],
                ['title_en' => 'Translation', 'title_ar' => 'الترجمة'],
                ['title_en' => 'Transcription', 'title_ar' => 'نسخ النصوص'],
                ['title_en' => 'Resume Writing', 'title_ar' => 'كتابة السيرة الذاتية'],
                ['title_en' => 'Cover Letter Writing', 'title_ar' => 'كتابة خطاب التقديم'],
                ['title_en' => 'Scriptwriting', 'title_ar' => 'كتابة السيناريو'],
                ['title_en' => 'SEO Writing', 'title_ar' => 'كتابة SEO'],
                ['title_en' => 'Ghostwriting', 'title_ar' => 'الكتابة بالوكالة'],
                ['title_en' => 'Academic Writing', 'title_ar' => 'الكتابة الأكاديمية'],
                ['title_en' => 'Creative Writing', 'title_ar' => 'الكتابة الإبداعية'],
                ['title_en' => 'Business Writing', 'title_ar' => 'الكتابة التجارية'],
                ['title_en' => 'Localization', 'title_ar' => 'التعريب'],
                ['title_en' => 'Grant Writing', 'title_ar' => 'كتابة المنح'],
                ['title_en' => 'Press Releases', 'title_ar' => 'البيانات الصحفية'],
                ['title_en' => 'Content Strategy', 'title_ar' => 'استراتيجية المحتوى'],
            ],

            'Music & Audio' => [
                ['title_en' => 'Voice Over', 'title_ar' => 'التعليق الصوتي'],
                ['title_en' => 'Podcast Editing', 'title_ar' => 'تحرير البودكاست'],
                ['title_en' => 'Audio Mixing', 'title_ar' => 'مزج الصوت'],
                ['title_en' => 'Sound Design', 'title_ar' => 'تصميم الصوت'],
                ['title_en' => 'Music Production', 'title_ar' => 'إنتاج الموسيقى'],
                ['title_en' => 'Audio Mastering', 'title_ar' => 'الماسترينغ الصوتي'],
                ['title_en' => 'Jingles', 'title_ar' => 'الأغاني الإعلانية القصيرة'],
                ['title_en' => 'Background Music', 'title_ar' => 'الموسيقى الخلفية'],
                ['title_en' => 'Audiobook Narration', 'title_ar' => 'سرد الكتب الصوتية'],
                ['title_en' => 'Character Voices', 'title_ar' => 'أصوات الشخصيات'],
                ['title_en' => 'Music Composition', 'title_ar' => 'تأليف الموسيقى'],
                ['title_en' => 'Live Sound Engineering', 'title_ar' => 'هندسة الصوت المباشر'],
                ['title_en' => 'Foley Artistry', 'title_ar' => 'فن المؤثرات الصوتية'],
                ['title_en' => 'Sound Editing', 'title_ar' => 'تحرير الصوت'],
                ['title_en' => 'Audio Restoration', 'title_ar' => 'استعادة الصوت'],
                ['title_en' => 'Voice Acting', 'title_ar' => 'التمثيل الصوتي'],
                ['title_en' => 'Mixing & Mastering', 'title_ar' => 'المزج والماسترينغ'],
                ['title_en' => 'Music Arrangement', 'title_ar' => 'ترتيب الموسيقى'],
                ['title_en' => 'Soundtrack Creation', 'title_ar' => 'إنشاء الموسيقى التصويرية'],
                ['title_en' => 'Radio Production', 'title_ar' => 'إنتاج الراديو'],
            ],

        ];

        foreach ($skillsByCategory as $categoryTitle => $skills) {
            $category = Category::where('title_en', $categoryTitle)->first();

            if (!$category) continue;

            foreach ($skills as $skill) {
                Skill::updateOrCreate(
                    [
                        'title_en' => $skill['title_en'],
                        'category_id' => $category->id,
                    ],
                    [
                        'uuid' => Str::uuid(),
                        'title_ar' => $skill['title_ar'],
                    ]
                );
            }
        }
    }
}
