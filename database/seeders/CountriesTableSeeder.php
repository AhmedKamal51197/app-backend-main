<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeder to seed the countries table
 */
class CountriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('countries')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $countries = [
            ['is_enabled' => true, 'arabic_name' => 'المملكة العربية السعودية', 'iso' => 'SA', 'iso3' => 'SAU', 'english_name' => 'Saudi Arabia', 'phone_code' => '+966', 'currency' => 'Saudi Riyal', 'currency_code' => 'SAR', 'capital' => 'Riyadh'],
            ['is_enabled' => true, 'arabic_name' => 'الكويت', 'iso' => 'KW', 'iso3' => 'KWT', 'english_name' => 'Kuwait', 'phone_code' => '+965', 'currency' => 'Kuwaiti Dinar', 'currency_code' => 'KWD', 'capital' => 'Kuwait City'],
            ['is_enabled' => true, 'arabic_name' => 'قطر', 'iso' => 'QA', 'iso3' => 'QAT', 'english_name' => 'Qatar', 'phone_code' => '+974', 'currency' => 'Qatari Riyal', 'currency_code' => 'QAR', 'capital' => 'Doha'],
            ['is_enabled' => true, 'arabic_name' => 'الإمارات العربية المتحدة', 'iso' => 'AE', 'iso3' => 'ARE', 'english_name' => 'United Arab Emirates', 'phone_code' => '+971', 'currency' => 'UAE Dirham', 'currency_code' => 'AED', 'capital' => 'Abu Dhabi'],
            ['is_enabled' => true, 'arabic_name' => 'البحرين', 'iso' => 'BH', 'iso3' => 'BHR', 'english_name' => 'Bahrain', 'phone_code' => '+973', 'currency' => 'Bahraini Dinar', 'currency_code' => 'BHD', 'capital' => 'Manama'],
            ['is_enabled' => true, 'arabic_name' => 'فلسطين', 'iso' => 'PS', 'iso3' => 'PSE', 'english_name' => 'Palestinian Territory, Occupied', 'phone_code' => '+970', 'currency' => 'Israeli New Shekel', 'currency_code' => 'ILS', 'capital' => 'Jerusalem'],
//            ['is_enabled' => true, 'arabic_name' => 'اليمن', 'iso' => 'YE', 'iso3' => 'YEM', 'english_name' => 'Yemen', 'phone_code' => '+967', 'currency' => 'Yemeni Rial', 'currency_code' => 'YER', 'capital' => "Sana'a"],
            ['is_enabled' => true, 'arabic_name' => 'مصر', 'iso' => 'EG', 'iso3' => 'EGY', 'english_name' => 'Egypt', 'phone_code' => '+20', 'currency' => 'Egyptian Pound', 'currency_code' => 'EGP', 'capital' => 'Cairo'],
//            ['is_enabled' => true, 'arabic_name' => 'الجزائر', 'iso' => 'DZ', 'iso3' => 'DZA', 'english_name' => 'Algeria', 'phone_code' => '+213', 'currency' => 'Algerian Dinar', 'currency_code' => 'DZD', 'capital' => 'Algiers'],
//            ['is_enabled' => true, 'arabic_name' => 'جزر القمر', 'iso' => 'KM', 'iso3' => 'COM', 'english_name' => 'Comoros', 'phone_code' => '+269', 'currency' => 'Comorian Franc', 'currency_code' => 'KMF', 'capital' => 'Moroni'],
//            ['is_enabled' => true, 'arabic_name' => 'جيبوتي', 'iso' => 'DJ', 'iso3' => 'DJI', 'english_name' => 'Djibouti', 'phone_code' => '+253', 'currency' => 'Djiboutian Franc', 'currency_code' => 'DJF', 'capital' => 'Djibouti'],
//            ['is_enabled' => true, 'arabic_name' => 'العراق', 'iso' => 'IQ', 'iso3' => 'IRQ', 'english_name' => 'Iraq', 'phone_code' => '+964', 'currency' => 'Iraqi Dinar', 'currency_code' => 'IQD', 'capital' => 'Baghdad'],
            ['is_enabled' => true, 'arabic_name' => 'الأردن', 'iso' => 'JO', 'iso3' => 'JOR', 'english_name' => 'Jordan', 'phone_code' => '+962', 'currency' => 'Jordanian Dinar', 'currency_code' => 'JOD', 'capital' => 'Amman'],
            ['is_enabled' => true, 'arabic_name' => 'لبنان', 'iso' => 'LB', 'iso3' => 'LBN', 'english_name' => 'Lebanon', 'phone_code' => '+961', 'currency' => 'Lebanese Pound', 'currency_code' => 'LBP', 'capital' => 'Beirut'],
//            ['is_enabled' => true, 'arabic_name' => 'ليبيا', 'iso' => 'LY', 'iso3' => 'LBY', 'english_name' => 'Libya', 'phone_code' => '+218', 'currency' => 'Libyan Dinar', 'currency_code' => 'LYD', 'capital' => 'Tripoli'],
//            ['is_enabled' => true, 'arabic_name' => 'موريتانيا', 'iso' => 'MR', 'iso3' => 'MRT', 'english_name' => 'Mauritania', 'phone_code' => '+222', 'currency' => 'Ouguiya', 'currency_code' => 'MRU', 'capital' => 'Nouakchott'],
            ['is_enabled' => true, 'arabic_name' => 'المغرب', 'iso' => 'MA', 'iso3' => 'MAR', 'english_name' => 'Morocco', 'phone_code' => '+212', 'currency' => 'Moroccan Dirham', 'currency_code' => 'MAD', 'capital' => 'Rabat'],
            ['is_enabled' => true, 'arabic_name' => 'عمان', 'iso' => 'OM', 'iso3' => 'OMN', 'english_name' => 'Oman', 'phone_code' => '+968', 'currency' => 'Omani Rial', 'currency_code' => 'OMR', 'capital' => 'Muscat'],
//            ['is_enabled' => true, 'arabic_name' => 'الصومال', 'iso' => 'SO', 'iso3' => 'SOM', 'english_name' => 'Somalia', 'phone_code' => '+252', 'currency' => 'Somali Shilling', 'currency_code' => 'SOS', 'capital' => 'Mogadishu'],
            ['is_enabled' => true, 'arabic_name' => 'السودان', 'iso' => 'SD', 'iso3' => 'SDN', 'english_name' => 'Sudan', 'phone_code' => '+249', 'currency' => 'Sudanese Pound', 'currency_code' => 'SDG', 'capital' => 'Khartoum'],
//            ['is_enabled' => true, 'arabic_name' => 'سوريا', 'iso' => 'SY', 'iso3' => 'SYR', 'english_name' => 'Syria', 'phone_code' => '+963', 'currency' => 'Syrian Pound', 'currency_code' => 'SYP', 'capital' => 'Damascus'],
            ['is_enabled' => true, 'arabic_name' => 'تونس', 'iso' => 'TN', 'iso3' => 'TUN', 'english_name' => 'Tunisia', 'phone_code' => '+216', 'currency' => 'Tunisian Dinar', 'currency_code' => 'TND', 'capital' => 'Tunis'],
            ['is_enabled' => true, 'arabic_name' => 'ألمانيا', 'iso' => 'DE', 'iso3' => 'DEU', 'english_name' => 'Germany', 'phone_code' => '+49', 'currency' => 'Euro', 'currency_code' => 'EUR', 'capital' => 'Berlin'],
            ['is_enabled' => true, 'arabic_name' => 'فرنسا', 'iso' => 'FR', 'iso3' => 'FRA', 'english_name' => 'France', 'phone_code' => '+33', 'currency' => 'Euro', 'currency_code' => 'EUR', 'capital' => 'Paris'],
            ['is_enabled' => true, 'arabic_name' => 'إسبانيا', 'iso' => 'ES', 'iso3' => 'ESP', 'english_name' => 'Spain', 'phone_code' => '+34', 'currency' => 'Euro', 'currency_code' => 'EUR', 'capital' => 'Madrid'],
            ['is_enabled' => true, 'arabic_name' => 'المملكة المتحدة', 'iso' => 'GB', 'iso3' => 'GBR', 'english_name' => 'United Kingdom', 'phone_code' => '+44', 'currency' => 'Pound Sterling', 'currency_code' => 'GBP', 'capital' => 'London'],
            ['is_enabled' => true, 'arabic_name' => 'روسيا', 'iso' => 'RU', 'iso3' => 'RUS', 'english_name' => 'Russia', 'phone_code' => '+7', 'currency' => 'Russian Ruble', 'currency_code' => 'RUB', 'capital' => 'Moscow'],
            ['is_enabled' => true, 'arabic_name' => 'كندا', 'iso' => 'CA', 'iso3' => 'CAN', 'english_name' => 'Canada', 'phone_code' => '+1', 'currency' => 'Canadian Dollar', 'currency_code' => 'CAD', 'capital' => 'Ottawa'],
            ['is_enabled' => true, 'arabic_name' => 'أستراليا', 'iso' => 'AU', 'iso3' => 'AUS', 'english_name' => 'Australia', 'phone_code' => '+61', 'currency' => 'Australian Dollar', 'currency_code' => 'AUD', 'capital' => 'Canberra'],
            ['is_enabled' => true, 'arabic_name' => 'البرازيل', 'iso' => 'BR', 'iso3' => 'BRA', 'english_name' => 'Brazil', 'phone_code' => '+55', 'currency' => 'Brazilian Real', 'currency_code' => 'BRL', 'capital' => 'Brasília'],
            ['is_enabled' => true, 'arabic_name' => 'الأرجنتين', 'iso' => 'AR', 'iso3' => 'ARG', 'english_name' => 'Argentina', 'phone_code' => '+54', 'currency' => 'Argentine Peso', 'currency_code' => 'ARS', 'capital' => 'Buenos Aires'],
            ['is_enabled' => true, 'arabic_name' => 'الهند', 'iso' => 'IN', 'iso3' => 'IND', 'english_name' => 'India', 'phone_code' => '+91', 'currency' => 'Indian Rupee', 'currency_code' => 'INR', 'capital' => 'New Delhi'],
            ['is_enabled' => true, 'arabic_name' => 'باكستان', 'iso' => 'PK', 'iso3' => 'PAK', 'english_name' => 'Pakistan', 'phone_code' => '+92', 'currency' => 'Pakistani Rupee', 'currency_code' => 'PKR', 'capital' => 'Islamabad'],
            ['is_enabled' => true, 'arabic_name' => 'كوريا الجنوبية', 'iso' => 'KR', 'iso3' => 'KOR', 'english_name' => 'South Korea', 'phone_code' => '+82', 'currency' => 'South Korean Won', 'currency_code' => 'KRW', 'capital' => 'Seoul'],
            ['is_enabled' => true, 'arabic_name' => 'إندونيسيا', 'iso' => 'ID', 'iso3' => 'IDN', 'english_name' => 'Indonesia', 'phone_code' => '+62', 'currency' => 'Indonesian Rupiah', 'currency_code' => 'IDR', 'capital' => 'Jakarta'],
//            ['is_enabled' => true, 'arabic_name' => 'جنوب أفريقيا', 'iso' => 'ZA', 'iso3' => 'ZAF', 'english_name' => 'South Africa', 'phone_code' => '+27', 'currency' => 'South African Rand', 'currency_code' => 'ZAR', 'capital' => 'Pretoria'],
//            ['is_enabled' => true, 'arabic_name' => 'نيجيريا', 'iso' => 'NG', 'iso3' => 'NGA', 'english_name' => 'Nigeria', 'phone_code' => '+234', 'currency' => 'Naira', 'currency_code' => 'NGN', 'capital' => 'Abuja'],
            ['is_enabled' => true, 'arabic_name' => 'المكسيك', 'iso' => 'MX', 'iso3' => 'MEX', 'english_name' => 'Mexico', 'phone_code' => '+52', 'currency' => 'Mexican Peso', 'currency_code' => 'MXN', 'capital' => 'Mexico City'],
            ['is_enabled' => true, 'arabic_name' => 'تركيا', 'iso' => 'TR', 'iso3' => 'TUR', 'english_name' => 'Turkey', 'phone_code' => '+90', 'currency' => 'Turkish Lira', 'currency_code' => 'TRY', 'capital' => 'Ankara'],
            ['is_enabled' => true, 'arabic_name' => 'السويد', 'iso' => 'SE', 'iso3' => 'SWE', 'english_name' => 'Sweden', 'phone_code' => '+46', 'currency' => 'Swedish Krona', 'currency_code' => 'SEK', 'capital' => 'Stockholm'],
            ['is_enabled' => true, 'arabic_name' => 'النرويج', 'iso' => 'NO', 'iso3' => 'NOR', 'english_name' => 'Norway', 'phone_code' => '+47', 'currency' => 'Norwegian Krone', 'currency_code' => 'NOK', 'capital' => 'Oslo'],
            ['is_enabled' => true, 'arabic_name' => 'سويسرا', 'iso' => 'CH', 'iso3' => 'CHE', 'english_name' => 'Switzerland', 'phone_code' => '+41', 'currency' => 'Swiss Franc', 'currency_code' => 'CHF', 'capital' => 'Bern'],
            ['is_enabled' => true, 'arabic_name' => 'النمسا', 'iso' => 'AT', 'iso3' => 'AUT', 'english_name' => 'Austria', 'phone_code' => '+43', 'currency' => 'Euro', 'currency_code' => 'EUR', 'capital' => 'Vienna'],
            ['is_enabled' => true, 'arabic_name' => 'اليونان', 'iso' => 'GR', 'iso3' => 'GRC', 'english_name' => 'Greece', 'phone_code' => '+30', 'currency' => 'Euro', 'currency_code' => 'EUR', 'capital' => 'Athens'],
            ['is_enabled' => true, 'arabic_name' => 'بولندا', 'iso' => 'PL', 'iso3' => 'POL', 'english_name' => 'Poland', 'phone_code' => '+48', 'currency' => 'Polish Złoty', 'currency_code' => 'PLN', 'capital' => 'Warsaw'],
            ['is_enabled' => true, 'arabic_name' => 'البرتغال', 'iso' => 'PT', 'iso3' => 'PRT', 'english_name' => 'Portugal', 'phone_code' => '+351', 'currency' => 'Euro', 'currency_code' => 'EUR', 'capital' => 'Lisbon'],
//            ['is_enabled' => true, 'arabic_name' => 'تشيلي', 'iso' => 'CL', 'iso3' => 'CHL', 'english_name' => 'Chile', 'phone_code' => '+56', 'currency' => 'Chilean Peso', 'currency_code' => 'CLP', 'capital' => 'Santiago'],
//            ['is_enabled' => true, 'arabic_name' => 'كولومبيا', 'iso' => 'CO', 'iso3' => 'COL', 'english_name' => 'Colombia', 'phone_code' => '+57', 'currency' => 'Colombian Peso', 'currency_code' => 'COP', 'capital' => 'Bogotá'],
//            ['is_enabled' => true, 'arabic_name' => 'إيران', 'iso' => 'IR', 'iso3' => 'IRN', 'english_name' => 'Iran', 'phone_code' => '+98', 'currency' => 'Iranian Rial', 'currency_code' => 'IRR', 'capital' => 'Tehran'],
            ['is_enabled' => true, 'arabic_name' => 'تايلاند', 'iso' => 'TH', 'iso3' => 'THA', 'english_name' => 'Thailand', 'phone_code' => '+66', 'currency' => 'Baht', 'currency_code' => 'THB', 'capital' => 'Bangkok'],
            ['is_enabled' => true, 'arabic_name' => 'ماليزيا', 'iso' => 'MY', 'iso3' => 'MYS', 'english_name' => 'Malaysia', 'phone_code' => '+60', 'currency' => 'Ringgit', 'currency_code' => 'MYR', 'capital' => 'Kuala Lumpur'],
//            ['is_enabled' => true, 'arabic_name' => 'فيتنام', 'iso' => 'VN', 'iso3' => 'VNM', 'english_name' => 'Vietnam', 'phone_code' => '+84', 'currency' => 'Dong', 'currency_code' => 'VND', 'capital' => 'Hanoi'],
            ['is_enabled' => true, 'arabic_name' => 'سنغافورة', 'iso' => 'SG', 'iso3' => 'SGP', 'english_name' => 'Singapore', 'phone_code' => '+65', 'currency' => 'Singapore Dollar', 'currency_code' => 'SGD', 'capital' => 'Singapore'],
            ['is_enabled' => true, 'arabic_name' => 'الولايات المتحدة الأمريكية', 'iso' => 'US', 'iso3' => 'USA', 'english_name' => 'United States of America', 'phone_code' => '+1', 'currency' => 'US Dollar', 'currency_code' => 'USD', 'capital' => 'Washington, D.C.'],
            ['is_enabled' => true, 'arabic_name' => 'اليابان', 'iso' => 'JP', 'iso3' => 'JPN', 'english_name' => 'Japan', 'phone_code' => '+81', 'currency' => 'Japanese Yen', 'currency_code' => 'JPY', 'capital' => 'Tokyo'],
            ['is_enabled' => true, 'arabic_name' => 'الصين', 'iso' => 'CN', 'iso3' => 'CHN', 'english_name' => 'China', 'phone_code' => '+86', 'currency' => 'Renminbi', 'currency_code' => 'CNY', 'capital' => 'Beijing'],
            ['is_enabled' => true, 'arabic_name' => 'إيطاليا', 'iso' => 'IT', 'iso3' => 'ITA', 'english_name' => 'Italy', 'phone_code' => '+39', 'currency' => 'Euro', 'currency_code' => 'EUR', 'capital' => 'Rome'],
        ];

        foreach ($countries as $country) {
            Country::updateOrCreate(
                ['iso' => $country['iso']],
                $country
            );
        }
    }
}
