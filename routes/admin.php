<?php

use Illuminate\Support\Facades\Route;


Route::prefix('v1')->middleware('administrator')->group(function () {
    /**
     * Users Routes
     */
    require('Api/Admin/users_routes.php');

    /**
     * Permissions Routes
     */
    require('Api/Admin/permission_routes.php');

    /**
     * KYC Routes
     */
    require('Api/Admin/roles_routes.php');

    /**
     * KYC Routes
     */
    require('Api/Admin/kyc_routes.php');

    /**
     * Categories Routes
     */
    require('Api/Admin/categories_routes.php');

    /**
     * Sub Categories Routes
     */
    require('Api/Admin/sub_categories_routes.php');

    /**
     * Skills Routes
     */
    require('Api/Admin/skills_routes.php');

    /**
     * Countries Routes
     */
    require('Api/Admin/countries_routes.php');

    /**
     * Certificate Providers Routes
     */
    require('Api/Admin/certificates_providers_routes.php');

    /**
     * Certificates Routes
     */
    require('Api/Admin/certificates_routes.php');

    /**
     * Orders Routes
     */
    require('Api/Admin/orders_routes.php');

    /**
     * Reports Routes
     */
    require('Api/Admin/reports_routes.php');

    /**
     * Payment Requests Routes
     */
    require('Api/Admin/payment_requests_routes.php');

    /**
     * Wallets Routes
     */
    require('Api/Admin/wallet_routes.php');

    /**
     * Commissions Routes
     */
    require('Api/Admin/commissions_routes.php');

    /**
     * Refunds Routes
     */
    require('Api/Admin/refunds_routes.php');

    /**
     * Analysis Routes
     */
    require('Api/Admin/analysis_routes.php');

    /**
     * Commission Settings Routes
     */
    require('Api/Admin/commission_settings_routes.php');

    /**
     * Projects Routes
     */
    require('Api/Admin/projects_routes.php');

    /**
     * Services Routes
     */
    require('Api/Admin/services_routes.php');

    /**
     * Features Routes
     */
    require('Api/Admin/features_routes.php');

    /**
     * Portfolios Routes
     */
    require('Api/Admin/portfolio_routes.php');

    /**
     * Chats Routes
     */
    require('Api/Admin/chats_routes.php');

    /**
     * Settings Routes
     */
    require('Api/Admin/settings_routes.php');

    /**
     * Banners Routes
     */
    require('Api/Admin/banners_routes.php');

    /**
     * FAQs Routes
     */
    require('Api/Admin/faqs_routes.php');

    /**
     * Colors Routes
     */
    require('Api/Admin/colors_routes.php');
    
    /**
     * Pages Routes
     */
    require('Api/Admin/pages_routes.php');

    /**
     * Notification Settings Routes
     */
    require('Api/Admin/notification_settings_routes.php');

    /**
     * Dashboard Notifications Routes
     */
    require('Api/Admin/dashboard_notifications_routes.php');
});

