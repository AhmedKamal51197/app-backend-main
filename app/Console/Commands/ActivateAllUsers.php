<?php

namespace App\Console\Commands;

use App\Enums\UserStatusEnum;
use App\Models\User;
use Illuminate\Console\Command;

/**
 * Adding activate all users
 */
class ActivateAllUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Change this to something meaningful.
     */
    protected $signature = 'users:activate-all';

    /**
     * The console command description.
     */
    protected $description = 'Set status to active for all users';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        User::query()->update(['status' => UserStatusEnum::ACTIVE->value]);
        User::query()->update(['active' => true]);
        $this->info('✅ All users have been activated.');
    }
}
