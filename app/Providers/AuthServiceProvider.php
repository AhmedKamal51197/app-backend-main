<?php

namespace App\Providers;

use App\Models\Kyc;
use App\Models\PaymentRequest;
use App\Policies\KycPolicy;
use App\Policies\PaymentRequestPolicy;
use Carbon\Carbon;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use Laravel\Passport\Passport;

/**
 * A class defines the auth service providers
 */
class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Kyc::class => KycPolicy::class,
        PaymentRequest::class => PaymentRequestPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Passport::tokensExpireIn(now()->addDays(15));
        Passport::refreshTokensExpireIn(now()->addDays(30));
        Passport::personalAccessTokensExpireIn(now()->addMonths(6));

        // overwrite the password reset link
        ResetPassword::createUrlUsing(function ($user, string $token) {
            return config('app.frontend_url') . '/auth/reset-password?token=' . $token;
        });

        VerifyEmail::createUrlUsing(function ($notifiable) {
            $frontendUrl = sprintf('%s/email-verification', config('app.frontend_url'));

            $verifyUrl = URL::temporarySignedRoute(
                'verification.verify',
                Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
                [
                    'id' => $notifiable->getKey(),
                    'hash' => sha1($notifiable->getEmailForVerification()),
                ]
            );

            $urlParts = parse_url($verifyUrl);

            return $frontendUrl . '?' . urlencode($urlParts['query']);
        });
    }
}
