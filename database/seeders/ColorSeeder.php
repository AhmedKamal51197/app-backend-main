<?php

namespace Database\Seeders;

use App\Models\Color;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $colors = [
            [
                'name_en' => 'Primary Purple',
                'name_ar' => 'البنفسجي الأساسي',
                'hex_code' => '#947AB6',
                'key' => 'primary_purple',
                'is_active' => true,
            ],
            [
                'name_en' => 'Coral Red',
                'name_ar' => 'أحمر مرجاني',
                'hex_code' => '#FF5C77',
                'key' => 'coral_red',
                'is_active' => true,
            ],
            [
                'name_en' => 'Success Green',
                'name_ar' => 'أخضر النجاح',
                'hex_code' => '#00C950',
                'key' => 'success_green',
                'is_active' => true,
            ],
            [
                'name_en' => 'Orange',
                'name_ar' => 'برتقالي',
                'hex_code' => '#F87D1D',
                'key' => 'orange',
                'is_active' => true,
            ],
            [
                'name_en' => 'Sky Blue',
                'name_ar' => 'أزرق سماوي',
                'hex_code' => '#1B90FF',
                'key' => 'sky_blue',
                'is_active' => true,
            ],
            [
                'name_en' => 'Magenta',
                'name_ar' => 'أرجواني',
                'hex_code' => '#F31DED',
                'key' => 'magenta',
                'is_active' => true,
            ],
        ];

        foreach ($colors as $color) {
            Color::updateOrCreate(
                ['key' => $color['key']],
                array_merge($color, ['uuid' => Str::uuid()])
            );
        }
    }
}