<?php

namespace Database\Seeders;

use App\Enums\RoleAccessLevelEnum;
use App\Enums\RoleTypeEnum;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Create roles
        $provider = Role::updateOrCreate(
            ['name' => 'provider', 'guard_name' => 'api'],
            [
                'uuid' => Str::uuid(),
                'name_ar' => 'مقدم الخدمة',
                'allowed_user' => true,
                'is_active' => true,
                'description' => 'Service provider role',
                'type' => RoleTypeEnum::TECHNICAL->value,
            ]
        );
        $seeker = Role::updateOrCreate(
            ['name' => 'seeker', 'guard_name' => 'api'],
            [
                'uuid' => Str::uuid(),
                'name_ar' => 'طالب الخدمة',
                'allowed_user' => true,
                'is_active' => true,
                'description' => 'Service seeker role',
                'type' => RoleTypeEnum::VIEW_ONLY->value,
            ]
        );
        $root = Role::updateOrCreate(
            ['name' => 'root', 'guard_name' => 'api'],
            [
                'uuid' => Str::uuid(),
                'name_ar' => 'المدير العام',
                'allowed_user' => false,
                'is_active' => true,
                'description' => 'Root administrator with full access',
                'type' => RoleTypeEnum::ADMINISTRATIVE->value,
                'access_level' => RoleAccessLevelEnum::FULL_ACCESS->value,
            ]
        );

        // Create all permissions
        $permissions = [
            // Users Permissions
            'Index Users' => 'عرض المستخدمين',
            'Details Users' => 'تفاصيل المستخدمين',
            'Toggle User Status' => 'تفعيل/تعطيل المستخدمين',
            'Delete Users' => 'حذف المستخدمين',

            // KYC Permissions
            'Index KYC' => 'عرض توثيق الهوية',
            'Details KYC' => 'تفاصيل توثيق الهوية',
            'Approve KYC' => 'قبول توثيق الهوية',
            'Reject KYC' => 'رفض توثيق الهوية',
            'Delete KYC' => 'حذف توثيق الهوية',

            // Orders Permissions
            'Index Orders' => 'عرض الطلبات',
            'Details Orders' => 'تفاصيل الطلبات',
            'Cancel Orders' => 'إلغاء الطلبات',
            'Refund Orders' => 'استرداد الطلبات',
            'Approve Order Cancellation' => 'قبول إلغاء الطلب',
            'Reject Order Cancellation' => 'رفض إلغاء الطلب',
            'Delete Orders' => 'حذف الطلبات',

            // Projects Permissions
            'Index Projects' => 'عرض المشاريع',
            'Details Projects' => 'تفاصيل المشاريع',
            'Approve Projects' => 'قبول المشاريع',
            'Reject Projects' => 'رفض المشاريع',
            'Approve Project Cancellation' => 'قبول إلغاء المشروع',
            'Reject Project Cancellation' => 'رفض إلغاء المشروع',

            // Services Permissions
            'Index Services' => 'عرض الخدمات',
            'Details Services' => 'تفاصيل الخدمات',
            'Toggle Service Visibility' => 'إخفاء/إظهار الخدمات',
            'Approve Services' => 'اعتماد الخدمات',
            'Delete Services' => 'حذف الخدمات',

            // Portfolios Permissions
            'Index Portfolios' => 'عرض الأعمال',
            'Details Portfolios' => 'تفاصيل الأعمال',
            'Toggle Portfolio Visibility' => 'إخفاء/إظهار الأعمال',
            'Delete Portfolios' => 'حذف الأعمال',

            // Wallets Permissions
            'Index Wallets' => 'عرض المحافظ',
            'Details Wallets' => 'تفاصيل المحافظ',

            // Payment Requests Permissions
            'Index Payment Requests' => 'عرض طلبات الدفع',
            'Details Payment Requests' => 'تفاصيل طلبات الدفع',
            'Approve Payment Requests' => 'قبول طلبات الدفع',
            'Reject Payment Requests' => 'رفض طلبات الدفع',

            // Commissions Permissions
            'Index Commissions' => 'عرض العمولات',
            'Details Commissions' => 'تفاصيل العمولات',

            // Reports Permissions
            'Index Reports' => 'عرض التقارير',
            'Details Reports' => 'تفاصيل التقارير',
            'Add Reports' => 'إضافة التقارير',
            'Toggle Report Status' => 'تغيير حالة التقرير',
            'Delete Reports' => 'حذف التقارير',
            'Respond To Reports' => 'الرد على التقارير',

            // Chats Permissions
            'Index Chats' => 'عرض المحادثات',
            'Details Chats' => 'تفاصيل المحادثات',
            'Open Chats' => 'فتح المحادثات',
            'Send Chat Messages' => 'إرسال رسائل المحادثة',

            // Refunds Permissions
            'Index Refunds' => 'عرض المبالغ المستردة',
            'Details Refunds' => 'تفاصيل المبالغ المستردة',

            // Commission Settings Permissions
            'Index Commission Settings' => 'عرض إعدادات العمولة',
            'Details Commission Settings' => 'تفاصيل إعدادات العمولة',
            'Edit Commission Settings' => 'تعديل إعدادات العمولة',
            'Delete Commission Settings' => 'حذف إعدادات العمولة',

            // Banners Permissions
            'Index Banners' => 'عرض البانرات',
            'Details Banners' => 'تفاصيل البانرات',
            'Add Banners' => 'إضافة البانرات',
            'Edit Banners' => 'تعديل البانرات',
            'Delete Banners' => 'حذف البانرات',
            'Toggle Banner Status' => 'تفعيل/تعطيل البانرات',

            // FAQs Permissions
            'Index FAQs' => 'عرض الأسئلة الشائعة',
            'Details FAQs' => 'تفاصيل الأسئلة الشائعة',
            'Add FAQs' => 'إضافة الأسئلة الشائعة',
            'Edit FAQs' => 'تعديل الأسئلة الشائعة',
            'Delete FAQs' => 'حذف الأسئلة الشائعة',
            'Toggle FAQ Status' => 'تفعيل/تعطيل الأسئلة الشائعة',

            // Pages Permissions
            'Index Pages' => 'عرض الصفحات',
            'Details Pages' => 'تفاصيل الصفحات',
            'Edit Pages' => 'تعديل الصفحات',

            // Notification Settings Permissions
            'Index Notification Settings' => 'عرض إعدادات الإشعارات',
            'Edit Notification Settings' => 'تعديل إعدادات الإشعارات',

            // Roles Permissions
            'Index Roles' => 'عرض الأدوار',
            'Details Roles' => 'تفاصيل الأدوار',
            'Edit Roles' => 'تعديل الأدوار',
            'Delete Roles' => 'حذف الأدوار',
            'Add Roles' => 'إضافة الأدوار',
            'Add Roles Permissions' => 'إضافة صلاحيات الأدوار',
            'Delete Roles Permissions' => 'حذف صلاحيات الأدوار',

            // Permissions Permissions
            'Index Permissions' => 'عرض الصلاحيات',
            'Details Permissions' => 'تفاصيل الصلاحيات',
            'Edit Permissions' => 'تعديل الصلاحيات',
            'Delete Permissions' => 'حذف الصلاحيات',
            'Add Permissions' => 'إضافة الصلاحيات',
            
            // New Permissions
            "dashboard.view" => "صفحة لوحة التحكم",
            "reports.view" => "التقارير",
            "reports_received.view" => "الإبلاغات المستلمة",
            "reports_received.details" => "تفاصيل الإبلاغات المستلمة",
            "projects.view" => "عرض المشاريع",
            "projects.details" => "تفاصيل المشاريع",
            "services.view" => "عرض الخدمات",
            "services.details" => "تفاصيل الخدمات",
            "services.orders" => "طلبات الخدمات",
            "users.view" => "عرض المستخدمين",
            "users.client_details" => "تفاصيل العميل",
            "users.mostaql_details" => "تفاصيل المستقل",
            "users.wallet" => "محفظة المستخدم",
            "business.view" => "عرض الأعمال",
            "business.details" => "تفاصيل الأعمال",
            "money_revenue.view" => "عرض الإيرادات والتقارير",
            "settings.general" => "الإعدادات العامة",
            "settings.sale_commission" => "عمولة المبيعات",
            "settings.alerts" => "التنبيهات",
            "settings.supervisors" => "المشرفين",
            "settings.supervisor_details" => "تفاصيل المشرف",
            "settings.content" => "المحتوى",
            "settings.faq" => "الأسئلة الشائعة",
            "settings.permissions" => "الصلاحيات",
            "account.settings" => "إعدادات الحساب",
            "account.change_password" => "تغيير كلمة المرور",
            "tickets.view" => "عرض التذاكر",
            "tickets.details" => "تفاصيل التذاكر",
            "app_chat.open_services" => "محادثات الخدمات المفتوحة",
            "app_chat.active_services" => "محادثات الخدمات النشطة",
            "app_chat.complete_services" => "محادثات الخدمات المكتملة",
            "app_chat.canceled_services" => "محادثات الخدمات الملغاة",
            "app_chat.canceled_services_order" => "محادثات طلبات الخدمات الملغاة",
            "app_chat.open_projects" => "محادثات المشاريع المفتوحة",
            "app_chat.active_projects" => "محادثات المشاريع النشطة",
            "app_chat.complete_projects" => "محادثات المشاريع المكتملة",
            "app_chat.canceled_projects" => "محادثات المشاريع الملغاة",
            "app_chat.canceled_projects_order" => "محادثات طلبات المشاريع الملغاة",
            "app_chat.direct_conversation" => "المحادثات المباشرة",

            // Dashboard Notifications
            "Index Notifications" => "عرض إشعارات لوحة التحكم",
            "Add Notifications" => "إضافة إشعارات لوحة التحكم",
            "Details Notifications" => "تفاصيل إشعارات لوحة التحكم",
            "Edit Notifications" => "تعديل إشعارات لوحة التحكم",
            "Delete Notifications" => "حذف إشعارات لوحة التحكم",
        ];

        // Create permissions
        $createdPermissions = [];
        foreach ($permissions as $permissionName => $permissionNameAr) {
            $permission = Permission::updateOrCreate(
                ['name' => $permissionName, 'guard_name' => 'api'],
                ['uuid' => Str::uuid(), 'name_ar' => $permissionNameAr]
            );
            $createdPermissions[$permissionName] = $permission;
        }

        // Assign all permissions to root role
        $allPermissionNames = array_keys($permissions);
        $root->givePermissionTo($allPermissionNames);
    }
}
