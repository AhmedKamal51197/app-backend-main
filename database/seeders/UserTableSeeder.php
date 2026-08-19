<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * A class defines the user table seeder
 */
class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $mayesh = User::updateOrCreate(
            ['email' => 'mayesh@vscourse.com'],
            [
                'name' => 'Mohamed Nashat Ayesh',
                'mobile_verified_at' => now(),
                'mobile' => '972567130468',
                'email_verified_at' => now(),
                'password' => bcrypt('MAyesh@123Hello'),
            ]
        );

        if (!$mayesh->hasRole('root')) {
            $mayesh->assignRole('root');
        }
    }
}
