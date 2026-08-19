<?php

namespace App\Models;

use App\Enums\AttachmentStorageEnum;
use App\Traits\HasAttachment;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A class defines the system settings with relations
 */
class Setting extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasUuid;

    public const PAGE_RESULT_LIMIT = 15;
    public const PAGE = 1;
    public const ATTACHMENT_STORAGE = AttachmentStorageEnum::S3;
    public const SUPERVISOR_DEFAULT_PASSWORD = 'password123';

    protected $guarded = ['id', 'uuid'];

    /**
     * Define the relation with attachment
     *
     * @return MorphOne
     */
    public function attachment(): MorphOne
    {
        return $this->morphOne(Attachment::class, 'attachable');
    }

    /**
     * Adding load privacy policy
     *
     * @return string
     */
    public static function privacyPolicy(): string
    {
        $lang = app()->getLocale();

        if ($lang == 'ar') {
            return <<<TEXT
سياسة الخصوصية

خصوصيتك مهمة بالنسبة لنا. توضح سياسة الخصوصية هذه كيف نجمع معلوماتك الشخصية ونستخدمها ونحميها.

1. جمع المعلومات:
نقوم بجمع معلومات شخصية مثل اسمك وعنوان بريدك الإلكتروني وبيانات الاستخدام عند استخدامك لخدماتنا.

2. استخدام المعلومات:
نستخدم معلوماتك لتقديم خدماتنا وتحسينها، وللتواصل معك، وضمان الأمان.

3. مشاركة المعلومات:
لا نشارك معلوماتك الشخصية مع أطراف ثالثة إلا إذا تطلب القانون ذلك أو بموافقتك.

4. أمان البيانات:
نتخذ تدابير معقولة لحماية بياناتك من الوصول غير المصرح به أو التعديل أو الكشف.

5. حقوقك:
لك الحق في الوصول إلى معلوماتك الشخصية أو تحديثها أو حذفها.

6. التغييرات:
قد نقوم بتحديث سياسة الخصوصية من وقت لآخر، ونشجعك على مراجعتها بشكل دوري.

إذا كان لديك أي أسئلة حول هذه السياسة، يرجى الاتصال بنا.

تاريخ النفاذ: 17 يوليو 2025
TEXT;
        }
        return <<<TEXT
Privacy Policy

Your privacy is important to us. This privacy policy explains how we collect, use, and protect your personal information.

1. Information Collection:
We collect personal information such as your name, email address, and usage data when you use our services.

2. Use of Information:
We use your information to provide and improve our services, communicate with you, and ensure security.

3. Sharing of Information:
We do not share your personal information with third parties except as required by law or with your consent.

4. Data Security:
We take reasonable measures to protect your data from unauthorized access, alteration, or disclosure.

5. Your Rights:
You have the right to access, update, or delete your personal information.

6. Changes:
We may update this privacy policy from time to time. We encourage you to review it periodically.

If you have any questions about this policy, please contact us.

Effective Date: July 17, 2025
TEXT;
    }

}
