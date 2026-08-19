<?php

namespace App\Console\Commands;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SeedDummyNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:seed-dummy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed two dummy notifications for all users in the system';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting to seed dummy notifications for all users...');

        $users = User::all();
        $count = 0;

        foreach ($users as $user) {
            // Notification 1
            Notification::create([
                'user_id' => $user->id,
                'title' => 'Welcome to Moawen!',
                'body' => 'Thank you for joining our platform. We are excited to have you here.',
                'seen' => false,
                'important' => false,
                'reference' => \App\Enums\NotificationReferenceEnum::SYSTEM->value,
            ]);

            // Notification 2
            Notification::create([
                'user_id' => $user->id,
                'title' => 'Profile Optimization',
                'body' => 'Make sure to complete your profile to attract more clients and opportunities.',
                'seen' => false,
                'important' => false,
                'reference' => \App\Enums\NotificationReferenceEnum::SYSTEM->value,
            ]);

            $count++;
        }

        $this->info("Successfully seeded 2 dummy notifications for {$count} users!");

        return Command::SUCCESS;
    }
}
