<?php

namespace Database\Seeders;

use App\Models\Duration;
use Illuminate\Database\Seeder;

/**
 * A class defined for duration seeder
 */
class DurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $durations = [
            ['en' => 'Less than month', 'ar' => 'أقل من شهر'],
            ['en' => '1 Month', 'ar' => 'شهر'],
            ['en' => '2 Month', 'ar' => 'شهرين'],
            ['en' => '3 Month', 'ar' => '٣ شهور'],
            ['en' => '4 Month', 'ar' => '٤ شهور'],
            ['en' => '5 Month', 'ar' => '٥ شهور'],
            ['en' => '6 Month', 'ar' => '٦ شهور'],
            ['en' => 'More than 6 months', 'ar' => 'أكثر من ٦ شهور'],
        ];

        foreach ($durations as $duration) {
            Duration::updateOrCreate(
                ['title_en' => $duration['en']],
                ['title_ar' => $duration['ar']]
            );
        }
    }
}
