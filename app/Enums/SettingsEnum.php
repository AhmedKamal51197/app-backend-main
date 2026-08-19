<?php

namespace App\Enums;

enum SettingsEnum: string
{
    case SMS_EXPIRED_AT_HOURS = 'sms_expired_at_hours';
    case SITE_LOGO = 'site_logo';
    case PAYMENT_GATEWAY = 'payment_gateway';
    case DAILY_PAYMENT_REQUESTS_AMOUNT = 'daily_payment_requests_amount';
    case MONTHLY_PAYMENT_REQUESTS_AMOUNT = 'monthly_payment_requests_amount';
    case MINIMUM_PAYMENT_REQUEST_AMOUNT = 'minimum_payment_request_amount';
}
