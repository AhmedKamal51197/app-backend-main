<?php

namespace App\Services\Setting;

use App\Enums\SettingsEnum;
use App\Http\Resources\Api\Attachment\AttachmentResource;
use App\Models\Setting;
use Illuminate\Support\Collection;

/**
 * Settings that are safe to show publicly (website contact details, colors,
 * social and store links). Built from an explicit whitelist: nothing else in
 * the settings table is ever returned, even if added later.
 */
class PublicSettingService
{
    /** Placeholder values from the initial seed that must never reach visitors. */
    private const PLACEHOLDER_EMAILS = ['info@example.com'];
    private const PLACEHOLDER_PHONES = ['1234567890', '+1234567890'];

    public function get(): array
    {
        $keys = [
            'main_email', 'main_mobile', 'main_color', 'default_language', SettingsEnum::SITE_LOGO->value,
            'whatsapp', 'social_instagram', 'social_x', 'social_tiktok', 'app_ios_url', 'app_android_url',
        ];

        /** @var Collection<string, Setting> $settings */
        $settings = Setting::query()
            ->whereIn('setting_name', $keys)
            ->with('attachment')
            ->get()
            ->keyBy('setting_name');

        $value = fn (string $key): ?string => $this->clean($settings->get($key)?->setting_value);
        $logo = $settings->get(SettingsEnum::SITE_LOGO->value)?->attachment;

        return [
            'email' => $this->email($value('main_email')),
            'phone' => $this->phone($value('main_mobile')),
            'whatsapp' => $this->phone($value('whatsapp')),
            'default_language' => in_array($value('default_language'), ['ar', 'en'], true) ? $value('default_language') : null,
            'primary_color' => preg_match('/^#[0-9a-fA-F]{6}$/', (string) $value('main_color')) ? $value('main_color') : null,
            'logo_url' => $logo ? (AttachmentResource::make($logo)->resolve()['url'] ?? null) : null,
            'social' => [
                'instagram' => $this->url($value('social_instagram')),
                'x' => $this->url($value('social_x')),
                'tiktok' => $this->url($value('social_tiktok')),
            ],
            'apps' => [
                'ios_url' => $this->url($value('app_ios_url')),
                'android_url' => $this->url($value('app_android_url')),
            ],
            'updated_at' => $settings->max('updated_at')?->toIso8601String(),
        ];
    }

    private function clean(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function email(?string $email): ?string
    {
        if (!$email || in_array(strtolower($email), self::PLACEHOLDER_EMAILS, true)) {
            return null;
        }

        return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : null;
    }

    /**
     * Normalise a phone number to E.164 (+9665XXXXXXXX) so tel: and WhatsApp
     * links work. Saudi local numbers (05XXXXXXXX) get the +966 prefix.
     */
    private function phone(?string $phone): ?string
    {
        if (!$phone || in_array(preg_replace('/\s+/', '', $phone), self::PLACEHOLDER_PHONES, true)) {
            return null;
        }

        $digits = preg_replace('/[^\d+]/', '', $phone);

        if (str_starts_with($digits, '00')) {
            $digits = '+' . substr($digits, 2);
        } elseif (preg_match('/^05\d{8}$/', $digits)) {
            $digits = '+966' . substr($digits, 1);
        } elseif (preg_match('/^9665\d{8}$/', $digits)) {
            $digits = '+' . $digits;
        }

        return preg_match('/^\+[1-9]\d{7,14}$/', $digits) ? $digits : null;
    }

    private function url(?string $url): ?string
    {
        return $url && filter_var($url, FILTER_VALIDATE_URL) && str_starts_with($url, 'https://') ? $url : null;
    }
}
