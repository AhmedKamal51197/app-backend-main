<?php

use Illuminate\Support\Facades\Route;


Route::prefix('v1')->middleware('administrator')->group(function () {
    /**
     * Users Routes
     */
    require_once('Api/Admin/users_routes.php');

    /**
     * Permissions Routes
     */
    require_once('Api/Admin/permission_routes.php');

    /**
     * KYC Routes
     */
    require_once('Api/Admin/roles_routes.php');

    /**
     * KYC Routes
     */
    require_once('Api/Admin/kyc_routes.php');

    /**
     * Categories Routes
     */
    require_once('Api/Admin/categories_routes.php');

    /**
     * Sub Categories Routes
     */
    require_once('Api/Admin/sub_categories_routes.php');

    /**
     * Skills Routes
     */
    require_once('Api/Admin/skills_routes.php');

    /**
     * Countries Routes
     */
    require_once('Api/Admin/countries_routes.php');

    /**
     * Certificate Providers Routes
     */
    require_once('Api/Admin/certificates_providers_routes.php');

    /**
     * Certificates Routes
     */
    require_once('Api/Admin/certificates_routes.php');

    /**
     * Orders Routes
     */
    require_once('Api/Admin/orders_routes.php');

    /**
     * Reports Routes
     */
    require_once('Api/Admin/reports_routes.php');

    /**
     * Payment Requests Routes
     */
    require_once('Api/Admin/payment_requests_routes.php');

    /**
     * Wallets Routes
     */
    require_once('Api/Admin/wallet_routes.php');

    /**
     * Commissions Routes
     */
    require_once('Api/Admin/commissions_routes.php');

    /**
     * Refunds Routes
     */
    require_once('Api/Admin/refunds_routes.php');

    /**
     * Analysis Routes
     */
    require_once('Api/Admin/analysis_routes.php');

    /**
     * Commission Settings Routes
     */
    require_once('Api/Admin/commission_settings_routes.php');

    /**
     * Projects Routes
     */
    require_once('Api/Admin/projects_routes.php');

    /**
     * Services Routes
     */
    require_once('Api/Admin/services_routes.php');

    /**
     * Features Routes
     */
    require_once('Api/Admin/features_routes.php');

    /**
     * Portfolios Routes
     */
    require_once('Api/Admin/portfolio_routes.php');

    /**
     * Chats Routes
     */
    require_once('Api/Admin/chats_routes.php');

    /**
     * Settings Routes
     */
    require_once('Api/Admin/settings_routes.php');

    /**
     * Banners Routes
     */
    require_once('Api/Admin/banners_routes.php');

    /**
     * FAQs Routes
     */
    require_once('Api/Admin/faqs_routes.php');

    /**
     * Colors Routes
     */
    require_once('Api/Admin/colors_routes.php');
    
    /**
     * Pages Routes
     */
    require_once('Api/Admin/pages_routes.php');

    /**
     * Notification Settings Routes
     */
    require_once('Api/Admin/notification_settings_routes.php');

    /**
     * Dashboard Notifications Routes
     */
    require_once('Api/Admin/dashboard_notifications_routes.php');
});

