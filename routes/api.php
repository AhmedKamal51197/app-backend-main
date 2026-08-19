<?php

use Illuminate\Support\Facades\Route;

/**
 * API version 1.0
 */
Route::prefix('v1')->group(function () {
    /**
     * KYC Routes
     */
    require_once('Api/User/kyc_routes.php');

    /**
     * User Routes
     */
    require_once('Api/User/user_routes.php');

    /**
     * Country Routes
     */
    require_once('Api/User/countries_routes.php');

    /**
     * Category Routes
     */
    require_once('Api/User/categories_routes.php');

    /**
     * Sub Category Routes
     */
    require_once('Api/User/sub_categories_routes.php');

    /**
     * Skills Routes
     */
    require_once('Api/User/skills_routes.php');

    /**
     *  Wallet Routes
     */
    require_once('Api/User/wallet_routes.php');

    /**
     * Certificate providers Routes
     */
    require_once('Api/User/certificate_providers_routes.php');

    /**
     * Bank account Routes
     */
    require_once('Api/User/bank_account_routes.php');

    /**
     * PayPal Routes
     */
    require_once('Api/User/paypal_routes.php');

    /**
     *
     * Educations Routes
     */
    require_once('Api/User/educations_routes.php');

    /**
     * Experiences Routes
     */
    require_once('Api/User/experiences_routes.php');

    /**
     * Portfolios Routes
     */
    require_once('Api/User/portfolios_routes.php');

    /**
     * Services Routes
     */
    require_once('Api/User/services_routes.php');

    /**
     * Jobs Routes
     */
    require_once('Api/User/jobs_routes.php');

    /**
     * Chats Routes
     */
    require_once('Api/User/chats_routes.php');

    /**
     * Payment Requests Routes
     */
    require_once('Api/User/payment_requests_routes.php');

    /**
     * Verify Email Routes
     */
    require_once('Api/User/verify_email_routes.php');

    /**
     * Checkout Routes
     */
    require_once('Api/User/checkouts_routes.php');

    /**
     * Order Routes
     */
    require_once('Api/User/orders_routes.php');

    /**
     * Search Routes
     */
    require_once('Api/User/search_routes.php');

    /**
     * Home Routes
     */
    require_once('Api/User/home_routes.php');

    /**
     * Favorite Routes
     */
    require_once('Api/User/favorites_routes.php');

    /**
     * Providers Routes
     */
    require_once('Api/User/providers_routes.php');

    /**
     * Reports Routes
     */
    require_once('Api/User/reports_routes.php');

    /**
     * Proposals Routes
     */
    require_once('Api/User/proposals_routes.php');

    /**
     * Projects Routes
     */
    require_once('Api/User/projects_routes.php');

    /**
     * Features Routes
     */
    require_once('Api/User/features_routes.php');

    /**
     * Banners Routes
     */
    require_once('Api/User/banners_routes.php');

    /**
     * FAQs Routes
     */
    require_once('Api/User/faqs_routes.php');

    /**
     * Pages Routes
     */
    require_once('Api/User/pages_routes.php');

    /**
     * FCM Test Routes
     */
    require_once('Api/Test/fcm_test_routes.php');
});


