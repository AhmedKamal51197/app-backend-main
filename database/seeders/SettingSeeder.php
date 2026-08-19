<?php

namespace Database\Seeders;

use App\Enums\AttachmentDocumentTypeEnum;
use App\Enums\AttachmentFileTypeEnum;
use App\Enums\AttachmentStorageEnum;
use App\Models\Attachment;
use App\Models\Setting;
use Illuminate\Database\Seeder;

/**
 * A class defines the setting seeder
 */
class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {

        $siteLogo = Setting::updateOrCreate(
            ['setting_name' => 'site_logo'],
            ['setting_value' => '']
        );

        // Create attachment for site_logo
        $attachmentPath = 'settings/' . $siteLogo->id . '/bOugKxlbv842W0N8i8rSmAVKsHGkKQPbjNfkbutB.png';

        Attachment::updateOrCreate(
            [
                'attachable_type' => Setting::class,
                'attachable_id' => $siteLogo->id,
            ],
            [
                'user_id' => 1,
                'path' => $attachmentPath,
                'disk' => AttachmentStorageEnum::S3->value,
                'type' => AttachmentFileTypeEnum::PNG->value,
                'file_meta' => json_encode([
                    'original_name' => 'logo.png',
                    'mime_type' => 'image/png',
                    'size' => 150000,
                ]),
            ]
        );

        Setting::updateOrCreate(
            ['setting_name' => 'default_language'],
            ['setting_value' => 'en']
        );

        Setting::updateOrCreate(
            ['setting_name' => 'main_email'],
            ['setting_value' => 'info@example.com']
        );

        Setting::updateOrCreate(
            ['setting_name' => 'main_mobile'],
            ['setting_value' => '+1234567890']
        );

        Setting::updateOrCreate(
            ['setting_name' => 'main_color'],
            ['setting_value' => '#947AB6']
        );

        Setting::updateOrCreate(
            ['setting_name' => 'daily_payment_requests_amount'],
            ['setting_value' => '1000']
        );

        Setting::updateOrCreate(
            ['setting_name' => 'monthly_payment_requests_amount'],
            ['setting_value' => '30000']
        );

        Setting::updateOrCreate(
            ['setting_name' => 'minimum_payment_request_amount'],
            ['setting_value' => '100']
        );
    }
}
