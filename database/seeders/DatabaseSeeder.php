<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * A class defines the database seeder
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(CountriesTableSeeder::class);
        $this->call(RolesAndPermissionsSeeder::class);
        $this->call(UserTableSeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(SkillSeeder::class);
        $this->call(CertificateDataSeeder::class);
        $this->call(EducationMajorsSeeder::class);
        $this->call(EducationDegreesSeeder::class);
        $this->call(IndustrySeeder::class);
        $this->call(DurationSeeder::class);
        $this->call(FeatureSeeder::class);
        $this->call(CommissionSettingSeeder::class);
        $this->call(DummyDataSeeder::class);
        $this->call(SettingSeeder::class);
        $this->call(BannerSeeder::class);
        $this->call(FaqSeeder::class);
        $this->call(ColorSeeder::class);
        $this->call(PageSeeder::class);
        $this->call(NotificationSettingSeeder::class);
    }
}
