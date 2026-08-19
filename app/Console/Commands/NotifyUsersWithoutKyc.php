<?php

namespace App\Console\Commands;

use App\Enums\KycStatusEnum;
use App\Models\Role;
use App\Models\User;
use App\Notifications\Kyc\KycNotCompletedNotification;
use Illuminate\Console\Command;

/**
 * A class defined for notify users without KYC
 */
class NotifyUsersWithoutKyc extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'notify:users-without-kyc';

    /**
     * The console command description.
     */
    protected $description = 'Send notification to users who have not completed KYC';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $providerRoleId = Role::where('name', 'provider')->value('id');

        $users = User::whereHas('roles', function ($query) use ($providerRoleId) {
            $query->where('id', $providerRoleId);
        })
            ->where(function ($query) {
                $query->whereDoesntHave('kycs') // No KYC at all
                ->orWhereDoesntHave('kycs', function ($q) {
                    $q->where('status', KycStatusEnum::APPROVED->value); // No approved KYC
                });
            })
            ->get();

        $this->info("Found {$users->count()} users without approved KYC.");

        foreach ($users as $user) {
            $user->notify(new KycNotCompletedNotification());
            $this->info("Notified: {$user->email}");
        }

        return Command::SUCCESS;
    }
}
