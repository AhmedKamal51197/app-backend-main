<?php

namespace App\Console\Commands;

use App\Models\Role;
use Illuminate\Console\Command;

class ActivateAllRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'roles:activate-all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Activate all system roles that are currently disabled';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $count = Role::where('is_active', false)->count();

        if ($count === 0) {
            $this->info('No disabled roles found. All roles are already active.');
            return 0;
        }

        Role::where('is_active', false)->update(['is_active' => true]);

        $this->info("Successfully activated {$count} roles.");

        return 0;
    }
}
