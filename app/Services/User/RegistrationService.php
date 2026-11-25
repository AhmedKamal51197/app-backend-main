<?php

namespace App\Services\User;

use App\Actions\User\CreateUserAction;
use App\Models\PendingRegistration;
use App\Models\User;
use App\Notifications\VerifyEmail\VerifyEmailOtpNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use RuntimeException;

/**
 * Sign-up in two steps: the data waits in `pending_registrations` and the
 * real user is only created once the email OTP is confirmed.
 *
 * - Email already has an account: rejected (validation, before reaching here).
 * - A pending sign-up exists: its data is replaced and a new OTP is sent.
 * - The pending sign-up expired: it is replaced the same way (fresh start).
 */
class RegistrationService
{
    /**
     * Store (or replace) the pending sign-up and email a new code.
     *
     * @throws RuntimeException when a code was sent less than a minute ago
     */
    public function start(array $data): PendingRegistration
    {
        $email = mb_strtolower(trim($data['email']));
        $pending = PendingRegistration::where('email', $email)->first();

        if ($pending && !$pending->isExpired()) {
            $this->ensureCanResend($pending);
        }

        $otp = $this->newOtp();

        $pending = PendingRegistration::updateOrCreate(['email' => $email], [
            'name' => $data['name'],
            'password' => Hash::make($data['password']),
            'mobile' => $data['mobile'] ?? null,
            'country_id' => $data['country_id'],
            'role' => $data['role'],
            'fcm_token' => $data['fcm_token'] ?? null,
            'otp_hash' => Hash::make($otp),
            'otp_expires_at' => now()->addMinutes(PendingRegistration::OTP_MINUTES),
            'attempts' => 0,
            'last_sent_at' => now(),
            'expires_at' => now()->addHours(PendingRegistration::LIFETIME_HOURS),
        ]);

        $this->sendOtp($email, $otp);

        return $pending;
    }

    /**
     * Send a new code for an existing pending sign-up.
     *
     * @throws RuntimeException
     */
    public function resend(string $email): void
    {
        $pending = $this->findActive($email);
        $this->ensureCanResend($pending);

        $otp = $this->newOtp();

        $pending->update([
            'otp_hash' => Hash::make($otp),
            'otp_expires_at' => now()->addMinutes(PendingRegistration::OTP_MINUTES),
            'attempts' => 0,
            'last_sent_at' => now(),
        ]);

        $this->sendOtp($pending->email, $otp);
    }

    /**
     * Check the code; when correct create the real (already verified) user
     * and delete the pending row.
     *
     * @throws RuntimeException
     */
    public function verify(string $email, string $otp): User
    {
        $pending = $this->findActive($email);

        if ($pending->attempts >= PendingRegistration::MAX_ATTEMPTS) {
            throw new RuntimeException(__('Too many wrong attempts, please request a new code'), 429);
        }

        if ($pending->otp_expires_at->isPast()) {
            throw new RuntimeException(__('The code has expired, please request a new code'), 422);
        }

        if (!Hash::check($otp, $pending->otp_hash)) {
            $pending->increment('attempts');

            throw new RuntimeException(__('OTP is not valid'), 422);
        }

        // Someone may have finished a sign-up with this email or mobile meanwhile.
        if (User::where('email', $pending->email)->exists()) {
            $pending->delete();

            throw new RuntimeException(__('Email must be unique'), 422);
        }

        if ($pending->mobile && User::where('mobile', $pending->mobile)->exists()) {
            throw new RuntimeException(__('The mobile number is already registered'), 422);
        }

        return DB::transaction(function () use ($pending) {
            $user = CreateUserAction::create([
                'name' => $pending->name,
                'email' => $pending->email,
                'password_hash' => $pending->password,
                'country_id' => $pending->country_id,
                'mobile' => $pending->mobile,
                'role' => $pending->role,
                'fcm_token' => $pending->fcm_token,
                'email_verified_at' => now(),
            ]);

            $pending->delete();

            return $user;
        });
    }

    private function findActive(string $email): PendingRegistration
    {
        $pending = PendingRegistration::where('email', mb_strtolower(trim($email)))->first();

        if (!$pending || $pending->isExpired()) {
            throw new RuntimeException(__('No pending registration for this email, please register again'), 404);
        }

        return $pending;
    }

    private function ensureCanResend(PendingRegistration $pending): void
    {
        $wait = PendingRegistration::RESEND_SECONDS - (int) $pending->last_sent_at->diffInSeconds(now());

        if ($wait > 0) {
            throw new RuntimeException(__('Please wait :seconds seconds before requesting a new code', ['seconds' => $wait]), 429);
        }
    }

    private function newOtp(): string
    {
        return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    private function sendOtp(string $email, string $otp): void
    {
        Notification::route('mail', $email)->notify(new VerifyEmailOtpNotification($otp));
    }
}
