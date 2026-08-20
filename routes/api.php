<?php

use Illuminate\Support\Facades\Route;

/**
 * API version 1.0
 */
Route::prefix('v1')->group(function () {
    /**
     * KYC Routes
     */
    require('Api/User/kyc_routes.php');

    /**
     * User Routes
     */
    require('Api/User/user_routes.php');

    /**
     * Country Routes
     */
    require('Api/User/countries_routes.php');

    /**
     * Category Routes
     */
    require('Api/User/categories_routes.php');

    /**
     * Sub Category Routes
     */
    require('Api/User/sub_categories_routes.php');

    /**
     * Skills Routes
     */
    require('Api/User/skills_routes.php');

    /**
     *  Wallet Routes
     */
    require('Api/User/wallet_routes.php');

    /**
     * Certificate providers Routes
     */
    require('Api/User/certificate_providers_routes.php');

    /**
     * Bank account Routes
     */
    require('Api/User/bank_account_routes.php');

    /**
     * PayPal Routes
     */
    require('Api/User/paypal_routes.php');

    /**
     *
     * Educations Routes
     */
    require('Api/User/educations_routes.php');

    /**
     * Experiences Routes
     */
    require('Api/User/experiences_routes.php');

    /**
     * Portfolios Routes
     */
    require('Api/User/portfolios_routes.php');

    /**
     * Services Routes
     */
    require('Api/User/services_routes.php');

    /**
     * Jobs Routes
     */
    require('Api/User/jobs_routes.php');

    /**
     * Chats Routes
     */
    require('Api/User/chats_routes.php');

    /**
     * Payment Requests Routes
     */
    require('Api/User/payment_requests_routes.php');

    /**
     * Verify Email Routes
     */
    require('Api/User/verify_email_routes.php');

    /**
     * Checkout Routes
     */
    require('Api/User/checkouts_routes.php');

    /**
     * Order Routes
     */
    require('Api/User/orders_routes.php');

    /**
     * Search Routes
     */
    require('Api/User/search_routes.php');

    /**
     * Home Routes
     */
    require('Api/User/home_routes.php');

    /**
     * Favorite Routes
     */
    require('Api/User/favorites_routes.php');

    /**
     * Providers Routes
     */
    require('Api/User/providers_routes.php');

    /**
     * Reports Routes
     */
    require('Api/User/reports_routes.php');

    /**
     * Proposals Routes
     */
    require('Api/User/proposals_routes.php');

    /**
     * Projects Routes
     */
    require('Api/User/projects_routes.php');

    /**
     * Features Routes
     */
    require('Api/User/features_routes.php');

    /**
     * Banners Routes
     */
    require('Api/User/banners_routes.php');

    /**
     * FAQs Routes
     */
    require('Api/User/faqs_routes.php');

    /**
     * Pages Routes
     */
    require('Api/User/pages_routes.php');

    /**
     * FCM Test Routes
     */
    require('Api/Test/fcm_test_routes.php');
});


