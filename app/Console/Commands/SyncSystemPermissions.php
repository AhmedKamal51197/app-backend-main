<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class SyncSystemPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync system permissions for the root user';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $permissions = [
            // Dashboard & Reports 
            "dashboard.view" => "صفحة لوحة التحكم",
            "reports.view" => "التقارير",
            "reports_received.view" => "الإبلاغات المستلمة",
            "reports_received.details" => "تفاصيل الإبلاغات المستلمة",

            // Projects المشاريع
            "projects.view" => "عرض المشاريع",
            "projects.details" => "تفاصيل المشاريع",

            // Services الخدمات
            "services.view" => "عرض الخدمات",
            "services.details" => "تفاصيل الخدمات",
            "services.orders" => "طلبات الخدمات",
            "Index Services" => "قائمة الخدمات",
            "Details Services" => "تفاصيل الخدمة",
            "Toggle Service Visibility" => "إظهار/إخفاء الخدمة",
            "Approve Services" => "اعتماد الخدمات",
            "Delete Services" => "حذف الخدمات",

            // Users المستخدمين
            "users.view" => "عرض المستخدمين",
            "users.client_details" => "تفاصيل العميل",
            "users.mostaql_details" => "تفاصيل المستقل",
            "users.wallet" => "محفظة المستخدم",
            "Index Users" => "قائمة المستخدمين",
            "Details Users" => "تفاصيل المستخدمين",
            "Toggle User Status" => "تفعيل/تعطيل المستخدم",
            "Index Wallets" => "قائمة المحافظ",
            "Index Payment Requests" => "قائمة طلبات الدفع",
            "Index Orders" => "قائمة الطلبات",
            "Index KYC" => "قائمة التوثيق",

            // Business الأعمال
            "business.view" => "عرض الأعمال",
            "business.details" => "تفاصيل الأعمال",

            // Money / Revenue الإيرادات والتقارير
            "money_revenue.view" => "عرض الإيرادات والتقارير",

            // Settings الإعدادات 
            "settings.general" => "الإعدادات العامة",
            "settings.sale_commission" => "عمولة المبيعات",
            "settings.alerts" => "التنبيهات",
            "settings.supervisors" => "المشرفين",
            "settings.supervisor_details" => "تفاصيل المشرف",
            "settings.content" => "المحتوى",
            "settings.faq" => "الأسئلة الشائعة",
            "settings.permissions" => "الصلاحيات",

            // Account إعدادات الحساب الشخصي
            "account.settings" => "إعدادات الحساب",
            "account.change_password" => "تغيير كلمة المرور",

            // Technical Support التذاكر 
            "tickets.view" => "عرض التذاكر",
            "tickets.details" => "تفاصيل التذاكر",

            // App Chat - Services  
            "app_chat.open_services" => "محادثات الخدمات المفتوحة",
            "app_chat.active_services" => "محادثات الخدمات النشطة",
            "app_chat.complete_services" => "محادثات الخدمات المكتملة",
            "app_chat.canceled_services" => "محادثات الخدمات الملغاة",
            "app_chat.canceled_services_order" => "محادثات طلبات الخدمات الملغاة",

            // App Chat - Projects
            "app_chat.open_projects" => "محادثات المشاريع المفتوحة",
            "app_chat.active_projects" => "محادثات المشاريع النشطة",
            "app_chat.complete_projects" => "محادثات المشاريع المكتملة",
            "app_chat.canceled_projects" => "محادثات المشاريع الملغاة",
            "app_chat.canceled_projects_order" => "محادثات طلبات المشاريع الملغاة",

            // App Chat - Direct
            "app_chat.direct_conversation" => "المحادثات المباشرة",

            // Dashboard Notifications
            "Index Notifications" => "عرض إشعارات لوحة التحكم",
            "Add Notifications" => "إضافة إشعارات لوحة التحكم",
            "Details Notifications" => "تفاصيل إشعارات لوحة التحكم",
            "Edit Notifications" => "تعديل إشعارات لوحة التحكم",
            "Delete Notifications" => "حذف إشعارات لوحة التحكم",
        ];

        $this->info('Starting permissions sync...');

        foreach ($permissions as $name => $nameAr) {
            Permission::updateOrCreate(
                ['name' => $name, 'guard_name' => 'api'],
                [
                    'uuid' => (string) Str::uuid(),
                    'name_ar' => $nameAr
                ]
            );
        }

        $rootRole = Role::where('name', 'root')->first();
        if ($rootRole) {
            $rootRole->givePermissionTo(array_keys($permissions));
            $this->info('Permissions assigned to root role.');
        } else {
            $this->error('Root role not found!');
        }

        $this->info('Permissions sync completed successfully.');

        return Command::SUCCESS;
    }
}
