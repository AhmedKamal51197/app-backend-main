<?php

namespace Database\Seeders;

use App\Models\NotificationSetting;
use Illuminate\Database\Seeder;

class NotificationSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // Email Notifications
            [
                'setting_key' => 'email_notifications',
                'setting_name_en' => 'Email Notifications',
                'setting_name_ar' => 'الإشعارات عبر الإيميل',
                'description_en' => 'Enable/disable email notifications for external communications',
                'description_ar' => 'تفعيل أو إلغاء تفعيل الإشعارات الخارجية عبر البريد الإلكتروني والمعلومات العامة',
                'is_enabled' => true,
            ],

            // Internal Notifications
            [
                'setting_key' => 'internal_notifications',
                'setting_name_en' => 'Internal Notifications',
                'setting_name_ar' => 'الإشعارات الداخلية',
                'description_en' => 'Enable/disable internal app notifications and alerts',
                'description_ar' => 'تفعيل أو إلغاء تفعيل الإشعارات الداخلية لإرسال التنبيهات والمعلومات العامة',
                'is_enabled' => true,
            ],

            // Specific notification types
            [
                'setting_key' => 'withdrawal_requests',
                'setting_name_en' => 'Withdrawal Request Alerts',
                'setting_name_ar' => 'تنبيه طلبات السحب',
                'description_en' => 'Notifications for withdrawal requests',
                'description_ar' => 'إشعارات خاصة بطلبات سحب الأموال من المحفظة',
                'is_enabled' => true,
            ],
            [
                'setting_key' => 'wallet_credit',
                'setting_name_en' => 'Wallet Credit',
                'setting_name_ar' => 'بشحن محفظة',
                'description_en' => 'Notifications for wallet credit transactions',
                'description_ar' => 'إشعارات عمليات شحن المحفظة',
                'is_enabled' => true,
            ],
            [
                'setting_key' => 'disputed_orders',
                'setting_name_en' => 'Disputed Orders',
                'setting_name_ar' => 'طلب متنازع عليه',
                'description_en' => 'Notifications for disputed orders',
                'description_ar' => 'إشعارات الطلبات المتنازع عليها',
                'is_enabled' => true,
            ],
            [
                'setting_key' => 'completed_service',
                'setting_name_en' => 'Completed Service',
                'setting_name_ar' => 'خدمة مكتملة',
                'description_en' => 'Notifications when services are completed',
                'description_ar' => 'إشعارات إكمال الخدمات المطلوبة',
                'is_enabled' => true,
            ],
            [
                'setting_key' => 'service_waiting_approval',
                'setting_name_en' => 'Service Waiting Approval',
                'setting_name_ar' => 'خدمة بانتظار الموافقة',
                'description_en' => 'Notifications for services waiting approval',
                'description_ar' => 'إشعارات الخدمات التي تنتظر الموافقة من الإدارة',
                'is_enabled' => true,
            ],
            [
                'setting_key' => 'completed_project',
                'setting_name_en' => 'Completed Project',
                'setting_name_ar' => 'مشروع مكتمل',
                'description_en' => 'Notifications when projects are completed',
                'description_ar' => 'إشعارات إكمال المشاريع المطلوبة',
                'is_enabled' => true,
            ],
        ];

        foreach ($settings as $setting) {
            NotificationSetting::updateOrCreate(
                ['setting_key' => $setting['setting_key']],
                $setting
            );
        }
    }
}
