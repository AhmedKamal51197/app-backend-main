<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Service;
use Illuminate\Support\Facades\DB;

class CleanPartTimePackages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:clean-part-time-packages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Removes extra packages for part-time services, keeping only the first package';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Cleaning part-time services packages...');

        $services = Service::where('type', 'part_time')
            ->with('packages')
            ->get();

        DB::beginTransaction();
        try {
            foreach ($services as $service) {
                $packages = $service->packages->sortBy('id');

                if ($packages->count() > 1) {
                    $keep = $packages->first();
                    $toDelete = $packages->slice(1)->pluck('id');

                    $service->packages()
                        ->whereIn('id', $toDelete)
                        ->delete();

                    $this->info("Cleaned service ID {$service->id} → kept package {$keep->id}, deleted others.");
                }else{
                    $package = $packages->first();
                    $package->days = 1;
                    $package->save();
                }
            }

            DB::commit();
            $this->info('All part-time services cleaned successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Error: ' . $e->getMessage());
        }

        return Command::SUCCESS;
    }
}
