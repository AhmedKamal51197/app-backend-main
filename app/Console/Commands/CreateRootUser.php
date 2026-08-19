<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Enums\UserStatusEnum;
use Illuminate\Console\Command;

class CreateRootUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create-root';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new root user with predefined credentials';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $email = 'info@moawen.sa';
        $password = 'Info@123';
        $name = 'Mohamed Nashat Ayesh';

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => bcrypt($password),
                'active' => true,
                'status' => UserStatusEnum::ACTIVE->value,
                'email_verified_at' => now(),
                'mobile_verified_at' => now(),
            ]
        );

        if (!$user->hasRole('root')) {
            $user->assignRole('root');
        }

        $this->info("✅ Root user created successfully: {$email}");

        return 0;
    }
}
